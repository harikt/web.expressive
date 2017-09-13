<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Action\InputTransformer;

use Dms\Common\Structure\FileSystem\UploadedFileFactory;
use Dms\Core\Module\IParameterizedAction;
use Dms\Web\Expressive\Action\IActionInputTransformer;
use Dms\Web\Expressive\Http\ModuleContext;
use Symfony\Component\HttpFoundation\File\UploadedFile as SymfonyUploadedFile;

/**
 * Converts symphony uploaded files to the equivalent dms uploaded file class.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class SymfonyToDmsUploadedFileTransformer implements IActionInputTransformer
{
    /**
     * Transforms for the supplied action.
     *
     * @param ModuleContext        $moduleContext
     * @param IParameterizedAction $action
     * @param array                $input
     *
     * @return array
     */
    public function transform(ModuleContext $moduleContext, IParameterizedAction $action, array $input) : array
    {
        array_walk_recursive($input, function (&$value) {
            if ($value instanceof SymfonyUploadedFile) {
                $value = UploadedFileFactory::build(
                    $value->getRealPath(),
                    $value->getError(),
                    $value->getClientOriginalName(),
                    $value->getClientMimeType()
                );
            }
        });

        return $input;
    }
}
