<?php

/**
 * Convert a string to snake_case
 *
 * @param string $string
 * @return string
 */
function snake_case($string)
{
    // Replace non-alphanumeric characters with spaces
    $string = preg_replace('/[^a-zA-Z0-9]/', ' ', $string);
    
    // Convert to lowercase
    $string = strtolower($string);
    
    // Replace spaces with underscores
    $string = preg_replace('/\s+/', '_', $string);
    
    // Remove leading and trailing underscores
    return trim($string, '_');
}

/**
 * Convert a string to camelCase
 *
 * @param string $string
 * @return string
 */
function camel_case($string)
{
    // First convert to snake_case
    $string = snake_case($string);
    
    // Then convert to camelCase
    $string = lcfirst(str_replace('_', '', ucwords($string, '_')));
    
    return $string;
}

/**
 * Convert a string to PascalCase
 *
 * @param string $string
 * @return string
 */
function pascal_case($string)
{
    // First convert to snake_case
    $string = snake_case($string);
    
    // Then convert to PascalCase
    $string = str_replace('_', '', ucwords($string, '_'));
    
    return $string;
}

/**
 * Convert a string to kebab-case
 *
 * @param string $string
 * @return string
 */
function kebab_case($string)
{
    // First convert to snake_case
    $string = snake_case($string);
    
    // Then replace underscores with hyphens
    $string = str_replace('_', '-', $string);
    
    return $string;
}

/**
 * Truncate a string to a specified length
 *
 * @param string $string
 * @param int $length
 * @param string $append
 * @return string
 */
function str_truncate($string, $length, $append = '...')
{
    if (mb_strlen($string) <= $length) {
        return $string;
    }
    
    return mb_substr($string, 0, $length) . $append;
}

/**
 * Check if a string starts with a given substring
 *
 * @param string $haystack
 * @param string $needle
 * @return bool
 */
function str_starts_on($haystack, $needle)
{
    if ($needle === '') {
        return true;
    }
    
    return strpos($haystack, $needle) === 0;
}

/**
 * Check if a string ends with a given substring
 *
 * @param string $haystack
 * @param string $needle
 * @return bool
 */
function str_ends_on($haystack, $needle)
{
    if ($needle === '') {
        return true;
    }
    
    return substr($haystack, -strlen($needle)) === $needle;
}

/**
 * Check if a string contains a given substring
 *
 * @param string $haystack
 * @param string $needle
 * @return bool
 */
function str_contain($haystack, $needle)
{
    if ($needle === '') {
        return true;
    }
    
    return strpos($haystack, $needle) !== false;
}

/**
 * Generate a random string
 *
 * @param int $length
 * @param string $characters
 * @return string
 */
function str_random($length = 16, $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
{
    $string = '';
    $max = strlen($characters) - 1;
    
    for ($i = 0; $i < $length; $i++) {
        $string .= $characters[mt_rand(0, $max)];
    }
    
    return $string;
}