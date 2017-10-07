<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Tests\Integration\Scaffold\Fixture\ValueObject\Persistence\Infrastructure;

use Dms\Core\Persistence\Db\Mapping\Definition\MapperDefinition;
use Dms\Core\Persistence\Db\Mapping\IndependentValueObjectMapper;
use Dms\Web\Expressive\Tests\Integration\Scaffold\Fixture\ValueObject\Domain\TestWebValueObject;
use Dms\Common\Structure\Web\Persistence\EmailAddressMapper;
use Dms\Common\Structure\Web\Persistence\HtmlMapper;
use Dms\Common\Structure\Web\Persistence\IpAddressMapper;
use Dms\Common\Structure\Web\Persistence\UrlMapper;

/**
 * The Dms\Web\Expressive\Tests\Integration\Scaffold\Fixture\ValueObject\Domain\TestWebValueObject value object mapper.
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

        $map->embedded(TestWebValueObject::EMAIL_ADDRESS)
            ->using(new EmailAddressMapper('email_address'));

        $map->embedded(TestWebValueObject::NULLABLE_EMAIL_ADDRESS)
            ->withIssetColumn('nullable_email_address')
            ->using(new EmailAddressMapper('nullable_email_address'));

        $map->embedded(TestWebValueObject::HTML)
            ->using(new HtmlMapper('html'));

        $map->embedded(TestWebValueObject::NULLABLE_HTML)
            ->withIssetColumn('nullable_html')
            ->using(new HtmlMapper('nullable_html'));

        $map->embedded(TestWebValueObject::IP_ADDRESS)
            ->using(new IpAddressMapper('ip_address'));

        $map->embedded(TestWebValueObject::NULLABLE_IP_ADDRESS)
            ->withIssetColumn('nullable_ip_address')
            ->using(new IpAddressMapper('nullable_ip_address'));

        $map->embedded(TestWebValueObject::URL)
            ->using(new UrlMapper('url'));

        $map->embedded(TestWebValueObject::NULLABLE_URL)
            ->withIssetColumn('nullable_url')
            ->using(new UrlMapper('nullable_url'));
    }
}
