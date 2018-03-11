<?php declare(strict_types=1);

namespace SergeyNezbritskiy\PrivatBank\tests;

use PHPUnit\Framework\TestCase;
use SergeyNezbritskiy\PrivatBank\Merchant;

/**
 * Class MerchantTest
 * @package SergeyNezbritskiy\PrivatBank\tests
 */
class MerchantTest extends TestCase
{

    public function testMerchantGetters()
    {
        $id = '12345';
        $signature = md5('my_custom_string');
        $merchant = new Merchant($id, $signature);
        $this->assertEquals($id, $merchant->getId());
        $this->assertEquals($signature, $merchant->getSignature());
    }

}