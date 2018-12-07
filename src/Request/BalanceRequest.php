<?php declare(strict_types=1);

namespace SergeyNezbritskiy\PrivatBank\Request;

use InvalidArgumentException;
use SergeyNezbritskiy\PrivatBank\Api\HttpResponseInterface;
use SergeyNezbritskiy\PrivatBank\Api\ResponseInterface;
use SergeyNezbritskiy\PrivatBank\Base\AbstractAuthorizedRequest;
use SergeyNezbritskiy\PrivatBank\Response\BalanceResponse;

/**
 * Class BalanceRequest
 *
 * Params:
 * cardNumber - required, integer
 * country - optional, string
 * @package SergeyNezbritskiy\PrivatBank\Request
 * @see https://api.privatbank.ua/#p24/balance
 */
class BalanceRequest extends AbstractAuthorizedRequest
{

    /**
     * Body sample
     * ```xml
     *  <data>
     *      <oper>cmt</oper>
     *      <wait>0</wait>
     *      <test>0</test>
     *      <payment id="">
     *          <prop name="cardnum" value="5168742060221193" />
     *          <prop name="country" value="UA" />
     *      </payment>
     *  </data>
     * ```
     * @return array
     */
    protected function getBodyMap(): array
    {
        return [
            'oper',
            'wait',
            'test',
            'payment' => [
                'children' => [
                    'prop[]' => [
                        'dataProvider' => 'payment',
                        'attributes' => ['name', 'value'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    protected function getBodyParams(): array
    {
        $params = $this->getParams();

        return array_merge(parent::getBodyParams(), [
            'payment' => [
                [
                    'name' => 'cardnum',
                    'value' => $params['cardNumber'],
                ],
                [
                    'name' => 'country',
                    'value' => $params['country'],
                ]
            ]
        ]);
    }

    /**
     * @param array $params
     * @return array
     */
    protected function initParams(array $params): array
    {
        if (empty($params['cardNumber'])) {
            throw new InvalidArgumentException('Argument cardNumber is required');
        }
        return array_merge(['country' => ''], $params);
    }

    /**
     * @return string
     */
    protected function getRoute(): string
    {
        return 'balance';
    }

    /**
     * @param HttpResponseInterface $httpResponse
     * @return ResponseInterface
     * @throws \SergeyNezbritskiy\PrivatBank\Base\PrivatBankApiException
     */
    protected function getResponse(HttpResponseInterface $httpResponse): ResponseInterface
    {
        return new BalanceResponse($httpResponse);
    }
}
