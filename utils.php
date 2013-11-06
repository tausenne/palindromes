<?php
/**
 * Formats a float to a desired style while keeping all of its precision.
 *
 * @param float $num the number to format
 * @param string $decimal_separator character to indicate a decimal point
 * @param string $thousands_separator characters to separate thousands
 *
 * @return string formatted number
 */
function number_format_all($num = 0.0, $decimal_separator = '.', $thousands_separator = ',') {
    $parts = explode('.', $num);
    return (number_format($parts[0], 0, $decimal_separator, $thousands_separator) . ".{$parts[1]}");
}

/**
 * Wrapper for printing log messages to screen. Adds date-time-timezone timestamp and
 * breaks the line afterwards.
 *
 * @param string $msg message to print
 */
function proc_log($msg = '') {
    echo '[' . date('Y-m-d H:i:s T') . "] {$msg}" . PHP_EOL;
}
?>
