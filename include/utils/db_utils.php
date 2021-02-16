<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

use SpiceCRM\includes\database\DBManagerFactory;


$toHTML = array(
    '"' => '&quot;',
    '<' => '&lt;',
    '>' => '&gt;',
    "'" => '&#039;',
);
$GLOBALS['toHTML_keys'] = array_keys($toHTML);
$GLOBALS['toHTML_values'] = array_values($toHTML);
$GLOBALS['toHTML_keys_set'] = implode("", $GLOBALS['toHTML_keys']);
/**
 * Replaces specific characters with their HTML entity values
 * @param string $string String to check/replace
 * @param bool $encode Default true
 * @return string
 *
 * @todo Make this utilize the external caching mechanism after re-testing (see
 *       log on r25320).
 *
 * Bug 49489 - removed caching of to_html strings as it was consuming memory and
 * never releasing it
 */
function to_html($string, $encode = true)
{
    if (empty($string)) {
        return $string;
    }

    global $toHTML;

    if ($encode && is_string($string)) {
        /*
         * cn: bug 13376 - handle ampersands separately
         * credit: ashimamura via bug portal
         */
        //$string = str_replace("&", "&amp;", $string);

        if (is_array($toHTML)) { // cn: causing errors in i18n test suite ($toHTML is non-array)
            $string = str_ireplace($GLOBALS['toHTML_keys'], $GLOBALS['toHTML_values'], $string);
        }
    }

    return $string;
}


/**
 * Replaces specific HTML entity values with the true characters
 * @param string $string String to check/replace
 * @param bool $encode Default true
 * @return string
 */
function from_html($string, $encode = true)
{
    if (!is_string($string) || !$encode) {
        return $string;
    }

    global $toHTML;
    static $toHTML_values = null;
    static $toHTML_keys = null;
    static $cache = array();
    if (!empty($toHTML) && is_array($toHTML) && (!isset($toHTML_values) || !empty($GLOBALS['from_html_cache_clear']))) {
        $toHTML_values = array_values($toHTML);
        $toHTML_keys = array_keys($toHTML);
    }

    // Bug 36261 - Decode &amp; so we can handle double encoded entities
    $string = str_ireplace("&amp;", "&", $string);

    if (!isset($cache[$string])) {
        $cache[$string] = str_ireplace($toHTML_values, $toHTML_keys, $string);
    }
    return $cache[$string];
}

/*
 * Return a version of $proposed that can be used as a column name in any of our supported databases
 * Practically this means no longer than 25 characters as the smallest identifier length for our supported DBs is 30 chars for Oracle plus we add on at least four characters in some places (for indices for example)
 * @param string $name Proposed name for the column
 * @param string $ensureUnique
 * @param int $maxlen Deprecated and ignored
 * @return string Valid column name trimmed to right length and with invalid characters removed
 */
function getValidDBName($name, $ensureUnique = false, $maxLen = 30)
{
    return DBManagerFactory::getInstance()->getValidDBName($name, $ensureUnique);
}

