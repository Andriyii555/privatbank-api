<?php declare(strict_types=1);

namespace SergeyNezbritskiy\PrivatBank\tests\Response;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use SergeyNezbritskiy\PrivatBank\Response\ExchangeRatesResponse;

/**
 * Class ExchangeRatesResponseTest
 * @package SergeyNezbritskiy\PrivatBank\tests\Response
 */
class ExchangeRatesResponseTest extends TestCase
{
    /**
     * @var ExchangeRatesResponse
     */
    private $response;
    private $content = array();

    protected function setUp()
    {
        $test = $this;
        $stream = $this->getMockBuilder(\stdClass::class)
            ->setMethods(['getContents'])
            ->getMock();
        $stream->expects($this->once())
            ->method('getContents')
            ->willReturnCallback(function () use ($test) {
                return $test->content;
            });

        /** @var Response|MockObject $httpResponse */
        $httpResponse = $this->getMockBuilder(Response::class)
            ->setMethods(['getBody'])
            ->getMock();
        $httpResponse->expects($this->once())
            ->method('getBody')
            ->willReturn($stream);
        $this->response = new ExchangeRatesResponse($httpResponse);
    }

    public function tearDown()
    {
        $this->response = null;
    }

    public function testSuccessfulResponse()
    {
        $this->content = <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<exchangerates>
    <row>
        <exchangerate ccy="USD" base_ccy="UAH" buy="26.00000" sale="26.45503"/>
    </row>
    <row>
        <exchangerate ccy="EUR" base_ccy="UAH" buy="32.20000" sale="32.78689"/>
    </row>
    <row>
        <exchangerate ccy="RUR" base_ccy="UAH" buy="0.45000" sale="0.48008"/>
    </row>
    <row>
        <exchangerate ccy="BTC" base_ccy="USD" buy="8332.9060" sale="9210.0540"/>
    </row>
</exchangerates>
XML;

        $result = $this->response->toArray();
        $this->assertInternalType('array', $result);
        foreach ($result as $item) {
            $this->assertArrayHasKey('ccy', $item);
            $this->assertArrayHasKey('base_ccy', $item);
            $this->assertArrayHasKey('buy', $item);
            $this->assertArrayHasKey('sale', $item);
        }
        $this->assertGreaterThan(0, count($result));
    }
}