<?php 
// ernicani/Routing/Response.php

namespace Ernicani\Routing;

class Response
{
    private $content;
    private $statusCode;
    private $headers;

    public function __construct($content, int $statusCode = 200, array $headers = [])
    {
        $this->content = $content;
        $this->statusCode = $statusCode;
        $this->headers = $headers;
    }

    public function send()
    {
        http_response_code($this->statusCode);

        foreach ($this->headers as $header => $value) {
            header("$header: $value");
        }

        echo $this->content;
    }

    public static function createJsonResponse($data, int $statusCode = 200, array $headers = [])
    {
        $headers['Content-Type'] = 'application/json';
        $content = json_encode($data);
        return new self($content, $statusCode, $headers);
    }
}
