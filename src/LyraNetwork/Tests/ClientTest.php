<?php
namespace LyraNetwork\Tests;

use PHPUnit_Framework_TestCase;
use LyraNetwork\Client;
use LyraNetwork\Constants;

/**
 * ./vendor/bin/phpunit src/LyraNetwork/Tests/ClientTest.php
 */
class ClientTest extends PHPUnit_Framework_TestCase
{
    /**
     * ./vendor/bin/phpunit --filter testClientValidCall src/LyraNetwork/Tests/ClientTest.php
     */
    public function testClientValidCall()
    {
        $store = array("value" => "sdk test string value");
        
        $client = new Client("69876357:testprivatekey_DEMOPRIVATEKEY23G4475zXZQ2UA5x7M");
        $response = $client->post('Charge/SDKTest', $store);

        $this->assertEquals("SUCCESS", $response["status"]);
        $this->assertEquals($store["value"], $response["answer"]["value"]);
    }

    /**
     * ./vendor/bin/phpunit --filter testFileGetContentsClientValidCall src/LyraNetwork/Tests/ClientTest.php
     */
    public function testFileGetContentsClientValidCall()
    {
        $store = array("value" => "sdk test string value");
        
        $client = new Client("69876357:testprivatekey_DEMOPRIVATEKEY23G4475zXZQ2UA5x7M");
        $response = $client->postWithFileGetContents('Charge/SDKTest', $store);

        $this->assertEquals("SUCCESS", $response["status"]);
        $this->assertEquals($store["value"], $response["answer"]["value"]);
    }

    /**
     * ./vendor/bin/phpunit --filter testClientWrongKey src/LyraNetwork/Tests/ClientTest.php
     *
     * @expectedException LyraNetwork\Exceptions\LyraNetworkException
     */
    public function testClientWrongKey()
    {
        new Client("wrongkey");
    }

    /**
     * ./vendor/bin/phpunit --filter testInvalidKey src/LyraNetwork/Tests/ClientTest.php
     */
    public function testInvalidKey()
    {
        $store = array("value" => "sdk test string value");
        
        $client = new Client("69876357:testprivatekey_FAKE");
        $response = $client->post('Charge/SDKTest', $store);

        $this->assertEquals("ERROR", $response["status"]);
        $this->assertEquals("INT_005", $response["answer"]["errorCode"]);
    }

    /**
     * ./vendor/bin/phpunit --filter testFileGetContentsInvalidKey src/LyraNetwork/Tests/ClientTest.php
     */
    public function testFileGetContentsInvalidKey()
    {
        $store = array("value" => "sdk test string value");
        
        $client = new Client("69876357:testprivatekey_FAKE");
        $response = $client->postWithFileGetContents('Charge/SDKTest', $store);

        $this->assertEquals("ERROR", $response["status"]);
        $this->assertEquals("INT_005", $response["answer"]["errorCode"]);
    }

    /**
     * ./vendor/bin/phpunit --filter testClientConfiguration src/LyraNetwork/Tests/ClientTest.php
     */
    public function testClientConfiguration()
    {
        $client = new Client("A:B");
        $client->setPublickey("33148340:testpublickey_l83P7WpRK2hoUIcWyFVQsd4Omsz0XbCKYtNKeGbpX6CvS");

        $this->assertEquals(Constants::SDK_VERSION, $client->getVersion());
        $this->assertEquals("33148340:testpublickey_l83P7WpRK2hoUIcWyFVQsd4Omsz0XbCKYtNKeGbpX6CvS", $client->getPublicKey());
    }

    /**
     * ./vendor/bin/phpunit --filter testFakeProxy src/LyraNetwork/Tests/ClientTest.php
     *
     * @expectedException LyraNetwork\Exceptions\LyraNetworkException
     */
    public function testFakeProxy()
    {
        $client = new Client("69876357:testprivatekey_DEMOPRIVATEKEY23G4475zXZQ2UA5x7M");
        $client->setTimeOuts(1,1);
        $client->setProxy('fake.host', 1234);

        $store = array("value" => "sdk test string value");
        $response = $client->post('Charge/SDKTest', $store);
    }

    /**
     * ./vendor/bin/phpunit --filter testInvalidAnswer src/LyraNetwork/Tests/ClientTest.php
     */
    public function testInvalidAnswer()
    {
        $client = new Client("69876357:testprivatekey_DEMOPRIVATEKEY23G4475zXZQ2UA5x7M");

        $store = "FAKE";
        $response = $client->post('Charge/SDKTest', $store);

        $this->assertEquals("ERROR", $response["status"]);
        $this->assertEquals("INT_002", $response["answer"]["errorCode"]);
    }
}