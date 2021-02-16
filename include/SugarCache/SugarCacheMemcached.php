<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

namespace SpiceCRM\includes\SugarCache;

use Memcache;
use Memcached;
use SpiceCRM\includes\SugarObjects\SpiceConfig;

class SugarCacheMemcached extends SugarCacheAbstract
{
    /**
     * @var Memcache server name string
     */
    protected $_host = '127.0.0.1';
    
    /**
     * @var Memcache server port int
     */
    protected $_port = 11211;
    
    /**
     * @var Memcached object
     */
    protected $_memcached = '';
    
    /**
     * @see SugarCacheAbstract::$_priority
     */
    protected $_priority = 900;
     
    /**
     * @see SugarCacheAbstract::useBackend()
     */
    public function useBackend()
    {
        if ( extension_loaded('memcached')
                && empty(SpiceConfig::getInstance()->config['external_cache_disabled_memcached'])
                && $this->_getMemcachedObject() )
            return true;
            
        return false;
    }
    
    /**
     * @see SugarCacheAbstract::__construct()
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Get the memcached object; initialize if needed
     */
    protected function _getMemcachedObject()
    {
        if ( !($this->_memcached instanceOf Memcached) ) {
            $this->_memcached = new Memcached();
            $this->_host = SpiceConfig::getInstance()->get('external_cache.memcache.host', $this->_host);
            $this->_port = SpiceConfig::getInstance()->get('external_cache.memcache.port', $this->_port);
            if ( !@$this->_memcached->addServer($this->_host,$this->_port) ) {
                return false;
            }
        }
        
        return $this->_memcached;
    }
    
    /**
     * @see SugarCacheAbstract::_setExternal()
     */
    protected function _setExternal(
        $key,
        $value
        )
    {
        $this->_getMemcachedObject()->set($key, $value, $this->_expireTimeout);
    }
    
    /**
     * @see SugarCacheAbstract::_getExternal()
     */
    protected function _getExternal(
        $key
        )
    {
        $returnValue = $this->_getMemcachedObject()->get($key);
        if ( $this->_getMemcachedObject()->getResultCode() != Memcached::RES_SUCCESS ) {
            return null;
        }

        return $returnValue;
    }
    
    /**
     * @see SugarCacheAbstract::_clearExternal()
     */
    protected function _clearExternal(
        $key
        )
    {
        $this->_getMemcachedObject()->delete($key);
    }
    
    /**
     * @see SugarCacheAbstract::_resetExternal()
     */
    protected function _resetExternal()
    {
        $this->_getMemcachedObject()->flush();
    }
}
