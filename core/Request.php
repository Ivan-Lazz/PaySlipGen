<?php

namespace Core;

class Request
{
    protected $get;
    protected $post;
    protected $json;
    protected $files;
    protected $server;
    protected $headers;
    protected $method;
    protected $uri;
    protected $path;
    public $user = null;
    
    public function __construct()
    {
        $this->get = $_GET ?? [];
        $this->post = $_POST ?? [];
        $this->files = $_FILES ?? [];
        $this->server = $_SERVER ?? [];
        $this->method = $this->server['REQUEST_METHOD'] ?? 'GET';
        $this->uri = $this->server['REQUEST_URI'] ?? '/';
        $this->path = parse_url($this->uri, PHP_URL_PATH);
        
        // Parse JSON input for non-GET requests with JSON content type
        $contentType = $this->getHeader('Content-Type');
        
        if ($this->method !== 'GET' && strpos($contentType, 'application/json') !== false) {
            $input = file_get_contents('php://input');
            $this->json = json_decode($input, true) ?? [];
        }
        
        // Parse headers
        $this->parseHeaders();
    }
    
    protected function parseHeaders()
    {
        $this->headers = [];
        
        foreach ($this->server as $key => $value) {
            if (strpos($key, 'HTTP_') === 0) {
                $name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($key, 5)))));
                $this->headers[$name] = $value;
            } elseif (in_array($key, ['CONTENT_TYPE', 'CONTENT_LENGTH'])) {
                $name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', $key))));
                $this->headers[$name] = $value;
            }
        }
    }
    
    public function getHeader($name, $default = null)
    {
        return $this->headers[$name] ?? $default;
    }
    
    public function getMethod()
    {
        return $this->method;
    }
    
    public function getPath()
    {
        return $this->path;
    }
    
    public function get($key = null, $default = null)
    {
        if ($key === null) {
            return $this->get;
        }
        
        return $this->get[$key] ?? $default;
    }
    
    public function post($key = null, $default = null)
    {
        if ($key === null) {
            return $this->post;
        }
        
        return $this->post[$key] ?? $default;
    }
    
    public function getJson($key = null, $default = null)
    {
        if ($key === null) {
            return $this->json ?? [];
        }
        
        return $this->json[$key] ?? $default;
    }
    
    public function all()
    {
        return array_merge($this->get, $this->post, $this->json ?? []);
    }
    
    public function input($key = null, $default = null)
    {
        $data = $this->all();
        
        if ($key === null) {
            return $data;
        }
        
        return $data[$key] ?? $default;
    }
    
    public function only(array $keys)
    {
        return array_intersect_key($this->all(), array_flip($keys));
    }
    
    public function file($key = null)
    {
        if ($key === null) {
            return $this->files;
        }
        
        return $this->files[$key] ?? null;
    }
    
    public function hasFile($key)
    {
        return isset($this->files[$key]) && $this->files[$key]['error'] !== UPLOAD_ERR_NO_FILE;
    }
    
    public function isMethod($method)
    {
        return strtoupper($this->method) === strtoupper($method);
    }
    
    public function isJson()
    {
        $contentType = $this->getHeader('Content-Type');
        return strpos($contentType, 'application/json') !== false;
    }
    
    public function expectsJson()
    {
        return $this->isJson() || $this->getHeader('Accept') === 'application/json';
    }
}