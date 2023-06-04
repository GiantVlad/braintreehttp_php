<?php

namespace Test\Unit;

use BraintreeHttp\HttpRequest;
use BraintreeHttp\Serializer\Form;
use PHPUnit\Framework\TestCase;

class FormTest extends TestCase
{
    /**
     * @expectedException \Exception
     * @expectedExceptionMessage HttpRequest body must be an associative array when Content-Type is:
     */
    public function testFormThrowsWhenRequestBodyNotArray()
    {
        $this->expectException(\Exception::class);
        $multipart = new Form();

        $request = new HttpRequest("/", "POST");
        $request->body = "";
        $request->headers["Content-Type"] = "application/x-www-form-urlencoded";

        $multipart->encode($request);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage HttpRequest body must be an associative array when Content-Type is:
     */
    public function testFormThrowsWhenRequestBodyNotAssociativeArray()
    {
        $this->expectException(\Exception::class);
        $multipart = new Form();

        $body = [];
        $body[] = "form-param 1";
        $body[] = "form-param 2";

        $request = new HttpRequest("/", "POST");
        $request->body = $body;
        $request->headers["Content-Type"] = "application/x-www-form-urlencoded";

        $multipart->encode($request);
    }
}
