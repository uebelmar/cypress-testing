<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

/*
$metadatahandle = @opendir('./metadata');
while (false !== ($metadatafile = readdir($metadatahandle))) {
    if (preg_match('/\.php$/', $metadatafile)) {
        include('metadata/' . $metadatafile);
    }
}


if($cmetadatahandle = @opendir('./custom/metadata')) {
    while (false !== ($cmetadatafile = readdir($cmetadatahandle))) {
        if (preg_match('/\.php$/', $cmetadatafile)) {
            include('custom/metadata/' . $cmetadatafile);
        }
    }
}



if(file_exists('custom/application/Ext/TableDictionary/tabledictionary.ext.php')){
    include('custom/application/Ext/TableDictionary/tabledictionary.ext.php');
}

// BEGIN CR1000108 vardefs to db
if(isset(\SpiceCRM\includes\SugarObjects\SpiceConfig::getInstance()->config['systemvardefs']['dictionary']) && \SpiceCRM\includes\SugarObjects\SpiceConfig::getInstance()->config['systemvardefs']['dictionary']){
    require_once 'include/SpiceDictionaryVardefs/SpiceDictionaryVardefs.php';
    SpiceCRM\includes\SpiceDictionaryVardefs\SpiceDictionaryVardefs::loadDictionaries($dictionary, 'metadata');
}
// END
*/
