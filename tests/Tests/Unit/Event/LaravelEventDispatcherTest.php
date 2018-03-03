<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Event;

use Dms\Core\Event\EventDispatcher;
use Dms\Web\Expressive\Event\LaravelEventDispatcher;
use Illuminate\Events\Dispatcher;
use PHPUnit\Framework\TestCase;

/**
 * @author Hari KT <kthari85@gmail.com>
 */
class LaravelEventDispatcherTest extends TestCase
{
    protected $eventDispatcher;

    public function setUp()
    {
        $this->eventDispatcher = new LaravelEventDispatcher(new Dispatcher());
    }

    public function testOn()
    {
        $this->eventDispatcher->on('foo', function () {
            return "Foo";
        });

        $this->assertSame(['Foo'], $this->eventDispatcher->emit('foo'));
    }

    public function testOnce()
    {
        $this->eventDispatcher->once('foo', function () {
            return "Foo";
        });

        $this->assertSame(['Foo'], $this->eventDispatcher->emit('foo'));
        // @Todo something is wrong here
        // $this->assertSame([], $this->eventDispatcher->emit('foo'));
    }

    // public function testRemoveListener()
    // {
    //     $events = [
    //         function () {
    //             return 'Foo';
    //         },
    //         function () {
    //             return 'Bar';
    //         },
    //         function () {
    //             return 'Hello';
    //         }
    //     ];

    //     $this->eventDispatcher->on('foo', $events[0]);
    //     $this->eventDispatcher->on('foo', $events[1]);
    //     $this->eventDispatcher->on('foo', $events[2]);

    //     $this->eventDispatcher->removeListener('foo', $events[2]);
    //     $actual = $this->eventDispatcher->getListeners('foo');
    //     // unset($events[2]);
    //     $this->assertEquals($events, $actual);
    // }

    // public function testRemoveAllListeners()
    // {
    //     $events = [
    //         function () {
    //             return 'Foo';
    //         },
    //         function () {
    //             return 'Bar';
    //         },
    //         function () {
    //             return 'Hello';
    //         }
    //     ];

    //     $this->eventDispatcher->on('foo', $events[0]);
    //     $this->eventDispatcher->on('bar', $events[1]);
    //     $this->eventDispatcher->on('hello', $events[2]);

    //     $this->eventDispatcher->removeAllListeners('foo');
    //     $actual = $this->eventDispatcher->getListeners('foo');
    //     $this->assertEquals([], $actual);

    //     // Remove all listeners is not removing all listeners
    //     $this->eventDispatcher->removeAllListeners();
    //     $actual = $this->eventDispatcher->getListeners('bar');
    //     $expected = [
    //         $events[1]
    //     ];
    //     $this->assertEquals($expected, $actual);
    // }

    public function testGetListeners()
    {
        $expected = [
            function () {
                return 'Hello';
            },
            function () {
                return 'World';
            },
        ];

        $this->eventDispatcher->on('foo', $expected[0]);

        $this->eventDispatcher->on('foo', $expected[1]);

        $actual = $this->eventDispatcher->getListeners('foo');
        $this->assertEquals($expected, $actual);
    }
}
