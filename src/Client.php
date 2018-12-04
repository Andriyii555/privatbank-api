<?php declare(strict_types=1);

namespace SergeyNezbritskiy\PrivatBank;

use GuzzleHttp\Exception\GuzzleException;
use SergeyNezbritskiy\PrivatBank\Api\AuthorizedRequestInterface;
use SergeyNezbritskiy\PrivatBank\Api\RequestInterface;
use SergeyNezbritskiy\PrivatBank\Base\HttpResponse;
use SergeyNezbritskiy\PrivatBank\Base\PrivatBankApiException;

/**
 * Class Client
 * @package SergeyNezbritskiy\PrivatBank
 * @method RequestInterface exchangeRates()
 * @method RequestInterface exchangeRatesArchive()
 * @method RequestInterface infrastructure()
 * @method RequestInterface offices()
 * @method AuthorizedRequestInterface balance()
 * @method AuthorizedRequestInterface statements()
 * @method AuthorizedRequestInterface paymentInternal()
 * @method AuthorizedRequestInterface paymentMobile()
 * @method AuthorizedRequestInterface paymentUkraine()
 * @method AuthorizedRequestInterface paymentVisa()
 * @method AuthorizedRequestInterface checkPaymentMobile()
 * @method AuthorizedRequestInterface checkPayment()
 */
class Client
{

    /**
     * @var string
     */
    protected $url = 'https://api.privatbank.ua/p24api/';

    /**
     * @var bool
     */
    private $testMode = true;

    /**
     * @var int
     */
    private $waitTimeout = 0;

    /**
     * @param string $request
     * @param array $params
     * @return HttpResponse
     * @throws PrivatBankApiException
     */
    public function request(string $request, array $params = array()): HttpResponse
    {
        $params = array_merge([
            'method' => 'GET',
            'query' => [],
            'body' => '',
        ], $params);

        $request = new Request($request, ...[
            $params['method'],
            $params['query'],
            $params['body'],
        ]);

        return $this->send($request);
    }

    /**
     * @param Request $request
     * @return HttpResponse
     * @throws PrivatBankApiException
     */
    public function send(Request $request): HttpResponse
    {
        $client = new \GuzzleHttp\Client();
        $uri = $this->url . $request->getRequestUri();
        try {
            $response = $client->request($request->getMethod(), $uri, [
                'query' => $request->getQuery(),
                'body' => $request->getBody(),
            ]);
            $result = new HttpResponse(
                $response->getBody()->getContents(),
                $response->getStatusCode(),
                $response->getReasonPhrase()
            );
            return $result;
        } catch (GuzzleException $e) {
            throw new PrivatBankApiException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param bool $mode
     * @return Client
     */
    public function setTestMode(bool $mode): Client
    {
        $this->testMode = $mode;
        return $this;
    }

    /**
     * @return bool
     */
    public function isTestMode(): bool
    {
        return $this->testMode;
    }

    /**
     * @return int
     */
    public function getWaitTimeout(): int
    {
        return $this->waitTimeout;
    }

    /**
     * @param int $waitTimeout
     * @return Client
     */
    public function setWaitTimeout(int $waitTimeout): Client
    {
        $this->waitTimeout = $waitTimeout;
        return $this;
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return RequestInterface
     * @throws \ErrorException
     */
    public function __call($name, $arguments): RequestInterface
    {
        $class = '\\SergeyNezbritskiy\\PrivatBank\\Request\\' . ucfirst($name) . 'Request';
        if (class_exists($class)) {
            return new $class($this, ...$arguments);
        }
        throw new \ErrorException('Method ' . $name . ' not supported');
    }
}
