<?php
namespace OLX\FluentHttpClient\Response;

use GuzzleHttp\Psr7\Response as GuzzleResponse;

class ResponseHolderTest extends \PHPUnit_Framework_TestCase
{
    public function testGetExistingHeader()
    {
        $response = $this->getMockBuilder(GuzzleResponse::class)->disableOriginalConstructor()->getMock();
        $response->method('getStatusCode')->willReturn(200);
        $response->method('getHeader')->willReturnCallback(function ($key) {
            $arr = ['foo' => 'bar', 'baz' => 'bazinga'];
            return $arr[$key];
        });

        $holder = new ResponseHolder($response);

        $this->assertEquals('bar', $holder->getHeader('foo'));
    }

    public function testGetNotExistingHeader()
    {
        $response = $this->getMockBuilder(GuzzleResponse::class)
            ->disableOriginalConstructor()
            ->setMethods(['getContents', 'getBody', 'getStatusCode'])
            ->getMock();

        $response->method('getStatusCode')->willReturn(200);
        $response->method('getContents')->willReturn(['foo' => 'bar']);
        $response->method('getBody')->willReturnSelf();

        $holder = new ResponseHolder($response);

        $this->assertFalse($holder->getHeader('I-Dont-Exist'));
    }

    public function testGetBody()
    {
        $response = $this->getMockBuilder(GuzzleResponse::class)
            ->disableOriginalConstructor()
            ->setMethods(['getContents', 'getBody'])
            ->getMock();

        $response->method('getContents')->willReturn('some string');
        $response->method('getBody')->willReturnSelf();

        $responseHolder = new ResponseHolder($response);
        $this->assertEquals('some string', $responseHolder->getBody());
    }

    public function testGetStatusCode()
    {
        $response = $this->getMockBuilder(GuzzleResponse::class)->disableOriginalConstructor()->getMock();
        $response->method('getStatusCode')->willReturn(123);

        $responseHolder = new ResponseHolder($response);
        $this->assertEquals(123, $responseHolder->getStatusCode());
    }
}
