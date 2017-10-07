<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Tests\Integration\Scaffold\Fixture\ValueObjectCollection\Persistence\Infrastructure;

use Dms\Core\Persistence\Db\Mapping\Definition\MapperDefinition;
use Dms\Core\Persistence\Db\Mapping\IndependentValueObjectMapper;
use Dms\Web\Expressive\Tests\Integration\Scaffold\Fixture\ValueObjectCollection\Domain\TestWebValueObject;
use Dms\Common\Structure\Web\Persistence\EmailAddressMapper;
use Dms\Common\Structure\Web\Persistence\HtmlMapper;
use Dms\Common\Structure\Web\Persistence\IpAddressMapper;
use Dms\Common\Structure\Web\Persistence\UrlMapper;

/**
 * The Dms\Web\Expressive\Tests\Integration\Scaffold\Fixture\ValueObjectCollection\Domain\TestWebValueObject value object mapper.
 */
class TestWebValueObjectMapper extends IndependentValueObjectMapper
{
    /**
     * Defines the entity mapper
     *
     * @param MapperDefinition $map
     *
     * @return void
     */
    protected function define(MapperDefinition $map)
    {
        $map->type(TestWebValueObject::class);

        $map->embeddedCollection(TestWebValueObject::EMAIL_ADDRESS)
            ->toTable('test_web_value_object_email_address')
            ->withPrimaryKey('id')
            ->withForeignKeyToParentAs('test_web_value_object_id')
            ->using(new EmailAddressMapper('email_address'));

        $map->embeddedCollection(TestWebValueObject::HTML)
            ->toTable('test_web_value_object_html')
            ->withPrimaryKey('id')
            ->withForeignKeyToParentAs('test_web_value_object_id')
            ->using(new HtmlMapper('html'));

        $map->embeddedCollection(TestWebValueObject::IP_ADDRESS)
            ->toTable('test_web_value_object_ip_address')
            ->withPrimaryKey('id')
            ->withForeignKeyToParentAs('test_web_value_object_id')
            ->using(new IpAddressMapper('ip_address'));

        $map->embeddedCollection(TestWebValueObject::URL)
            ->toTable('test_web_value_object_url')
            ->withPrimaryKey('id')
            ->withForeignKeyToParentAs('test_web_value_object_id')
            ->using(new UrlMapper('url'));
    }
}
