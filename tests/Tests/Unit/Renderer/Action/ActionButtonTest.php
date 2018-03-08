<?php
namespace Dms\Web\Expressive\Tests\Unit\Renderer\Action;

use Dms\Web\Expressive\Renderer\Action\ActionButton;
use PHPUnit\Framework\TestCase;

/**
 * @author Hari KT <kthari85@gmail.com>
 */
class ActionButtonTest extends TestCase
{
    public function testGetUrl()
    {
        $actionButton = new ActionButton(
            false,
            'edit',
            'Edit',
            function (string $objectId) {
                return str_replace('__object__', $objectId, '/hello/__object__');
            }
        );

        $this->assertSame('edit', $actionButton->getName());
        $this->assertSame('Edit', $actionButton->getLabel());
        $this->assertSame('/hello/123', $actionButton->getUrl(123));
        $this->assertFalse($actionButton->hasObjectSupportedCallback());
        $this->assertFalse($actionButton->isPost());
        $this->assertFalse($actionButton->isDisabled());
    }
}
