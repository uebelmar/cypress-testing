<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

namespace SpiceCRM\modules\UserAccessLogs;

use SpiceCRM\data\SugarBean;
use SpiceCRM\includes\authentication\AuthenticationController;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\TimeDate;

class UserAccessLog extends SugarBean
{
    public $object_name = 'UserAccessLog';
    public $table_name = 'useraccesslogs';
    public $disable_row_level_security = true;
    public $module_dir = 'UserAccessLogs';

    public function __construct()
    {
        parent::__construct();
        $this->tracker_visibility = false;
    }


    /**
     * @param $username
     * @param int $seconds
     * @return integer
     * @throws \Exception
     */
    public function getAmountFailedLoginsWithinByUsername($username, $seconds = 3600)
    {
        $db = DBManagerFactory::getInstance();

        $dtObj=new \DateTime();
        $dtObj->setTimestamp(time()-$seconds);
        $timeLimit = Timedate::getInstance()->asDb($dtObj);


        $sql="SELECT count(0) FROM useraccesslogs WHERE login_name = '" . $db->quote($username) . "' and date_entered >'".$timeLimit."' and action='loginfail'";
        $count=$db->fetchOne($sql);
        if(is_array($count)) {
            return $count[0];
        }
    }
    private function getRemoteAddress()
    { //todo refactor to a central place for ip address handling
        //maybe query_client_ip()?
        $ipaddress = '';
        if ($_SERVER['HTTP_CLIENT_IP'])
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if ($_SERVER['HTTP_X_FORWARDED_FOR'])
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if ($_SERVER['HTTP_X_FORWARDED'])
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if ($_SERVER['HTTP_FORWARDED_FOR'])
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if ($_SERVER['HTTP_FORWARDED'])
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if ($_SERVER['REMOTE_ADDR'])
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';

        return $ipaddress;
    }

    /**
     * @param string $action loginsuccess | loginfail
     * @param null $loginName
     */
    public function addRecord($action = 'loginsuccess', $loginName = null)
    {
        $currentUser = AuthenticationController::getInstance()->getCurrentUser();

        if ($loginName === null && $currentUser !== null) {
            $loginName = $currentUser->name;
        }
        $this->ipaddress = $this->getRemoteAddress();
        $this->assigned_user_id = $currentUser ? $currentUser->id : null;
        $this->action = $action;
        $this->login_name = $loginName;
        if(!$this->save()) {
            throw new \Exception("unable to save useraccess log record");
        }
        return true;
    }
}
