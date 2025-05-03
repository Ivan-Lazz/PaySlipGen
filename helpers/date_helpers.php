<?php

/**
 * Format a date
 *
 * @param string|int|\DateTime $date
 * @param string $format
 * @return string
 */
function date_format_custom($date, $format = 'Y-m-d H:i:s')
{
    if (is_numeric($date)) {
        // Unix timestamp
        return date($format, $date);
    } elseif (is_string($date)) {
        // String date
        return date($format, strtotime($date));
    } elseif ($date instanceof \DateTime) {
        // DateTime object
        return $date->format($format);
    }
    
    return date($format);
}

/**
 * Get the difference between two dates in seconds
 *
 * @param string|int|\DateTime $date1
 * @param string|int|\DateTime $date2
 * @return int
 */
function date_diff_seconds($date1, $date2)
{
    return strtotime(date_format_custom($date1)) - strtotime(date_format_custom($date2));
}

/**
 * Get the difference between two dates in days
 *
 * @param string|int|\DateTime $date1
 * @param string|int|\DateTime $date2
 * @return int
 */
function date_diff_days($date1, $date2)
{
    return floor(date_diff_seconds($date1, $date2) / 86400);
}

/**
 * Get the difference between two dates in months
 *
 * @param string|int|\DateTime $date1
 * @param string|int|\DateTime $date2
 * @return int
 */
function date_diff_months($date1, $date2)
{
    $date1 = new \DateTime(date_format_custom($date1, 'Y-m-d'));
    $date2 = new \DateTime(date_format_custom($date2, 'Y-m-d'));
    
    $diff = $date1->diff($date2);
    
    return ($diff->y * 12) + $diff->m;
}

/**
 * Get the difference between two dates in years
 *
 * @param string|int|\DateTime $date1
 * @param string|int|\DateTime $date2
 * @return int
 */
function date_diff_years($date1, $date2)
{
    $date1 = new \DateTime(date_format_custom($date1, 'Y-m-d'));
    $date2 = new \DateTime(date_format_custom($date2, 'Y-m-d'));
    
    $diff = $date1->diff($date2);
    
    return $diff->y;
}

/**
 * Check if a date is in the past
 *
 * @param string|int|\DateTime $date
 * @return bool
 */
function date_is_past($date)
{
    return strtotime(date_format_custom($date)) < time();
}

/**
 * Check if a date is in the future
 *
 * @param string|int|\DateTime $date
 * @return bool
 */
function date_is_future($date)
{
    return strtotime(date_format_custom($date)) > time();
}

/**
 * Get the start of a day
 *
 * @param string|int|\DateTime $date
 * @return string
 */
function date_start_of_day($date)
{
    return date_format_custom($date, 'Y-m-d 00:00:00');
}

/**
 * Get the end of a day
 *
 * @param string|int|\DateTime $date
 * @return string
 */
function date_end_of_day($date)
{
    return date_format_custom($date, 'Y-m-d 23:59:59');
}

/**
 * Get the start of a month
 *
 * @param string|int|\DateTime $date
 * @return string
 */
function date_start_of_month($date)
{
    return date_format_custom($date, 'Y-m-01 00:00:00');
}

/**
 * Get the end of a month
 *
 * @param string|int|\DateTime $date
 * @return string
 */
function date_end_of_month($date)
{
    return date_format_custom($date, 'Y-m-t 23:59:59');
}