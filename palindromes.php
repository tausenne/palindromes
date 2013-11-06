<?php
require_once('utils.php');
/**
 * All of this should be done properly as a class, but procedural was faster to code.
 * The input file must be located in the same directory as the script.
 */

/**
 * Gets input from a string. A string may be either a filename or a text string.
 * Existence of filename overrides a plain string.
 *
 * @param string $str the input string or filename
 *
 * @return string contents of the file/input string, stripped of all non-alpha characters
 */
function getInput($str = '') {
    $cur_dir = dirname(__FILE__);
    if (file_exists("{$cur_dir}/{$str}")) {
        $input = file_get_contents("{$cur_dir}/{$str}");
    }

    else {
        $input = $str;
    }

    return preg_replace('/[^a-zA-Z]+/', '', $input); // throw out everything non-alpha
}

/**
 * Finds palindromic substrings, either in the given string or in the given file.
 * If the argument is an existing file, that takes precedence. Can be called
 * recursively or iteratively.
 *
 * @param string $str the input string or filename
 * @param bool $recur whether to execute recursively. Will determine which function
 *                    to call
 */
function findPalindromes($str = '', $method = '') {
    $str = getInput($str);
    switch ($method) {
        case 'recursive':
            $func = 'isPalindromeRecursive';
            break;

        case 'half':
            $func = 'isPalindromeHalf';
            break;

        default:
            $func = 'isPalindrome';
    }

    $pals = array();
    for ($i = 0; $i <= strlen($str) - 1; ++$i) {
        for ($len = 1; $len <= (strlen($str) - $i); ++$len) {
            $partword = substr($str, $i, $len);
            if ($func($partword)) {
                $pals[] = $partword;
            }
        }
    }

    // write the results in a file. create/overwrite the file anew every time
    if (!empty($pals)) {
        $fh = fopen(dirname(__FILE__) . '/palindrome_results.txt', 'w');
        fwrite($fh, implode(',', $pals));
        fclose($fh);
    }
}

/**
 * Iterative implementation of determining whether a string is a palindrome.
 *
 * @param string $str string to validate palindromeness of
 *
 * @return bool true for palindromes, false for non-palindromes
 */
function isPalindrome($str = '') {
    $wordlength = strlen($str);
    switch ($wordlength) {
        case 1:
            return true;
            break;

        case 2:
        case 3:
            return ($str[0] == $str[$wordlength - 1]);
            break;

        default:
            $isPalindrome = true;
            $i = 0;
            $len = $wordlength;
            while ($i <= floor($wordlength / 2) && $len > 0) {
                $part = substr($str, $i, $len);
                if ($part[0] != $part[strlen($part) - 1]) {
                    $isPalindrome = false;
                    break;
                }

                $len -= 2;
                ++$i;
            }

            return $isPalindrome;
    }
}

/**
 * Determines whether a string is a palindrome by cutting it in half and
 * comparing the first half to the reverse of the second half.
 *
 * @param string $str string to validate palindromeness of
 *
 * @return bool true for palindromes, false for non-palindromes
 */
function isPalindromeHalf($str = '') {
    $wordlength = strlen($str);
    switch ($wordlength) {
        case 1:
            return true;
            break;

        case 2:
        case 3:
            return ($str[0] == $str[$wordlength - 1]);
            break;

        default:
            $half = floor($wordlength / 2);
            $first_half = substr($str, 0, $half);
            $second_half_start = ($wordlength % 2) ? ($half + 1) : $half; // if it's an odd-length word, second half starts AFTER the middle character
            $second_half_reversed = strrev(substr($str, $second_half_start, $wordlength));

            return ($first_half == $second_half_reversed);
    }
}

/**
 * Recursive implementation of determining whether a string is a palindrome.
 *
 * @param string $str string to validate palindromeness of
 *
 * @return bool true for palindromes, false for non-palindromes
 */
function isPalindromeRecursive($str = '') {
    $wordlength = strlen($str);
    switch ($wordlength) {
        case 1:
            return true;
            break;

        case 2:
        case 3:
            return ($str[0] == $str[$wordlength - 1]);
            break;

        default:
            if ($str[0] == $str[$wordlength - 1]) {
                return isPalindromeRecursive(substr($str, 1, $wordlength - 2));
            }

            else {
                return false;
            }
    }
}

date_default_timezone_set('UTC'); // don't use system time, stick with UTC for consistency's sake

if (empty($argv[1])) {
    proc_log('Please supply an input.');
    die();
}

elseif ($argv[1] == '--help') {
    $help = <<<HELP
You can run the script in the following ways:
            php palindromes.php kayak
            php palindromes.php kayak method=recursive
            php palindromes.php kayak method=half
            php palindromes.php very_long_palindrome.txt
            php palindromes.php very_long_palindrome.txt method=recursive
            php palindromes.php very_long_palindrome.txt method=half

The input file must be located in the same directory as the original script.

The results will be written to a file called palindrome_results.txt, located in the same directory as the
original script. You must run the script as a user that has read and write permissions to this directory.
HELP;

    proc_log($help);
}

else {
    if (isset($argv[2]) &&
        !empty($argv[2]) &&
        strpos($argv[2], '=') !== false) {
            $parts = explode('=', $argv[2]);
            if ($parts[0] == 'method') {
                $method = $parts[1];
            }
    }

    else {
        $method = '';
    }

    $start = microtime(true);
    findPalindromes($argv[1], $method);
    $end = microtime(true);
    $diff = number_format_all($end - $start);

    proc_log("the whole process took {$diff} s");
}
?>
