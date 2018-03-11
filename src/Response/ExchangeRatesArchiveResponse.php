<?php declare(strict_types=1);

namespace SergeyNezbritskiy\PrivatBank\Response;

use SergeyNezbritskiy\PrivatBank\Base\AbstractResponse;

/**
 * Class ExchangeRatesArchiveResponse
 * @package SergeyNezbritskiy\PrivatBank\Response
 */
class ExchangeRatesArchiveResponse extends AbstractResponse
{

    /**
     * Response Sample
     * ```xml
     *  <?xml version="1.0" encoding="UTF-8" standalone="yes"?>
     *  <exchangerates date="01.12.2014" bank="PB" BaseCurrency="980" BaseCurrencyLit="UAH">
     *      <exchangerate baseCurrency="UAH" currency="AUD" saleRateNB="12.8319250" purchaseRateNB="12.8319250"/>
     *      <exchangerate baseCurrency="UAH" currency="CAD" saleRateNB="13.2107400" purchaseRateNB="13.2107400" saleRate="15.0000000" purchaseRate="13.0000000"/>
     *  </exchangerates>
     * ```
     * @return array
     */
    protected function getMap(): array
    {
        return [
            '{list} as exchangerate[]' => [
                'baseCurrency' => '@baseCurrency',
                'currency' => '@currency',
                'saleRateNB' => '@saleRateNB',
                'purchaseRateNB' => '@purchaseRateNB',
//                'saleRate' => '@saleRate',
//                'purchaseRate' => '@purchaseRate'
            ]
        ];
    }

}