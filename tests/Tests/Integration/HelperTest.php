<?php declare(strict_types=1);

namespace Dms\Web\Laravel\Tests\Integration;

use Dms\Common\Structure\FileSystem\File;
use Dms\Core\Exception\InvalidArgumentException;
use Dms\Web\Laravel\Tests\Integration\Fixtures\Demo\DemoFixture;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class HelperTest extends CmsIntegrationTest
{
    protected static function getFixture()
    {
        return new DemoFixture();
    }

    public function testAssetFileInWrongPathUrl()
    {
        $this->expectException(InvalidArgumentException::class);

        asset_file_url(new File(__FILE__));
    }


    public function testNonExistentFileInWrongPath()
    {
        $this->expectException(InvalidArgumentException::class);

        asset_file_url(new File(__FILE__ . 'fsdfsdf'));
    }

    public function testAssetFileInCorrectPathUrl()
    {
        $this->assertSame(asset('.gitignore'), asset_file_url(new File(public_path('.gitignore'))));
    }

    public function testNonExistentFileInCorrectPathUrl()
    {
        $this->assertSame(asset('non-existent'), asset_file_url(new File(public_path() .  DIRECTORY_SEPARATOR . 'non-existent')));
    }
}