<?php

namespace Core;

abstract class Controller
{
    protected $db;
    
    public function __construct($db = null)
    {
        $this->db = $db ?: app('db')->getConnection();
    }
    
    protected function request()
    {
        return app('request');
    }
    
    protected function response()
    {
        return app('response');
    }
    
    protected function getInput()
    {
        return $this->request()->getJson();
    }
    
    protected function validate(array $data, array $rules)
    {
        $validator = new Validator($data, $rules);
        
        if (!$validator->passes()) {
            return $validator->errors();
        }
        
        return null;
    }
    
    protected function success($data = [], $message = 'Success', $code = 200)
    {
        return $this->response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], $code);
    }
    
    protected function error($message = 'Error', $code = 400, $errors = [])
    {
        return $this->response()->json([
            'status' => 'error',
            'message' => $message,
            'errors' => $errors
        ], $code);
    }
}