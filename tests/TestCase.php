<?php
/**
 * Plasma Schemas component
 * Copyright 2018-2019 PlasmaPHP, All Rights Reserved
 *
 * Website: https://github.com/PlasmaPHP
 * License: https://github.com/PlasmaPHP/schemas/blob/master/LICENSE
 * @noinspection PhpUnhandledExceptionInspection
*/

namespace Plasma\Schemas\Tests;

use Plasma\ClientInterface;
use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
use React\Promise\PromiseInterface;
use function Clue\React\Block\await;

class TestCase extends \PHPUnit\Framework\TestCase {
    /**
     * @var LoopInterface
     */
    public $loop;
    
    function setUp() {
        $this->loop = Factory::create();
    }
    
    function await(PromiseInterface $promise, float $timeout = 10.0) {
        return await($promise, $this->loop, $timeout);
    }
    
    function getClientMock(): ClientInterface {
        return $this->getMockBuilder(ClientInterface::class)
            ->setMethods(array(
                'create',
                'getConnectionCount',
                'checkinConnection',
                'beginTransaction',
                'close',
                'quit',
                'runCommand',
                'runQuery',
                'createReadCursor',
                'query',
                'prepare',
                'execute',
                'quote',
                'listeners',
                'on',
                'once',
                'emit',
                'removeListener',
                'removeAllListeners'
            ))
            ->getMock();
    }
}
