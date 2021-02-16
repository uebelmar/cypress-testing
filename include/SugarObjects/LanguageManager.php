<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/

namespace SpiceCRM\includes\SugarObjects;

use SpiceCRM\includes\database\DBManagerFactory;

/**
 * Language files management
 * @api
 */
class LanguageManager
{

    /**
     * syslanguage
     * @param bool $sysonly
     * @return array
     */
	public static function getLanguages($sysonly = false){
	    $db = DBManagerFactory::getInstance();

	    $retArray =[
	        'available' => [],
	        'default' => ''
        ];

	    $languages = $db->query("SELECT * FROM syslangs " . ($sysonly ? "WHERE system_language = 1" : ""). " ORDER BY sort_sequence, language_name");
	    while($language = $db->fetchByAssoc($languages)){
            $retArray['available'][] = [
                'language_code' => $language['language_code'],
                'language_name' => $language['language_name'],
                'system_language' => $language['system_language'],
                'communication_language' => $language['communication_language']
            ];

            if($language['is_default'])
                $retArray['default'] = $language['language_code'];
        }

        return $retArray;
    }

    /**
     * syslanguage
     * @param $syslang
     * @return array
     */
	public static function loadDatabaseLanguage($syslang){
        $retArray = array();

        // get default Labels
        $q = "SELECT syslanguagetranslations.*, syslanguagelabels.name label
        FROM syslanguagetranslations, syslanguagelabels
        WHERE syslanguagetranslations.syslanguagelabel_id = syslanguagelabels.id
          AND syslanguagetranslations.syslanguage = '".$syslang."'
        ORDER BY label ASC";

        if($res = DBManagerFactory::getInstance()->query($q)) {
            while ($row = DBManagerFactory::getInstance()->fetchByAssoc($res)) {
                $retArray[$row['label']] = array(
                    'label' => $row['label'],
                    'default' => $row['translation_default'],
                    'short' => $row['translation_short'],
                    'long' => $row['translation_long'],
                );
            }
        }

        // custom translations to default labels
        $q = "SELECT syslanguagecustomtranslations.*, syslanguagelabels.name label
        FROM syslanguagecustomtranslations, syslanguagelabels
        WHERE (syslanguagecustomtranslations.syslanguagelabel_id = syslanguagelabels.id )
          AND syslanguagecustomtranslations.syslanguage = '".$syslang."' ORDER BY label ASC";
        if($res = DBManagerFactory::getInstance()->query($q)) {
            while ($row = DBManagerFactory::getInstance()->fetchByAssoc($res)) {
                $retArray[$row['label']] = array(
                    'label' => $row['label'],
                    'default' => $row['translation_default'],
                    'short' => $row['translation_short'],
                    'long' => $row['translation_long'],
                );
            }
        }

        // get custom labels
        $q = "SELECT  syslanguagecustomtranslations.*, syslanguagecustomlabels.name label
        FROM syslanguagecustomtranslations, syslanguagecustomlabels
        WHERE syslanguagecustomtranslations.syslanguagelabel_id = syslanguagecustomlabels.id
          AND syslanguagecustomtranslations.syslanguage = '".$syslang."' ORDER BY label ASC";
        if($res = DBManagerFactory::getInstance()->query($q)) {
            while ($row = DBManagerFactory::getInstance()->fetchByAssoc($res)) {
                $retArray[$row['label']] = array(
                    'label' => $row['label'],
                    'default' => $row['translation_default'],
                    'short' => $row['translation_short'],
                    'long' => $row['translation_long'],
                );
            }
        }

        /*
        no exception handling wanted...
        elseif(\SpiceCRM\includes\database\DBManagerFactory::getInstance()->last_error){
            throw new Exception(\SpiceCRM\includes\database\DBManagerFactory::getInstance()->last_error);
        }
        */
        return $retArray;
    }


}
