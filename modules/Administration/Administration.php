<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

namespace SpiceCRM\modules\Administration;

use SpiceCRM\data\SugarBean;
use SpiceCRM\includes\SugarCache\SugarCache;

class Administration extends SugarBean {
    var $settings;
    var $table_name = "config";
    var $object_name = "Administration";
    var $new_schema = true;
    var $module_dir = 'Administration';
    var $config_categories = array(
        // 'mail', // cn: moved to include/OutboundEmail
        'disclosure', // appended to all outbound emails
        'notify',
        'system',
        'portal',
        'proxy',
        'massemailer',
        'ldap',
        'captcha',
        'sugarpdf'
    );

    function retrieveSettings($category = FALSE, $clean=false) {
        // declare a cache for all settings
        $settings_cache = SugarCache::sugar_cache_retrieve('admin_settings_cache');

        if($clean) {
            $settings_cache = array();
        }

        // Check for a cache hit
        if(!empty($settings_cache)) {
            $this->settings = $settings_cache;
            if (!empty($this->settings[$category]))
            {
                return $this;
            }
        }

        if ( ! empty($category) ) {
            $query = "SELECT category, name, value FROM {$this->table_name} WHERE category = '{$category}'";
        } else {
            $query = "SELECT category, name, value FROM {$this->table_name}";
        }

        $result = $this->db->query($query, true, "Unable to retrieve system settings");

        if(empty($result)) {
            return NULL;
        }

        while($row = $this->db->fetchByAssoc($result)) {
            if($row['category']."_".$row['name'] == 'ldap_admin_password' || $row['category']."_".$row['name'] == 'proxy_password')
                $this->settings[$row['category']."_".$row['name']] = $this->decrypt_after_retrieve($row['value']);
            else
                $this->settings[$row['category']."_".$row['name']] = $row['value'];
        }
        $this->settings[$category] = true;

        // At this point, we have built a new array that should be cached.
        SugarCache::sugar_cache_put('admin_settings_cache',$this->settings);
        return $this;
    }

    function saveConfig() {

        $this->retrieveSettings(false, true);
    }

    function saveSetting($category, $key, $value) {
        $result = $this->db->query("SELECT count(*) AS the_count FROM config WHERE category = '{$category}' AND name = '{$key}'");
        $row = $this->db->fetchByAssoc($result);
        $row_count = $row['the_count'];

        if($category."_".$key == 'ldap_admin_password' || $category."_".$key == 'proxy_password')
            $value = $this->encrpyt_before_save($value);

        if( $row_count == 0){
            $result = $this->db->query("INSERT INTO config (value, category, name) VALUES ('$value','$category', '$key')");
        }
        else{
            $result = $this->db->query("UPDATE config SET value = '{$value}' WHERE category = '{$category}' AND name = '{$key}'");
        }
        SugarCache::sugar_cache_clear('admin_settings_cache');
        return $this->db->getAffectedRowCount($result);
    }

    function get_config_prefix($str) {
        return Array(substr($str, 0, strpos($str, "_")), substr($str, strpos($str, "_")+1));
    }
}
