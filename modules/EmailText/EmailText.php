<?php
namespace SpiceCRM\modules\EmailText;

use SpiceCRM\data\SugarBean;
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

/**
 * Class for separate storage of Email texts
 */
class EmailText extends SugarBean
{
	var $disable_row_level_security = true;
    var $table_name = 'emails_text';
    var $module_name = "EmailText";
    var $module_dir = 'EmailText';
    var $object_name = 'EmailText';
}
