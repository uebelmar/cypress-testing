<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

namespace SpiceCRM\includes\utils;

use ArrayObject;

/**
 * Wrapper around PHP's ArrayObject class that provides dot-notation recursive searching
 * for multi-dimensional arrays
 */
class SugarArray extends ArrayObject
{
    /**
     * Return the value matching $key if exists, otherwise $default value
     *
     * This method uses dot notation to look through multi-dimensional arrays
     *
     * @param string $key key to look up
     * @param mixed $default value to return if $key does not exist
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return $this->_getFromSource($key, $default);
    }

    /**
     * Provided as a convinience method for fetching a value within an existing
     * array without instantiating a SugarArray
     *
     * NOTE: This should only used where refactoring an array into a SugarArray
     *       is unfeasible.  This operation is more expensive than a direct
     *       SugarArray as each time it creates and throws away a new instance
     *
     * @param array $haystack haystack
     * @param string $needle needle
     * @param mixed $default default value to return
     * @return mixed
     */
    static public function staticGet($haystack, $needle, $default = null)
    {
        if (empty($haystack)) {
            return $default;
        }
        $array = new self($haystack);
        return $array->get($needle, $default);
    }

    private function _getFromSource($key, $default)
    {
        if (strpos($key, '.') === false) {
            return isset($this[$key]) ? $this[$key] : $default;
        }

        $exploded = explode('.', $key);
        $current_key = array_shift($exploded);
        return $this->_getRecursive($this->_getFromSource($current_key, $default), $exploded, $default);
    }

    private function _getRecursive($raw_config, $children, $default)
    {
        if ($raw_config === $default) {
            return $default;
        } elseif (count($children) == 0) {
            return $raw_config;
        } else {
            $next_key = array_shift($children);
            return isset($raw_config[$next_key]) ?
                $this->_getRecursive($raw_config[$next_key], $children, $default) :
                $default;
        }
    }
}
