<?php

namespace Core;

class Validator
{
    protected $data;
    protected $rules;
    protected $errors = [];
    protected $customMessages = [];
    
    /**
     * Create a new validator instance
     *
     * @param array $data
     * @param array $rules
     * @param array $customMessages
     */
    public function __construct(array $data, array $rules, array $customMessages = [])
    {
        $this->data = $data;
        $this->rules = $rules;
        $this->customMessages = $customMessages;
        $this->validate();
    }
    
    /**
     * Validate the data
     *
     * @return void
     */
    protected function validate()
    {
        foreach ($this->rules as $field => $fieldRules) {
            foreach ($fieldRules as $rule) {
                $this->applyRule($field, $rule);
            }
        }
    }
    
    /**
     * Apply a validation rule to a field
     *
     * @param string $field
     * @param mixed $rule
     * @return void
     */
    protected function applyRule($field, $rule)
    {
        // Skip validation if field doesn't exist and rule is not 'required'
        if (!isset($this->data[$field]) && $rule !== 'required') {
            return;
        }
        
        // Get rule name and parameters
        $ruleName = is_array($rule) ? $rule[0] : $rule;
        $params = is_array($rule) ? array_slice($rule, 1) : [];
        
        // Call the appropriate validation method
        $method = 'validate' . ucfirst($ruleName);
        
        if (method_exists($this, $method)) {
            $result = $this->$method($field, $params);
            
            if ($result === false) {
                $this->addError($field, $ruleName, $params);
            }
        }
    }
    
    /**
     * Add an error message
     *
     * @param string $field
     * @param string $rule
     * @param array $params
     * @return void
     */
    protected function addError($field, $rule, $params = [])
    {
        $key = "{$field}.{$rule}";
        
        if (isset($this->customMessages[$key])) {
            $message = $this->customMessages[$key];
        } else {
            $message = $this->getDefaultMessage($field, $rule, $params);
        }
        
        $this->errors[$field][] = $message;
    }
    
    /**
     * Get the default error message for a rule
     *
     * @param string $field
     * @param string $rule
     * @param array $params
     * @return string
     */
    protected function getDefaultMessage($field, $rule, $params)
    {
        $fieldName = ucfirst(str_replace('_', ' ', $field));
        
        switch ($rule) {
            case 'required':
                return "{$fieldName} is required";
            case 'email':
                return "{$fieldName} must be a valid email address";
            case 'numeric':
                return "{$fieldName} must be a number";
            case 'string':
                return "{$fieldName} must be a string";
            case 'min':
                return "{$fieldName} must be at least {$params[0]}";
            case 'max':
                return "{$fieldName} may not exceed {$params[0]}";
            case 'in':
                return "{$fieldName} must be one of: " . implode(', ', $params);
            case 'date':
                return "{$fieldName} must be a valid date";
            default:
                return "{$fieldName} is invalid";
        }
    }
    
    /**
     * Check if validation passes
     *
     * @return bool
     */
    public function passes()
    {
        return empty($this->errors);
    }
    
    /**
     * Check if validation fails
     *
     * @return bool
     */
    public function fails()
    {
        return !$this->passes();
    }
    
    /**
     * Get the validation errors
     *
     * @return array
     */
    public function errors()
    {
        return $this->errors;
    }
    
    /**
     * Validate required rule
     *
     * @param string $field
     * @param array $params
     * @return bool
     */
    protected function validateRequired($field, $params)
    {
        return isset($this->data[$field]) && $this->data[$field] !== '';
    }
    
    /**
     * Validate email rule
     *
     * @param string $field
     * @param array $params
     * @return bool
     */
    protected function validateEmail($field, $params)
    {
        return filter_var($this->data[$field], FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Validate numeric rule
     *
     * @param string $field
     * @param array $params
     * @return bool
     */
    protected function validateNumeric($field, $params)
    {
        return is_numeric($this->data[$field]);
    }
    
    /**
     * Validate string rule
     *
     * @param string $field
     * @param array $params
     * @return bool
     */
    protected function validateString($field, $params)
    {
        return is_string($this->data[$field]);
    }
    
    /**
     * Validate min rule
     *
     * @param string $field
     * @param array $params
     * @return bool
     */
    protected function validateMin($field, $params)
    {
        $value = $this->data[$field];
        $min = $params[0];
        
        if (is_numeric($value)) {
            return $value >= $min;
        }
        
        return mb_strlen($value) >= $min;
    }
    
    /**
     * Validate max rule
     *
     * @param string $field
     * @param array $params
     * @return bool
     */
    protected function validateMax($field, $params)
    {
        $value = $this->data[$field];
        $max = $params[0];
        
        if (is_numeric($value)) {
            return $value <= $max;
        }
        
        return mb_strlen($value) <= $max;
    }
    
    /**
     * Validate in rule
     *
     * @param string $field
     * @param array $params
     * @return bool
     */
    protected function validateIn($field, $params)
    {
        return in_array($this->data[$field], $params);
    }
    
    /**
     * Validate date rule
     *
     * @param string $field
     * @param array $params
     * @return bool
     */
    protected function validateDate($field, $params)
    {
        $format = isset($params[0]) ? $params[0] : 'Y-m-d';
        $date = \DateTime::createFromFormat($format, $this->data[$field]);
        
        return $date && $date->format($format) === $this->data[$field];
    }
}