<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Renderer\Table\Column\Component;

use Dms\Common\Structure\FileSystem\Form\FileUploadType;
use Dms\Common\Structure\FileSystem\Form\ImageUploadType;
use Dms\Common\Structure\FileSystem\Image;
use Dms\Common\Structure\FileSystem\PathHelper;
use Dms\Core\Exception\InvalidArgumentException;
use Dms\Core\File\IFile;
use Dms\Core\Form\Field\Type\FileType;
use Dms\Core\Form\Field\Type\ImageType;
use Dms\Core\Model\Criteria\Condition\ConditionOperator;
use Dms\Core\Table\IColumnComponent;
use Dms\Web\Expressive\Renderer\Table\IColumnComponentRenderer;
use Illuminate\Config\Repository;

/**
 * The file preview component renderer.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class FilePreviewComponentRenderer implements IColumnComponentRenderer
{
    /**
     * @var Repository
     */
    protected $config;

    /**
     * FilePreviewComponentRenderer constructor.
     *
     * @param Repository $config
     */
    public function __construct(Repository $config)
    {
        $this->config = $config;
    }

    /**
     * @param IColumnComponent $component
     *
     * @return bool
     */
    public function accepts(IColumnComponent $component) : bool
    {
        $fieldType = $component->getType()->getOperator(ConditionOperator::EQUALS)->getField()->getType();

        return $fieldType instanceof FileUploadType || $fieldType instanceof ImageUploadType
        || $fieldType instanceof FileType || $fieldType instanceof ImageType;
    }

    /**
     * Renders the supplied column component value as a html string.
     *
     * @param IColumnComponent $component
     * @param mixed            $value
     *
     * @return string
     * @throws InvalidArgumentException
     */
    public function render(IColumnComponent $component, $value) : string
    {
        /** @var IFile $value */
        $isImage = $value instanceof Image;

        $name = $value->getClientFileNameWithFallback();
        if ($isImage && $this->isPublicFile($value)) {
            $url = asset_file_url($value);

            return '<img src="' . e($url) . '" alt="' . e($name) . '" />';
        } else {
            $url = asset('vendor/dms/img/file/icon/' . strtolower(array_last(explode('.', $name))) . '.png');

            return '<img class="dms-file-icon" src="' . e($url) . '" alt="' . e($name) . '" />';
        }
    }

    private function isPublicFile(IFile $file)
    {
        return strpos($file->getFullPath(), PathHelper::normalize($this->config->get('dms.public.path'))) === 0;
    }
}
