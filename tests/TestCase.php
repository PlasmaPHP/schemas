<?php
/**
 * Plasma Schemas component
 * Copyright 2018-2019 PlasmaPHP, All Rights Reserved
 *
 * Website: https://github.com/PlasmaPHP
 * License: https://github.com/PlasmaPHP/schemas/blob/master/LICENSE
*/

namespace Plasma\Schemas\Tests;

class TestCase extends \PHPUnit\Framework\TestCase {
    /**
     * @var \React\EventLoop\LoopInterface
     */
    public $loop;
    
    function setUp() {
        $this->loop = \React\EventLoop\Factory::create();
    }
    
    function await(\React\Promise\PromiseInterface $promise, float $timeout = 10.0) {
        return \Clue\React\Block\await($promise, $this->loop, $timeout);
    }
    
    function getClientMock(): \Plasma\ClientInterface {
        return $this->getMockBuilder(\Plasma\ClientInterface::class)
            ->setMethods(array(
                'create',
                'getConnectionCount',
                'checkinConnection',
                'beginTransaction',
                'close',
                'quit',
                'runCommand',
                'runQuery',
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
