<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/
namespace SpiceCRM\modules\Trackers;

use SpiceCRM\data\SugarBean;
use SpiceCRM\includes\Logger\LoggerManager;
use SpiceCRM\includes\SugarObjects\SpiceConfig;

class Tracker extends SugarBean
{
    var $module_dir = 'Trackers';
    var $table_name = 'tracker';
    var $object_name = 'Tracker';

    /*
     * Return the most recently viewed items for this user.
     * The number of items to return is specified in sugar_config['history_max_viewed']
     * @param uid user_id
     * @param mixed module_name Optional - return only items from this module, a string of the module or array of modules
     * @return array list
     */
    function get_recently_viewed($user_id, $modules = '', $count = 10)
    {
        if (empty($_SESSION['breadCrumbs'])) {
            $breadCrumb = new BreadCrumbStack($user_id, $modules);
            $_SESSION['breadCrumbs'] = $breadCrumb;
            LoggerManager::getLogger()->info(string_format($GLOBALS['app_strings']['LBL_BREADCRUMBSTACK_CREATED'], array($user_id)));
        } else {
            $breadCrumb = $_SESSION['breadCrumbs'];
            $module_query = '';
            if (!empty($modules)) {
                $history_max_viewed = 10;
                $module_query = is_array($modules) ? ' AND module_name IN (\'' . implode("','", $modules) . '\')' : ' AND module_name = \'' . $modules . '\'';
            } else {
                $history_max_viewed = (!empty(SpiceConfig::getInstance()->config['history_max_viewed'])) ? SpiceConfig::getInstance()->config['history_max_viewed'] : 50;
            }

            $query = 'SELECT item_id, item_summary, module_name, id FROM ' . $this->table_name . ' WHERE id = (SELECT MAX(id) as id FROM ' . $this->table_name . ' WHERE user_id = \'' . $user_id . '\' AND deleted = 0 AND visible = 1' . $module_query . ')';
            $result = $this->db->limitQuery($query, 0, $history_max_viewed, true, $query);
            while (($row = $this->db->fetchByAssoc($result))) {
                $breadCrumb->push($row);
            }
        }

        $list = $breadCrumb->getBreadCrumbList($modules, $count);
        LoggerManager::getLogger()->info("Tracker: retrieving " . count($list) . " items");
        return $list;
    }

    function makeInvisibleForAll($item_id)
    {
        $query = "UPDATE $this->table_name SET visible = 0 WHERE item_id = '$item_id' AND visible = 1";
        $this->db->query($query, true);
        if (!empty($_SESSION['breadCrumbs'])) {
            $breadCrumbs = $_SESSION['breadCrumbs'];
            $breadCrumbs->popItem($item_id);
        }
    }

    static function logPage()
    {
        $time_on_last_page = 0;
        //no need to calculate it if it is a redirection page
        if (empty($GLOBALS['app']->headerDisplayed)) return;
        if (!empty($_SESSION['lpage'])) $time_on_last_page = time() - $_SESSION['lpage'];
        $_SESSION['lpage'] = time();
    }


    /**
     * bean_implements
     * Override method to support ACL roles
     */
    function bean_implements($interface)
    {
        return false;
    }
}
