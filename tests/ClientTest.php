<?php

use G\Gearman\Client;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    private $actions = ['doHigh', 'doLow', 'doNormal', 'doBackground', 'doHighBackground', 'doLowBackground'];

    public function test_simple_client()
    {
        foreach ($this->actions as $action) {
            $callCount     = 0;
            $gearmanClient = $this->getMock('GearmanClient');

            $gearmanClient
                ->expects($this->any())
                ->method('returnCode')
                ->willReturn(0);

            $gearmanClient
                ->expects($this->exactly(1))
                ->method($action)
                ->willReturnCallback(function ($name, $workload, $unique) {
                    $this->assertEquals(json_encode('HELLO'), $workload);
                    $this->assertEquals('uid', $unique);
                    $this->assertEquals('hello', $name);
                    $job = $this->getMock('\GearmanJob');
                    $job->expects($this->any())
                        ->method('workload')
                        ->willReturn(json_encode("myWorkload"));

                    return "RETURN";
                });

            $client = new Client($gearmanClient);
            $client->onSuccess(function ($response) use (&$callCount) {
                $callCount++;
                $this->assertEquals('RETURN', $response);
            });

            $client->$action('hello', "HELLO", 'uid');

            $this->assertEquals(1, $callCount);
        }
    }

    public function test_client_with_error()
    {
        foreach ($this->actions as $action) {
            $callCount     = 0;
            $gearmanClient = $this->getMock('GearmanClient');

            $gearmanClient
                ->expects($this->exactly(1))
                ->method('returnCode')
                ->willReturn(1);

            $gearmanClient
                ->expects($this->exactly(1))
                ->method('error')
                ->willReturn("ERROR");

            $gearmanClient
                ->expects($this->any())
                ->method($action)
                ->willReturn("Error");

            $client = new Client($gearmanClient);

            try {
                $client->$action('hello', "HELLO", 'uid');
            } catch (\Exception $e) {
                $callCount++;
                $this->assertEquals("ERROR", $e->getMessage());
                $this->assertEquals(1, $e->getCode());
            }

            $this->assertEquals(1, $callCount);
        }
    }
}