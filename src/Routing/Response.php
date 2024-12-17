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

    
    public static function createJsonResponse($data, int $statusCode = 200)
    {
        header('Content-Type: application/json', true, $statusCode);
        echo json_encode(self::serialize($data));
        exit;
    }

    private static function serialize($data)
    {
        if (is_array($data)) {
            $result = [];
            foreach ($data as $item) {
                $result[] = self::serializeEntity($item);
            }
            return $result;
        } elseif (is_object($data)) {
            return self::serializeEntity($data);
        } else {
            return $data;
        }
    }

    private static function serializeEntity($entity)
    {
        if (!is_object($entity)) {
            return $entity;
        }

        $data = [];
        $reflectionClass = new \ReflectionClass($entity);
    
        foreach ($reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            if (strpos($method->getName(), 'get') === 0 && $method->getNumberOfParameters() === 0) {
                $property = lcfirst(substr($method->getName(), 3));
                if ($property === 'password') {
                    continue; // Exclure le mot de passe
                }
                $value = $method->invoke($entity);
    
                if ($value instanceof \DateTimeInterface) {
                    $value = $value->format('Y-m-d H:i:s');
                }
    
                $data[$property] = $value;
            }
        }
    
        return $data;
    }
    

}
