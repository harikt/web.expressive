<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Document;

use Dms\Common\Structure\DateTime\DateTime;
use Dms\Common\Structure\Field;
use Dms\Common\Structure\FileSystem\File;
use Dms\Common\Structure\FileSystem\PathHelper;
use Dms\Common\Structure\FileSystem\RelativePathCalculator;
use Dms\Core\Auth\IAuthSystem;
use Dms\Core\Common\Crud\CrudModule;
use Dms\Core\Common\Crud\Definition\CrudModuleDefinition;
use Dms\Core\Common\Crud\Definition\Form\CrudFormDefinition;
use Dms\Core\Common\Crud\Definition\Table\SummaryTableDefinition;
use Dms\Core\Common\Crud\ICrudModule;
use Dms\Core\Exception\NotImplementedException;
use Dms\Core\File\IFile;
use Dms\Core\File\IUploadedFile;
use Dms\Core\Form\Builder\Form;
use Dms\Core\Form\Field\Builder\FieldBuilderBase;
use Dms\Core\Model\IMutableObjectSet;
use Dms\Core\Model\Object\ArrayDataObject;
use Dms\Core\Model\Type\Builder\Type;
use Dms\Core\Model\ValueObjectCollection;
use Dms\Web\Expressive\Util\FileSizeFormatter;

