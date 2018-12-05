<?php declare(strict_types=1);

namespace SergeyNezbritskiy\PrivatBank\Tests\Request;

use PHPUnit\Framework\TestCase;
use SergeyNezbritskiy\PrivatBank\Client;
use SergeyNezbritskiy\PrivatBank\Merchant;
use SergeyNezbritskiy\PrivatBank\Request\StatementsRequest;
use SergeyNezbritskiy\PrivatBank\Response\StatementsResponse;

/**
 * Class StatementsRequestTest
 * @package SergeyNezbritskiy\PrivatBank\tests\Request
 */
class StatementsRequestTest extends TestCase
{

    /**
     * @var StatementsRequest
     */
    private $request;

    /**
     * @var Client
     */
    private $client;

    protected function setUp()
    {
        $this->client = new Client();
        $this->request = new StatementsRequest($this->client);
    }

    protected function tearDown()
    {
        $this->request = null;
        $this->client = null;
    }

    /**
     * @throws \SergeyNezbritskiy\PrivatBank\Base\PrivatBankApiException
     */
    public function testBalance()
    {
        $merchantId = getenv('merchantId');
        $merchantSecret = getenv('merchantSecret');
        $card = getenv('cardNumber');
        $startDate = getenv('startDate');
        $endDate = getenv('endDate');
        if (empty($card) || empty($merchantId) || empty($merchantSecret) || empty($startDate) || empty($endDate)) {
            $this->markTestSkipped('Merchant data not specified');
        }

        $merchantId = new Merchant($merchantId, $merchantSecret);
        $this->request->setMerchant($merchantId);
        $result = $this->request->execute([
            'cardNumber' => $card,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);

        $this->assertInstanceOf(StatementsResponse::class, $result);

        $statements = $result->getData();

        $this->assertGreaterThan(0, count($statements));

        foreach ($statements as $cardData) {
            $this->assertArrayHasKey('card', $cardData);
            $this->assertArrayHasKey('appcode', $cardData);
            $this->assertArrayHasKey('trandate', $cardData);
            $this->assertArrayHasKey('trantime', $cardData);
            $this->assertArrayHasKey('amount', $cardData);
            $this->assertArrayHasKey('cardamount', $cardData);
            $this->assertArrayHasKey('rest', $cardData);
            $this->assertArrayHasKey('terminal', $cardData);
            $this->assertArrayHasKey('description', $cardData);
            break;
        }
    }
}
