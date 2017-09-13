<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Auth\Module;

use Dms\Core\Form\Object\FormObjectDefinition;
use Dms\Core\Form\Object\IndependentFormObject;

/**
 * The admin password reset form builder class.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class AdminPasswordResetForm extends IndependentFormObject
{
    /**
     * @var string
     */
    public $newPassword;

    /**
     * @var string
     */
    public $newPasswordConfirmation;

    /**
     * Defines the structure of the form object.
     *
     * @param FormObjectDefinition $form
     *
     * @return void
     */
    protected function defineForm(FormObjectDefinition $form)
    {
        $form->section('Details', [
            $form->field($this->newPassword)
                ->name('new_password')
                ->label('New Password')
                ->string()
                ->password()
                ->minLength(6)
                ->maxLength(50)
                ->required(),
            $form->field($this->newPasswordConfirmation)
                ->name('new_password_confirmation')
                ->label('Confirm Password')
                ->string()
                ->password()
                ->required(),
        ]);

        $form->fieldsMatch('new_password', 'new_password_confirmation');
    }
}
