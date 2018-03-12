<?php declare(strict_types=1);

namespace SergeyNezbritskiy\PrivatBank\Base;

use GuzzleHttp\Client;
use SergeyNezbritskiy\PrivatBank\Api\AuthorizedRequestInterface;
use SergeyNezbritskiy\PrivatBank\Api\ResponseInterface;
use SergeyNezbritskiy\PrivatBank\Merchant;
use SergeyNezbritskiy\XmlIo\XmlWriter;

/**
 * Class AbstractAuthorizedRequest
 * @package SergeyNezbritskiy\PrivatBank\Base
 */
abstract class AbstractAuthorizedRequest extends AbstractRequest implements AuthorizedRequestInterface
{

    protected $merchant;

    /**
     * @return mixed
     */
    public function getMerchant(): Merchant
    {
        return $this->merchant;
    }

    /**
     * @param mixed $merchant
     * @return AuthorizedRequestInterface
     */
    public function setMerchant(Merchant $merchant): AuthorizedRequestInterface
    {
        $this->merchant = $merchant;
        return $this;
    }

    /**
     * @param array $params
     * @return array
     */
    abstract protected function getBodyMap(array $params = []): array;

    /**
     * @param array $params
     * @return ResponseInterface
     */
    public function execute(array $params = array()): ResponseInterface
    {
        $client = new Client();

        $requestUri = $this->url . $this->getRoute();
        $response = $client->request('POST', $requestUri, [
            'body' => $this->getBody($params),
        ]);
        return $this->getResponse($response);
    }

    /**
     * @param $params
     * @return string
     */
    protected function getBody(array $params = []): string
    {
        $data = [
            'oper' => 'cmt',
            'wait' => 0,
            'test' => 1,
            'payment' => [[
                'name' => 'phone',
                'value' => '%2B380632285977',
            ], [
                'name' => 'amt',
                'value' => '0.05',
            ]]
        ];
        $xmlWriter = new XmlWriter();
        $dataXml = $xmlWriter->toXml($data, $this->getBodyMap());
        $signature = $this->calculateSignature($dataXml);


        $bodyMap = $this->getBodyMap();
        $bodyMap['data']['dataProvider'] = 'data';
        $childrenMap = array_merge([
            'merchant' => [
                'dataProvider' => 'merchant',
                'children' => ['id', 'signature']
            ]
        ], $bodyMap);

        $documentMap = [
            'request' => [
                'attributes' => ['version'],
                'children' => $childrenMap
            ]
        ];
        $documentData = [
            'version' => '1.0',
            'merchant' => [
                'id' => $this->getMerchant()->getId(),
                'signature' => $signature,
            ],
            'data' => $data,
        ];
        return $xmlWriter->toXmlString($documentData, $documentMap);

    }

    private function calculateSignature($xml)
    {
        $innerXml = '';
        foreach ($xml->childNodes as $node) {
            $innerXml .= $node->ownerDocument->saveXML($node, LIBXML_NOEMPTYTAG);
        }
        return $this->getMerchant()->calculateSignature($innerXml);
    }

}