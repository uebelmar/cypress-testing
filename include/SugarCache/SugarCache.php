<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

namespace SpiceCRM\includes\SugarCache;

use SpiceCRM\includes\SugarObjects\SpiceConfig;

/**
 * Sugar Cache manager
 * @api
 */
class SugarCache
{
    const EXTERNAL_CACHE_NULL_VALUE = "SUGAR_CACHE_NULL_ZZ";

    protected static $_cacheInstance;

    /**
     * @var true if the cache has been reset during this request, so we no longer return values from
     *      cache until the next reset
     */
    public static $isCacheReset = false;

    private function __construct()
    {
    }

    /**
     * initializes the cache in question
     *
     * either pulls the cache class to be used from the config or by default instantiates a memory cache
     */
    protected static function _init()
    {
        if (SpiceConfig::getInstance()->config['cache'] && SpiceConfig::getInstance()->config['cache']['class']) {
            $cacheClassFileName = SpiceConfig::getInstance()->config['cache']['class'];
            self::$_cacheInstance = new $cacheClassFileName();
        } else {
            self::$_cacheInstance = new SugarCacheMemory();
        }
    }

    /**
     * Returns the instance of the SugarCacheAbstract object, cooresponding to the external
     * cache being used.
     */
    public static function instance()
    {
        if (!is_subclass_of(self::$_cacheInstance, 'SugarCacheAbstract'))
            self::_init();

        return self::$_cacheInstance;
    }

    /**
     * Try to reset any opcode caches we know about
     *
     * @todo make it so developers can extend this somehow
     */
    public static function cleanOpcodes()
    {
    }

    /**
     * Try to reset file from caches
     */
    public static function cleanFile($file)
    {
        // APC
        if (function_exists('apc_delete_file') && ini_get('apc.stat') == 0) {
            apc_delete_file($file);
        }
    }

    public static function sugar_cache_put($key, $value, $ttl = null)
    {
        SugarCache::instance()->set($key, $value, $ttl);
    }

    public static function sugar_cache_retrieve($key)
    {
        return SugarCache::instance()->$key;
    }

    /**
     * moved into the class as static function
     * ToDo: check if this is still needed int his form
     *
     * @param $key
     */
    public static function sugar_cache_clear($key)
    {
        unset(self::instance()->$key);
    }
}
