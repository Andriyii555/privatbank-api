<?php declare(strict_types=1);

namespace SergeyNezbritskiy\PrivatBank\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use SergeyNezbritskiy\PrivatBank\Client;
use SergeyNezbritskiy\PrivatBank\Request;
use SergeyNezbritskiy\PrivatBank\Request\ExchangeRatesArchiveRequest;
use SergeyNezbritskiy\PrivatBank\Request\ExchangeRatesRequest;
use SergeyNezbritskiy\PrivatBank\Request\InfrastructureRequest;
use SergeyNezbritskiy\PrivatBank\Request\OfficesRequest;

/**
 * Class ClientTest
 */
class ClientTest extends TestCase
{

    /**
     * @var Client
     */
    private $client;

    protected function setUp()
    {
        $this->client = new Client();
    }

    protected function tearDown()
    {
        $this->client = null;
    }

    public function testRequest()
    {
        $response = $this->client->request('pubinfo', [
            'query' => ['exchange' => '', 'coursid' => 11]
        ]);
        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    public function testSend()
    {
        $request = new Request('pubinfo', '', ['exchange' => '', 'coursid' => 11]);
        $response = $this->client->send($request);
        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    public function testGetExchangeRates()
    {
        $this->assertInstanceOf(ExchangeRatesRequest::class, $this->client->exchangeRates());
    }

    public function testGetExchangeRatesArchive()
    {
        $this->assertInstanceOf(ExchangeRatesArchiveRequest::class, $this->client->exchangeRatesArchive());
    }

    public function testGetOffices()
    {
        $this->assertInstanceOf(OfficesRequest::class, $this->client->offices());
    }

    public function testTerminals()
    {
        $this->assertInstanceOf(InfrastructureRequest::class, $this->client->infrastructure());
    }

    public function testNotSupportedMethod()
    {
        $this->expectException(\ErrorException::class);
        $this->expectExceptionMessage('Method notSupportedMethod not supported');
        call_user_func_array([$this->client, 'notSupportedMethod'], []);
    }

}