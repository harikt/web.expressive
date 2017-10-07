<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Tests\Integration\Scaffold\Fixture\ValueObject\Domain;

use Dms\Common\Structure\Money\Currency;
use Dms\Common\Structure\Money\Money;
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
    const NULLABLE_EMAIL_ADDRESS = 'nullableEmailAddress';
    const HTML = 'html';
    const NULLABLE_HTML = 'nullableHtml';
    const IP_ADDRESS = 'ipAddress';
    const NULLABLE_IP_ADDRESS = 'nullableIpAddress';
    const URL = 'url';
    const NULLABLE_URL = 'nullableUrl';

    /**
     * @var EmailAddress
     */
    public $emailAddress;

    /**
     * @var EmailAddress|null
     */
    public $nullableEmailAddress;

    /**
     * @var Html
     */
    public $html;

    /**
     * @var Html|null
     */
    public $nullableHtml;

    /**
     * @var IpAddress
     */
    public $ipAddress;

    /**
     * @var IpAddress|null
     */
    public $nullableIpAddress;

    /**
     * @var Url
     */
    public $url;

    /**
     * @var Url|null
     */
    public $nullableUrl;

    /**
     * Defines the structure of this class.
     *
     * @param ClassDefinition $class
     */
    protected function define(ClassDefinition $class)
    {
        $class->property($this->emailAddress)->asObject(EmailAddress::class);

        $class->property($this->nullableEmailAddress)->nullable()->asObject(EmailAddress::class);

        $class->property($this->html)->asObject(Html::class);

        $class->property($this->nullableHtml)->nullable()->asObject(Html::class);

        $class->property($this->ipAddress)->asObject(IpAddress::class);

        $class->property($this->nullableIpAddress)->nullable()->asObject(IpAddress::class);

        $class->property($this->url)->asObject(Url::class);

        $class->property($this->nullableUrl)->nullable()->asObject(Url::class);
    }
}
