<?php declare(strict_types=1);

namespace Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ValueObjectCollection\Domain;

use Dms\Common\Structure\Web\EmailAddress;
use Dms\Common\Structure\Web\Html;
use Dms\Common\Structure\Web\IpAddress;
use Dms\Common\Structure\Web\Url;
use Dms\Core\Model\Object\ClassDefinition;
use Dms\Core\Model\Object\ValueObject;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class TestWebValueObject extends ValueObject
{
    const EMAIL_ADDRESS = 'emailAddress';
    const HTML = 'html';
    const IP_ADDRESS = 'ipAddress';
    const URL = 'url';

    /**
     * @var EmailAddress[]
     */
    public $emailAddress;

    /**
     * @var Html[]
     */
    public $html;

    /**
     * @var IpAddress[]
     */
    public $ipAddress;

    /**
     * @var Url[]
     */
    public $url;

    /**
     * Defines the structure of this class.
     *
     * @param ClassDefinition $class
     */
    protected function define(ClassDefinition $class)
    {
        $class->property($this->emailAddress)->asType(EmailAddress::collectionType());

        $class->property($this->html)->asType(Html::collectionType());

        $class->property($this->ipAddress)->asType(IpAddress::collectionType());

        $class->property($this->url)->asType(Url::collectionType());
    }
}