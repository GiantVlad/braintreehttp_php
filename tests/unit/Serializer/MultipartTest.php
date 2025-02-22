<?php

namespace Test\Unit;

use BraintreeHttp\HttpRequest;
use BraintreeHttp\Serializer\Multipart;
use BraintreeHttp\Serializer\FormPart;
use PHPUnit\Framework\TestCase;

class MultipartTest extends TestCase
{

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage HttpRequest body must be an associative array when Content-Type is:
     */
    public function testMultipartThrowsWhenRequestBodyNotArray()
    {
        $this->expectException(\Exception::class);
        $multipart = new Multipart();

        $request = new HttpRequest("/", "POST");
        $request->body = "";
        $request->headers["Content-Type"] = "multipart/form-data";

        $multipart->encode($request);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage HttpRequest body must be an associative array when Content-Type is:
     */
    public function testMultipartThrowsWhenRequestBodyNotAssociativeArray()
    {
        $this->expectException(\Exception::class);
        $multipart = new Multipart();

        $body = [];
        $body[] = "form-param 1";
        $body[] = "form-param 2";

        $request = new HttpRequest("/", "POST");
        $request->body = $body;
        $request->headers["Content-Type"] = "multipart/form-data";

        $multipart->encode($request);
    }

    public function testMultipartAddsContentTypeHeaderForPart()
    {
        $multipart = new Multipart();

        $body = [];
        $body["key1"] = "value1";

        $request = new HttpRequest("/", "POST");
        $request->body = $body;
        $request->headers["Content-Type"] = "multipart/form-data";

        $encodedBody = $multipart->encode($request);
        $this->assertStringContainsString("boundary=", $request->headers["Content-Type"]);
        $this->assertStringContainsString("Content-Disposition: form-data; name=\"key1\"\r\n\r\nvalue1\r\n", $encodedBody);
    }

    public function testMultipartMultipleKeys()
    {
        $multipart = new Multipart();

        $body = [];
        $body["key1"] = "value1";
        $body["key2"] = "value2";

        $request = new HttpRequest("/", "POST");
        $request->body = $body;
        $request->headers["Content-Type"] = "multipart/form-data";

        $encodedBody = $multipart->encode($request);
        $this->assertStringContainsString("boundary=", $request->headers["Content-Type"]);
        $this->assertStringContainsString("Content-Disposition: form-data; name=\"key1\"\r\n\r\nvalue1\r\n", $encodedBody);
        $this->assertStringContainsString("Content-Disposition: form-data; name=\"key2\"\r\n\r\nvalue2\r\n", $encodedBody);
    }

    public function testMultipartJSONPart()
    {
        $multipart = new Multipart();

        $body = [];
        $formPart = new FormPart([ "json_key" => "json_value"], [ "Content-Type" => "application/json"]);
        $body["key1"] = $formPart;

        $request = new HttpRequest("/", "POST");
        $request->body = $body;
        $request->headers["Content-Type"] = "multipart/form-data";

        $encodedBody = $multipart->encode($request);
        $this->assertStringContainsString("boundary=", $request->headers["Content-Type"]);
        $this->assertStringContainsString("Content-Disposition: form-data; name=\"key1\"; filename=\"key1.json\"\r\nContent-Type: application/json\r\n\r\n{\"json_key\":\"json_value\"}\r\n", $encodedBody);
    }

    public function testMultipartFile()
    {
        $multipart = new Multipart();

        $body = [];
        $file = fopen(dirname(__DIR__) . '/Serializer/sample.txt', 'rb');
        $body["file1"] = $file;

        $request = new HttpRequest("/", "POST");
        $request->body = $body;
        $request->headers["Content-Type"] = "multipart/form-data";

        $encodedBody = $multipart->encode($request);
        $this->assertStringContainsString("boundary=", $request->headers["Content-Type"]);
        $this->assertStringContainsString("Content-Disposition: form-data; name=\"file1\"; filename=\"sample.txt\"\r\nContent-Type: text/plain\r\n\r\nHello World!\n\r\n", $encodedBody);
    }
}
