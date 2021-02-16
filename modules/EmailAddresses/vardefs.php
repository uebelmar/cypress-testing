<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

/*********************************************************************************

 * Description:
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc. All Rights
 * Reserved. Contributor(s): ______________________________________..
 *********************************************************************************/

/**
 * Stub class to allow Link class easily use SugarEmailAddress
 */
global $dictionary;
if(file_exists('custom/metadata/email_addressesMetaData.php')) {
  include('custom/metadata/email_addressesMetaData.php');
} else {
  include('metadata/email_addressesMetaData.php');
}

if(file_exists('custom/metadata/emails_beansMetaData.php')) {
  include('custom/metadata/emails_beansMetaData.php');
} else {
  include('metadata/emails_beansMetaData.php');
}
