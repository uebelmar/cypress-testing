<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

namespace SpiceCRM\data\Relationships;

use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\Logger\LoggerManager;
use SpiceCRM\includes\SugarObjects\SpiceConfig;
use SpiceCRM\includes\SugarObjects\VardefManager;
use SpiceCRM\includes\SpiceDictionary\SpiceDictionaryVardefs;

/**
 * Create relationship objects
 * @api
 */
class SugarRelationshipFactory {
    static $rfInstance;

    protected $relationships;

    protected function __construct(){
        //Load the relationship definitions from the cache.
        $this->loadRelationships();
    }

    /**
     * @static
     * @return SugarRelationshipFactory
     */
    public static function getInstance()
    {
        if (is_null(self::$rfInstance))
            self::$rfInstance = new SugarRelationshipFactory();
        return self::$rfInstance;
    }

    public static function rebuildCache()
    {
        self::getInstance()->buildRelationshipCache();
    }

    public static function deleteCache()
    {
        $file = self::getInstance()->getCacheFile();
        if(sugar_is_file($file))
        {
            unlink($file);
        }
    }

    /**
     * @param  $relationshipName String name of relationship to load
     * @return void
     *
     *
     *
     */
    public function getRelationship($relationshipName)
    {
        if (empty($this->relationships[$relationshipName])) {
            LoggerManager::getLogger()->error("Unable to find relationship $relationshipName");
            return false;
        }

        $def = $this->relationships[$relationshipName];

        $type = isset($def['true_relationship_type']) ? $def['true_relationship_type'] : $def['relationship_type'];
        switch($type)
        {
            case "many-to-many":
                if (isset($def['rhs_module']) && $def['rhs_module'] == 'EmailAddresses')
                {
                    return new EmailAddressRelationship($def);
                }
                return new M2MRelationship($def);
            case "one-to-many":
                //If a relationship has no table or join keys, it must be bean based
                if (empty($def['true_relationship_type']) || (empty($def['table']) && empty($def['join_table'])) || empty($def['join_key_rhs'])){
                    return new One2MBeanRelationship($def);
                }
                else {
                    return new One2MRelationship($def);
                }
            case "one-to-one":
                if (empty($def['true_relationship_type'])){
                    return new One2OneBeanRelationship($def);
                }
                else {
                    return new One2OneRelationship($def);
                }
        }

        LoggerManager::getLogger()->fatal ("$relationshipName had an unknown type $type ");

        return false;
    }

    public function getRelationshipDef($relationshipName)
    {
        if (empty($this->relationships[$relationshipName])) {
            LoggerManager::getLogger()->error("Unable to find relationship $relationshipName");
            return false;
        }

        return $this->relationships[$relationshipName];
    }


    protected function loadRelationships()
    {
        if(isset(SpiceConfig::getInstance()->config['systemvardefs']['dictionary']) && SpiceConfig::getInstance()->config['systemvardefs']['dictionary']){
            $this->loadRelationshipsFromDb();
        }
        else{
            $this->loadRelationshipsFromCache();
        }
    }

    private function loadRelationshipsFromDb(){
        $this->relationships = SpiceDictionaryVardefs::loadRelationships();
    }

    /**
     * load relationships from cache
     */
    private function loadRelationshipsFromCache(){
        
        if(isset(SpiceConfig::getInstance()->config['systemvardefs']['dictionary']) && SpiceConfig::getInstance()->config['systemvardefs']['dictionary']){
            $this->loadRelationshipsCacheFromDb();
        } else {
            $this->loadRelationshipsCacheFromFiles();
        }
    }

    /**
     * load relationships from cache table
     * @param string $module filter on module
     * @return void
     */
    private function loadRelationshipsCacheFromDb($module = null){
        $relationships = SpiceDictionaryVardefs::getRelationshipsCacheFromDb($module);
        $this->relationships = $relationships;
    }

    /**
     * load relationships from cache file
     */
    private function loadRelationshipsCacheFromFiles(){
        if(sugar_is_file($this->getCacheFile()))
        {
            include($this->getCacheFile());
            $this->relationships = $relationships;
        } else {
            $this->buildRelationshipCache();
        }
    }


    protected function buildRelationshipCache()
    {
        global $beanList, $dictionary, $buildingRelCache;
        if ($buildingRelCache)
            return;
        $buildingRelCache = true;

        //Reload ALL the module vardefs....
        foreach($beanList as $moduleName => $beanName)
        {
            VardefManager::loadVardef($moduleName, BeanFactory::getObjectName($moduleName), false, array(
                //If relationships are not yet loaded, we can't figure out the rel_calc_fields.
                "ignore_rel_calc_fields" => true,
            ));
        }

        $relationships = array();

        //Grab all the relationships from the dictionary.
        foreach ($dictionary as $key => $def)
        {
            // BEGIN CR1000108 vardefs to db. Try to grab directly from db
//            if(isset(\SpiceCRM\includes\SugarObjects\SpiceConfig::getInstance()->config['systemvardefs']['dictionary']) && \SpiceCRM\includes\SugarObjects\SpiceConfig::getInstance()->config['systemvardefs']['dictionary']) {
//                $module = \SpiceCRM\modules\SystemVardefs\SystemVardefs::getModuleByDictionaryName($key);
//                \SpiceCRM\modules\SystemVardefs\SystemVardefs::loadRelationships($def, $module);
//            }
            // END CR1000108

            if (!empty($def['relationships']))
            {
                foreach($def['relationships'] as $relKey => $relDef)
                {
                    if ($key == $relKey) //Relationship only entry, we need to capture everything
                        $relationships[$key] = array_merge(array('name' => $key), $def, $relDef);
                    else {
                        $relationships[$relKey] = array_merge(array('name' => $relKey), $relDef);
                        if(!empty($relationships[$relKey]['join_table']) && empty($relationships[$relKey]['fields'])
                            && isset($dictionary[$relationships[$relKey]['join_table']]['fields'])) {
                            $relationships[$relKey]['fields'] = $dictionary[$relationships[$relKey]['join_table']]['fields'];
                        }
                    }
                }
            }
        }
        //Save it out
        sugar_mkdir(dirname($this->getCacheFile()), null, true);
        $out = "<?php \n \$relationships = " . var_export($relationships, true) . ";";
        sugar_file_put_contents_atomic($this->getCacheFile(), $out);

        $this->relationships = $relationships;

        //Now load all vardefs a second time populating the rel_calc_fields
        foreach ($beanList as $moduleName => $beanName) {
            VardefManager::loadVardef($moduleName, BeanFactory::getObjectName($moduleName));
        }

        $buildingRelCache = false;
    }

	protected function getCacheFile() {
		return sugar_cached("Relationships/relationships.cache.php");
	}



}