/**
 * The public file module.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class PublicFileModule extends CrudModule
{
    const ROOT_PATH = '.' . DIRECTORY_SEPARATOR;
    const ROOT_NAME = 'home';

    /**
     * @var string
     */
    protected $rootDirectory;

    /**
     * @var string
     */
    protected $trashDirectory;

    /**
     * @var RelativePathCalculator
     */
    protected $relativePathCalculator;

    /**
     * @var DirectoryTree
     */
    protected $directoryTree;

    /**
     * @var DirectoryTree
     */
    protected $trashDirectoryTree;

    /**
     * @var ValueObjectCollection
     */
    protected $trashDataSource;

    public function __construct(DirectoryTree $directory, DirectoryTree $trashDirectory, IAuthSystem $authSystem)
    {
        $this->rootDirectory          = $directory->directory->getFullPath();
        $this->directoryTree          = $directory;
        $this->trashDirectory         = $trashDirectory->directory->getFullPath();
        $this->trashDirectoryTree     = $trashDirectory;
        $this->trashDataSource        = $this->trashDirectoryTree->getAllFiles();
        $this->relativePathCalculator = new RelativePathCalculator();

        parent::__construct($this->directoryTree->getAllFiles(), $authSystem);
    }

    /**
     * @return DirectoryTree
     */
    public function getDirectoryTree() : DirectoryTree
    {
        return $this->directoryTree;
    }

    /**
     * @return string
     */
    public function getRootDirectory() : string
    {
        return $this->rootDirectory;
    }

    /**
     * @return string
     */
    public function getTrashDirectory() : string
    {
        return $this->trashDirectory;
    }

    /**
     * @return DirectoryTree
     */
    public function getTrashDirectoryTree() : DirectoryTree
    {
        return $this->trashDirectoryTree;
    }

    /**
     * @return ValueObjectCollection
     */
    public function getTrashDataSource() : ValueObjectCollection
    {
        return $this->trashDataSource;
    }

    protected function getRelativePath(string $path) : string
    {
        return $this->relativePathCalculator->getRelativePath($this->rootDirectory, $path);
    }

    /**
     * Defines the structure of this module.
     *
     * @param CrudModuleDefinition $module
     */
    protected function defineCrudModule(CrudModuleDefinition $module)
    {
        $module->name('files');

        $module->metadata(
            [
            'icon' => 'hdd-o'
            ]
        );

        $module->labelObjects()->fromCallback(
            function (File $file) {
                return $this->relativePathCalculator->getRelativePath($this->rootDirectory, $file->getFullPath());
            }
        );

        $module->action('upload-files')
            ->authorizeAll([self::VIEW_PERMISSION, self::EDIT_PERMISSION])
            ->form(
                Form::create()->section(
                    'Upload Files',
                    [
                    $this->folderField('folder', 'Folder')->value(self::ROOT_PATH)->required(),
                    Field::create('files', 'Files')->arrayOf(
                        Field::element()->file()->required()
                    )->required(),
                    ]
                )
            )
            ->handler(
                function (ArrayDataObject $input) {
                    foreach ($input['files'] as $file) {
                        /**
                         * @var IUploadedFile $file
                         */
                        $fullPath = PathHelper::combine($this->rootDirectory, $input['folder'], $file->getClientFileNameWithFallback());
                        $file->moveTo($this->getNonConflictingFileName($fullPath));
                    }
                }
            );

        $module->action('empty-trash')
            ->authorizeAll([self::VIEW_PERMISSION, self::EDIT_PERMISSION])
            ->handler(
                function () {
                    self::deleteDirectory($this->trashDirectoryTree, $deleteFolder = false);
                }
            );

        $module->action('restore-file')
            ->authorizeAll([self::VIEW_PERMISSION, self::EDIT_PERMISSION])
            ->form(
                Form::create()->section(
                    'File',
                    [
                    Field::create('file', 'File')->objectFromIndex($this->trashDataSource)->required(),
                    ]
                )
            )
            ->handler(
                function (ArrayDataObject $input) {
                    /**
                     * @var IFile $file
                     */
                    $file         = $input['file'];
                    $relativePath = $this->relativePathCalculator->getRelativePath($this->trashDirectory, $file->getFullPath());
                    $file->moveTo(PathHelper::combine($this->rootDirectory, $relativePath));
                }
            );

        $module->action('create-folder')
            ->authorizeAll([self::VIEW_PERMISSION, self::EDIT_PERMISSION])
            ->form(
                Form::create()->section(
                    'Create Folder',
                    [
                    $this->folderField('folder', 'Folder'),
                    ]
                )
            )
            ->handler(
                function (ArrayDataObject $input) {
                    @mkdir(PathHelper::combine($this->rootDirectory, $input['folder']), 0644, true);
                }
            );

        $module->crudForm(
            function (CrudFormDefinition $form) {
                $form->dependentOnObject(
                    function (CrudFormDefinition $form, File $file = null) {
                        $directoryPath = $file
                        ? $this->getRelativePath($file->getDirectory()->getFullPath())
                        : null;

                        $form->section(
                            'Details',
                            [
                            $form->field(
                                $this->folderField('folder', 'Folder')
                                    ->value($directoryPath)
                            )->withoutBinding(),
                            ]
                        );
                    },
                    ['folder']
                );

                $form->dependentOn(
                    ['folder'],
                    function (CrudFormDefinition $form, array $input, File $file = null) {
                        $directoryPath = PathHelper::combine($this->rootDirectory, $input['folder']);

                        $fileUploadField = Field::create('file', 'File')->file()->required()
                        ->moveToPathWithClientsFileName($directoryPath)
                        ->value($file);
                        $form->section(
                            'File',
                            [
                            $form->field(
                                $form->isEditForm()
                                ? $fileUploadField->readonly()
                                : $fileUploadField
                            )->withoutBinding(),
                            ]
                        );
                    }
                );

                $form->dependentOnObject(
                    function (CrudFormDefinition $form, File $file) {
                        $form->section(
                            'Details',
                            [
                            $form->field(
                                Field::create('created_at', 'Created At')->dateTime()->readonly()->value(
                                    new DateTime(new \DateTimeImmutable('@' . $file->getInfo()->getCTime()))
                                )
                            )->withoutBinding(),
                            $form->field(
                                Field::create('modified_at', 'Modified At')->dateTime()->readonly()->value(
                                    new DateTime(new \DateTimeImmutable('@' . $file->getInfo()->getMTime()))
                                )
                            )->withoutBinding(),
                            ]
                        );
                    }
                );

                $form->createObjectType()->fromCallback(
                    function (array $input) : File {
                        return $input['file'];
                    }
                );


                $form->onSave(
                    function (File $file, array $input) use ($form) {
                        if ($form->isEditForm()) {
                            $fullPath = PathHelper::combine($this->rootDirectory, $input['folder'], $file->getName());

                            if ($file->getFullPath() !== $fullPath) {
                                $file->moveTo($fullPath);
                            }
                        }
                    }
                );
            }
        );

        $module->objectAction('download')
            ->authorize(self::VIEW_PERMISSION)
            ->returns(File::class)
            ->handler(
                function (File $file) : File {
                    return $file;
                }
            );

        $module->removeAction()->handler(
            function (File $file) {
                $file->moveTo(PathHelper::combine($this->trashDirectory, $this->getRelativePath($file->getFullPath())));
            }
        );

        $module->summaryTable(
            function (SummaryTableDefinition $table) {
                $table->mapCallback(
                    function (File $file) {
                        return $file;
                    }
                )->to(Field::create('preview', 'Preview')->file());

                $table->mapProperty(File::CLIENT_FILE_NAME)->to(Field::create('name', 'Name')->string());

                $table->mapCallback(
                    function (File $file) {
                        return FileSizeFormatter::formatBytes($file->getSize());
                    }
                )->to(Field::create('size', 'File Size')->string());

                $table->view('all', 'All')
                    ->asDefault()
                    ->loadAll();
            }
        );
    }

    protected function folderField($name, $label) : FieldBuilderBase
    {
        return Field::create($name, $label)->string()->required()
            ->autocomplete($this->getAllDirectoryOptions())
            ->onlyContainsCharacterRanges(
                [
                'a'                 => 'z',
                'A'                 => 'Z',
                '0'                 => '9',
                '_'                 => '_',
                '-'                 => '-',
                DIRECTORY_SEPARATOR => DIRECTORY_SEPARATOR,
                ]
            )
            ->map(
                function (string $i) {
                    return $i === self::ROOT_NAME ? self::ROOT_PATH : $i;
                },
                function (string $i) {
                    return $i === self::ROOT_PATH ? self::ROOT_NAME : $i;
                },
                Type::string()
            );
    }

    private function getAllDirectoryOptions() : array
    {
        $options = [self::ROOT_NAME];

        foreach ($this->directoryTree->getAllDirectories() as $directory) {
            $options[] = $this->getRelativePath($directory->getFullPath());
        }

        return $options;
    }

    protected static function deleteDirectory(DirectoryTree $directoryTree, bool $deleteFolder = true)
    {
        $fullPath = $directoryTree->directory->getFullPath();

        foreach ($directoryTree->subDirectories as $subDirectory) {
            self::deleteDirectory($subDirectory);
        }

        foreach ($directoryTree->files as $file) {
            unlink($file->getFullPath());
        }

        if ($deleteFolder) {
            rmdir($fullPath);
        }
    }

    protected function loadCrudModuleWithDataSource(IMutableObjectSet $dataSource) : ICrudModule
    {
        throw NotImplementedException::method(__METHOD__);
    }

    private function getNonConflictingFileName(string $fullPath) : string
    {
        $file = new File($fullPath);

        $i            = 1;
        $extension    = $file->getExtension();
        $originalName = substr($file->getName(), 0, -strlen('.' . $extension));
        while ($file->exists()) {
            $file = new File(PathHelper::combine($file->getDirectory()->getFullPath(), $originalName . '-' . $i . '.' . $extension));
            $i++;
        }

        return $file->getFullPath();
    }
}
