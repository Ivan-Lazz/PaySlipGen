<?php

namespace Core;

class Response
{
    protected $statusCode = 200;
    protected $headers = [];
    
    public function setStatusCode($code)
    {
        $this->statusCode = $code;
        return $this;
    }
    
    public function setHeader($name, $value)
    {
        $this->headers[$name] = $value;
        return $this;
    }
    
    protected function sendHeaders()
    {
        http_response_code($this->statusCode);
        
        foreach ($this->headers as $name => $value) {
            header("{$name}: {$value}");
        }
    }
    
    public function json($data, $statusCode = null)
    {
        if ($statusCode !== null) {
            $this->setStatusCode($statusCode);
        }
        
        $this->setHeader('Content-Type', 'application/json');
        $this->sendHeaders();
        
        echo json_encode($data);
        exit;
    }
    
    public function send($content, $statusCode = null)
    {
        if ($statusCode !== null) {
            $this->setStatusCode($statusCode);
        }
        
        $this->sendHeaders();
        
        echo $content;
        exit;
    }
    
    public function redirect($url, $statusCode = 302)
    {
        $this->setStatusCode($statusCode);
        $this->setHeader('Location', $url);
        $this->sendHeaders();
        
        exit;
    }
    
    public static function success($data = [], $message = 'Success', $code = 200)
    {
        $response = new self();
        
        return $response->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], $code);
    }
    
    public static function error($message = 'Error', $code = 400, $errors = [])
    {
        $response = new self();
        
        return $response->json([
            'status' => 'error',
            'message' => $message,
            'errors' => $errors
        ], $code);
    }
}