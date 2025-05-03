<?php

/**
 * Get a value from an array using dot notation
 *
 * @param array $array
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
function array_get($array, $key, $default = null)
{
    if (is_null($key)) {
        return $array;
    }
    
    foreach (explode('.', $key) as $segment) {
        if (!is_array($array) || !array_key_exists($segment, $array)) {
            return $default;
        }
        
        $array = $array[$segment];
    }
    
    return $array;
}

/**
 * Set a value in an array using dot notation
 *
 * @param array &$array
 * @param string $key
 * @param mixed $value
 * @return array
 */
function array_set(&$array, $key, $value)
{
    $keys = explode('.', $key);
    $current = &$array;
    
    foreach ($keys as $i => $key) {
        if (count($keys) === 1) {
            break;
        }
        
        unset($keys[$i]);
        
        if (!isset($current[$key]) || !is_array($current[$key])) {
            $current[$key] = [];
        }
        
        $current = &$current[$key];
    }
    
    $current[array_shift($keys)] = $value;
    
    return $array;
}

/**
 * Check if a key exists in an array using dot notation
 *
 * @param array $array
 * @param string $key
 * @return bool
 */
function array_has($array, $key)
{
    if (empty($array) || is_null($key)) {
        return false;
    }
    
    if (array_key_exists($key, $array)) {
        return true;
    }
    
    foreach (explode('.', $key) as $segment) {
        if (!is_array($array) || !array_key_exists($segment, $array)) {
            return false;
        }
        
        $array = $array[$segment];
    }
    
    return true;
}

/**
 * Remove a key from an array using dot notation
 *
 * @param array &$array
 * @param string $key
 * @return void
 */
function array_forget(&$array, $key)
{
    $keys = explode('.', $key);
    $current = &$array;
    
    while (count($keys) > 1) {
        $key = array_shift($keys);
        
        if (!isset($current[$key]) || !is_array($current[$key])) {
            return;
        }
        
        $current = &$current[$key];
    }
    
    unset($current[array_shift($keys)]);
}

/**
 * Get the first element of an array
 *
 * @param array $array
 * @param mixed $default
 * @return mixed
 */
function array_first($array, $default = null)
{
    if (empty($array)) {
        return $default;
    }
    
    return reset($array);
}

/**
 * Get the last element of an array
 *
 * @param array $array
 * @param mixed $default
 * @return mixed
 */
function array_last($array, $default = null)
{
    if (empty($array)) {
        return $default;
    }
    
    return end($array);
}

/**
 * Filter an array using a callback
 *
 * @param array $array
 * @param callable $callback
 * @return array
 */
function array_where($array, $callback)
{
    return array_filter($array, $callback, ARRAY_FILTER_USE_BOTH);
}

/**
 * Pluck a list of values from an array
 *
 * @param array $array
 * @param string $key
 * @param string|null $index
 * @return array
 */
function array_pluck($array, $key, $index = null)
{
    $result = [];
    
    foreach ($array as $item) {
        if (isset($item[$key])) {
            if ($index !== null && isset($item[$index])) {
                $result[$item[$index]] = $item[$key];
            } else {
                $result[] = $item[$key];
            }
        }
    }
    
    return $result;
}