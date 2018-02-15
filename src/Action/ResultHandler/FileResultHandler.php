<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Action\ResultHandler;

use Dms\Common\Structure\FileSystem\File;
use Dms\Core\Common\Crud\Action\Crud\CreateAction;
use Dms\Core\Common\Crud\Action\Crud\EditAction;
use Dms\Core\Common\Crud\ICrudModule;
use Dms\Core\Module\IAction;
use Dms\Web\Expressive\Action\ActionResultHandler;
use Dms\Web\Expressive\File\ITemporaryFileService;
use Dms\Web\Expressive\Http\ModuleContext;
use Illuminate\Config\Repository;
use Zend\Diactoros\Response\JsonResponse;

/**
 * The file action result handler.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class FileResultHandler extends ActionResultHandler
{
    /**
     * @var ITemporaryFileService
     */
    protected $tempFileService;

    /**
     * @var Repository
     */
    protected $config;

    /**
     * FileResultHandler constructor.
     *
     * @param ITemporaryFileService $tempFileService
     * @param Repository            $config
     */
    public function __construct(ITemporaryFileService $tempFileService, Repository $config)
    {
        parent::__construct();
        $this->tempFileService = $tempFileService;
        $this->config          = $config;
    }


    /**
     * @return string|null
     */
    protected function supportedResultType()
    {
        return File::class;
    }

    /**
     * @param IAction $action
     * @param mixed   $result
     *
     * @return bool
     */
    protected function canHandleResult(ModuleContext $moduleContext, IAction $action, $result) : bool
    {
        return !($action instanceof CreateAction || $action instanceof EditAction || $action->getName() === ICrudModule::REMOVE_ACTION);
    }

    /**
     * @param ModuleContext $moduleContext
     * @param IAction       $action
     * @param mixed         $result
     *
     * @return Response|mixed
     */
    protected function handleResult(ModuleContext $moduleContext, IAction $action, $result)
    {
        /**
 * @var File $result
*/
        $tempFile = $this->tempFileService->storeTempFile(
            $result,
            $this->config->get('dms.storage.temp-files.download-expiry')
        );

        return new JsonResponse(
            [
            'message' => 'The action was successfully executed',
            'files'   => [
                [
                    'name'  => $tempFile->getFile()->getClientFileNameWithFallback(),
                    'token' => $tempFile->getToken(),
                ],
            ],
            ]
        );
    }
}
