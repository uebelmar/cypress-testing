<?php

/* * *******************************************************************************
* This file is part of KReporter. KReporter is an enhancement developed
* by aac services k.s.. All rights are (c) 2016 by aac services k.s.
*
* This Version of the KReporter is licensed software and may only be used in
* alignment with the License Agreement received with this Software.
* This Software is copyrighted and may not be further distributed without
* witten consent of aac services k.s.
*
* You can contact us at info@kreporter.org
******************************************************************************* */


use SpiceCRM\includes\SugarObjects\SpiceConfig;
use SpiceCRM\includes\authentication\AuthenticationController;

require_once('modules/KReports/Plugins/prototypes/kreportpresentationplugin.php');

class kreportpresentationtree extends kreportpresentationplugin {

    public function display(&$thisReport) {

        global $app_list_strings, $mod_strings, $current_language;
$current_user = AuthenticationController::getInstance()->getCurrentUser();

        // build the Field List for the JSON Reader
        $arrayList = json_decode(html_entity_decode($thisReport->listfields, ENT_QUOTES), true);
        $fieldArray = '[{name:\'text\'}';

        foreach ($arrayList as $thisList) {
            if ($fieldArray != '[')
                $fieldArray .= ',';
            $fieldArray .= '{name : \'' . trim($thisList['fieldid'], ':') . '\'}';

            // see if we nee to add a field for the currency_id to the store 2010-12-25 
            // change to check if set to avoid php notice
            if ((isset($thisReport->fieldNameMap[$thisList['fieldid']]['fields_name_map_entry']['type']) && $thisReport->fieldNameMap[$thisList['fieldid']]['fields_name_map_entry']['type'] == 'currency') || (isset($thisReport->fieldNameMap[$thisList['fieldid']]['fields_name_map_entry']['kreporttype']) && $thisReport->fieldNameMap[$thisList['fieldid']]['fields_name_map_entry']['kreporttype'] == 'currency'))
                $fieldArray .= ',{name : \'' . trim($thisList['fieldid'], ':') . '_curid\'}';
        }

        //$fieldArray .= trim($fieldArray, ',');
        $fieldArray .= "]";
        // $fieldArray = htmlentities($fieldArray, ENT_QUOTES, 'UTF-8');
        $viewJS .= '<script type="text/javascript">FieldsArray = ' . htmlentities($fieldArray) . ';</script>';
        // $thisview->ss->assign('listfieldarray', $fieldArray);
        // build the Field List for the JSON Reader
        $arrayList = json_decode(html_entity_decode($thisReport->listfields, ENT_QUOTES), true);

        $listtypeproperties = json_decode(html_entity_decode($thisReport->listtypeproperties), true);
        $stopTreeAt = '';
        $pastStopTree = false;
        if (isset($listtypeproperties['treeViewProperties']['stopTreeAt'])) {
            $stopTreeAt = $listtypeproperties['treeViewProperties']['stopTreeAt'];
        }

        //initiate the array
        $columnArray = array();

        // add the field for the collapse
        $columnArray[] = array(
            'xtype' => '\'treecolumn\'',
            'text' => '\'text\'',
            'width' => 150,
            'dataIndex' => '\'text\''
        );

        // process tehe fields
        foreach ($arrayList as $thisList) {
            if ($pastStopTree) {
                $thisFieldType = ($thisList['overridetype'] == '' ? $thisReport->getFieldTypeById($thisList['fieldid']) : $thisList['overridetype']);
                $thisColumn = array(
                    //2013-03-05 html entities to support special chars in Text
                    //2013-04-19 added UTF-8 support
                    'text' => '\'' . htmlentities($thisList['name'], ENT_QUOTES, 'UTF-8') . '\'',
                    'width' => ((isset($thisList['width']) && $thisList['width'] != '' && $thisList['width'] != '0') ? $thisList['width'] : '150'),
                    'sortable' => ($thisList['sort'] != '' && $thisList['sort'] != '-' ? 'true' : 'false'),
                    'dataIndex' => '\'' . trim($thisList['fieldid'], ':') . '\'',
                    'hidden' => ($thisList['display'] != 'yes' ? true : false),
                    'align' => $thisReport->getXtypeAlignment($thisFieldType, $thisList['fieldid']),
                    'renderer' => 'renderField'
                );

                // see if we have renderer we need to process
                $renderer = $thisReport->getXtypeRenderer($thisReport->getFieldTypeById($thisList['fieldid']), $thisList['fieldid']);
                if ($renderer != '')
                    $thisColumn['fieldrenderer'] = "'" . $renderer . "'";

                // see if we have alignment we need to process
                $alignment = $thisReport->getXtypeAlignment($thisReport->getFieldTypeById($thisList['fieldid']), $thisList['fieldid']);
                if ($alignment != '')
                    $thisColumn['align'] = $alignment;

                $columnArray[] = $thisColumn;
            }
            elseif ($thisList['fieldid'] == $stopTreeAt)
                $pastStopTree = true;
        }

        // TODO .. JSON Encode with Bitmask ... need Doku
        // $thisview->ss->assign('columnmodeldarray', str_replace('"', '', json_encode($columnArray)));
        // include the js file 

        $viewJS .= '<script type="text/javascript">ColumnsArray = ' . str_replace('"', '', json_encode($columnArray)) . ';</script>';
        $viewJS .= '<script type="text/javascript" src="custom/modules/KReports/Plugins/Presentation/treeview/js/viewtree' . (SpiceConfig::getInstance()->config['KReports']['debug'] ? '_debug' : '') . '.js"></script>';
        return $viewJS;
    }

}
