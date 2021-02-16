<?php
/***** SPICE-HEADER-SPACEHOLDER *****/

namespace SpiceCRM\includes\SysTrashCan;

use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\authentication\AuthenticationController;

class SysTrashCan
{
    static function addRecord($recordtype, $recodmodule, $recordid, $recordname = '', $linkname = '', $linkmodule = '', $linkid = '', $recorddata = '')
    {
        global $timedate;
$current_user = AuthenticationController::getInstance()->getCurrentUser();
$db = DBManagerFactory::getInstance();
        $now = $timedate->nowDb();
        $db->query("INSERT INTO systrashcan (id, transactionid, date_deleted, user_deleted, recordtype, recordmodule, recordid, recordname, linkname, linkmodule, linkid, recorddata) VALUES('" . create_guid() . "', '" . $GLOBALS['transactionID'] . "', '$now', '$current_user->id','$recordtype', '$recodmodule', '$recordid', '$recordname', '$linkname', '$linkmodule', '$linkid', '".base64_encode($recorddata)."' )");
    }

    static function getRecords(){
        $db = DBManagerFactory::getInstance();

        $retArray = [];

        $records = $db->query("SELECT systrashcan.*, users.user_name FROM systrashcan, users WHERE systrashcan.user_deleted = users.id AND recordtype = 'bean' AND recovered = '0' ORDER BY date_deleted DESC");
        while($record = $db->fetchByAssoc($records)){
            $retArray[] = $record;
        }

        return $retArray;
    }

    static function getRelated($transactionid, $recordid){
        $db = DBManagerFactory::getInstance();

        $retArray = [];
        $records = $db->query("SELECT systrashcan.* FROM systrashcan WHERE recordtype = 'related' AND transactionid='$transactionid' AND recordid='$recordid' AND recovered = '0' ORDER BY date_deleted DESC");
        while($record = $db->fetchByAssoc($records)){
            $retArray[] = $record;
        }
        return $retArray;
    }

    static function recover($id, $related){
        global $beanList;
$db = DBManagerFactory::getInstance();

        $record = $db->fetchByAssoc($db->query("SELECT systrashcan.* FROM systrashcan WHERE id='$id' AND recovered = '0'"));

        $bean = array_search($record['recordmodule'], $beanList);
        $focus = BeanFactory::getBean($bean);
        if($focus->retrieve($record['recordid'], true, false)){
            $focus->mark_undeleted($focus->id);

            if($related){
                $focus->load_relationships();

                // set as recovered
                $db->query("UPDATE systrashcan SET recovered = '1' WHERE id='$id'");

                $relRecords = SysTrashCan::getRelated($record['transactionid'], $focus->id);
                foreach($relRecords as $relRecord){
                    if($focus->{$relRecord['linkname']}) {
                        $focus->{$relRecord['linkname']}->add($relRecord['linkid']);
                    }
                    // set as recovered
                    $db->query("UPDATE systrashcan SET recovered = '1' WHERE id='".$relRecord['id']."'");
                }
            }

        } else {
            return 'unable to load bean';
        }

        return true;
    }
}
