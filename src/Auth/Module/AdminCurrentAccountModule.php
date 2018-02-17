<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Auth\Module;

use Dms\Core\Auth\IAdminRepository;
use Dms\Core\Auth\IAuthSystem;
use Dms\Core\Form\Builder\Form;
use Dms\Core\Language\Message;
use Dms\Core\Model\Object\ArrayDataObject;
use Dms\Core\Module\Definition\ModuleDefinition;
use Dms\Core\Module\Module;
use Dms\Web\Expressive\Auth\Admin;
use Dms\Web\Expressive\Auth\Password\IPasswordHasherFactory;
use Dms\Web\Expressive\Auth\Password\IPasswordResetService;

/**
 * The admin profile module.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class AdminCurrentAccountModule extends Module
{
    /**
     * @var IAdminRepository
     */
    private $dataSource;

    /**
     * @var IPasswordHasherFactory
     */
    private $hasher;

    /**
     * @var IPasswordResetService
     */
    private $passwordResetService;

    /**
     * UserModule constructor.
     *
     * @param IAdminRepository       $dataSource
     * @param IPasswordHasherFactory $hasher
     * @param IAuthSystem            $authSystem
     * @param IPasswordResetService  $passwordResetService
     */
    public function __construct(
        IAdminRepository $dataSource,
        IPasswordHasherFactory $hasher,
        IAuthSystem $authSystem,
        IPasswordResetService $passwordResetService
    ) {
        $this->dataSource           = $dataSource;
        $this->hasher               = $hasher;
        $this->passwordResetService = $passwordResetService;
        parent::__construct($authSystem);
    }

    /**
     * Defines the module.
     *
     * @param ModuleDefinition $module
     */
    protected function define(ModuleDefinition $module)
    {
        $module->name('account');

        $module->metadata(
            [
            'icon' => 'cog',
            ]
        );

        /**
         * @var Admin $user
        */
        $user = $this->authSystem->getAuthenticatedUser();

        $module->action('update-profile')
            ->form(
                Form::create()->section(
                    'Details',
                    [
                    AdminProfileFields::buildFullNameField($this->dataSource)->value($user->getFullName()),
                    AdminProfileFields::buildEmailField($this->dataSource)->value($user->getEmailAddressObject()),
                    AdminProfileFields::buildUsernameField($this->dataSource)->value($user->getUsername()),
                    ]
                )
            )
            ->returns(Message::class)
            ->handler(
                function (ArrayDataObject $input) {
                    /**
                     * @var Admin $user
                     */
                    $user = $this->authSystem->getAuthenticatedUser();

                    $user->setFullName($input['name']);
                    $user->setUsername($input['username']);
                    $user->setEmailAddress($input['email']);

                    $this->dataSource->save($user);

                    return new Message('auth.user.profile-updated');
                }
            );

        $module->action('change-password')
            ->form(new AdminPasswordResetForm())
            ->returns(Message::class)
            ->handler(
                function (AdminPasswordResetForm $input) {
                    $user = $this->authSystem->getAuthenticatedUser();

                    $this->passwordResetService->resetUserPassword($user, $input->newPassword);

                    return new Message('auth.user.password-reset');
                }
            );

        $module->widget('update-profile')
            ->label('Update Profile')
            ->withAction('update-profile');

        $module->widget('change-password')
            ->label('Change Password')
            ->withAction('change-password');
    }
}
