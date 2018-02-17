<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Renderer\Form\Field;

use Dms\Common\Structure\FileSystem\Form\FileUploadType;
use Dms\Common\Structure\FileSystem\Form\ImageUploadType;
use Dms\Common\Structure\FileSystem\PathHelper;
use Dms\Core\File\IFile;
use Dms\Core\Form\Field\Type\FieldType;
use Dms\Core\Form\IField;
use Dms\Core\Form\IFieldType;
use Dms\Web\Expressive\File\ITemporaryFileService;
use Dms\Web\Expressive\Renderer\Form\FormRenderingContext;
use Illuminate\Contracts\Config\Repository;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

/**
 * The file field renderer
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class FileFieldRenderer extends BladeFieldRenderer
{
    /**
     * @var ITemporaryFileService
     */
    protected $tempFileService;

    /**
     * @var Repository
     */
    private $config;

    /**
     * @var RouterInterface
     */
    protected $router;

    public function __construct(
        ITemporaryFileService $tempFileService,
        Repository $config,
        TemplateRendererInterface $template,
        RouterInterface $router,
        RelatedEntityLinker $relatedEntityLinker
    ) {
        parent::__construct($template, $relatedEntityLinker);
        $this->tempFileService = $tempFileService;
        $this->config          = $config;
        $this->router = $router;
    }


    /**
     * Gets the expected class of the field type for the field.
     *
     * @return array
     */
    public function getFieldTypeClasses() : array
    {
        return [FileUploadType::class];
    }

    protected function canRender(FormRenderingContext $renderingContext, IField $field, IFieldType $fieldType) : bool
    {
        return !$fieldType->has(FieldType::ATTR_OPTIONS);
    }

    protected function renderField(FormRenderingContext $renderingContext, IField $field, IFieldType $fieldType) : string
    {
        return $this->renderView(
            $field,
            'dms::components.field.dropzone.input',
            [
                FileUploadType::ATTR_MAX_SIZE    => 'maxFileSize',
                FileUploadType::ATTR_MIN_SIZE    => 'minFileSize',
                FileUploadType::ATTR_EXTENSIONS  => 'extensions',
                ImageUploadType::ATTR_MAX_WIDTH  => 'maxImageWidth',
                ImageUploadType::ATTR_MAX_HEIGHT => 'maxImageHeight',
                ImageUploadType::ATTR_MIN_WIDTH  => 'minImageWidth',
                ImageUploadType::ATTR_MIN_HEIGHT => 'minImageHeight',
            ],
            [
                'imagesOnly'    => $fieldType instanceof ImageUploadType,
                'existingFiles' => $this->getExistingFilesArray([$field->getUnprocessedInitialValue()]),
            ]
        );
    }

    protected function getExistingFilesArray(array $files) : array
    {
        /**
         * @var IFile[] $existingFiles
         */
        $existingFiles = [];
        $publicFiles   = [];

        foreach ($files as $file) {
            if (empty($file['file'])) {
                continue;
            }

            if ($this->isPublicFile($file['file'])) {
                $publicFiles[] = $file['file'];
            } else {
                $existingFiles[] = $file['file'];
            }
        }

        $tempFiles = $this->tempFileService->storeTempFiles(
            $existingFiles,
            $this->config->get('dms.storage.temp-files.download-expiry')
        );

        $data = [];

        foreach (array_merge($publicFiles, $existingFiles) as $key => $file) {
            /**
             * @var IFile $file
             */
            $tempFile        = $tempFiles[$key] ?? null;
            $imageDimensions = @getimagesize($file->getFullPath());

            $data[] = [
                    'name'        => $file->getClientFileNameWithFallback(),
                    'size'        => $file->exists() ? $file->getSize() : 0,
                    'previewUrl'  => $tempFile ? $this->router->generateUri('dms::file.preview', ['token' => $tempFile->getToken()]) : asset_file_url($file),
                    'downloadUrl' => $tempFile ? $this->router->generateUri('dms::file.download', ['token' => $tempFile->getToken()]) : asset_file_url($file),
                ] + ($imageDimensions ? ['width' => $imageDimensions[0], 'height' => $imageDimensions[1]] : []);
        }

        return $data;
    }

    private function isPublicFile(IFile $file)
    {
        return strpos($file->getFullPath(), PathHelper::normalize($this->config->get('dms.storage.public-files.dir'))) === 0;
    }

    protected function renderFieldValue(FormRenderingContext $renderingContext, IField $field, $value, IFieldType $fieldType) : string
    {
        return $this->renderValueViewWithNullDefault(
            $field,
            $value,
            'dms::components.field.dropzone.value',
            [
                'existingFiles' => $value !== null
                    ? $this->getExistingFilesArray([$value])
                    : null,
            ]
        );
    }
}
