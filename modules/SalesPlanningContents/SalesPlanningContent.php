<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
namespace SpiceCRM\modules\SalesPlanningContents;

use SpiceCRM\data\BeanFactory;
use SpiceCRM\data\SugarBean;
use SpiceCRM\includes\database\DBManagerFactory;

class SalesPlanningContent extends SugarBean
{

    public $module_dir = 'SalesPlanningContents';
    public $object_name = 'SalesPlanningContent';
    public $table_name = 'salesplanningcontents';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_summary_text()
    {
        return $this->name;
    }

    /*
     * enable acl check for this module
     * @param $interface: string
     * @return boolean
     */
    public function bean_implements($interface)
    {
        switch ($interface) {
            case 'ACL':
                return true;
        }
        return false;
    }

    /*
     * @param $startDate: string
     * @param $unit: string
     * @param $segments: string
     * @return $periods: array
     */
    public static function getPeriods($startDate, $unit, $segments)
    {
        global $app_list_strings, $timedate, $current_language;
        $app_list_strings = return_app_list_strings_language($current_language);
        $return = [];

        $currentDate = new DateTime($startDate);

        for ($i = 0; $i < $segments; $i++) {
            switch ($unit) {
                case 'days':
                    $dof = $currentDate->format('w');
                    // increase week day index to match the label index
                    $dof++;
                    // define the day
                    $return[] = array('date_raw' => $currentDate, 'title' => $app_list_strings['dom_cal_day_short'][$dof] . "., " . $timedate->to_display_date($currentDate));
                    // add a day interval to the date
                    $currentDate = $currentDate->add(new DateInterval('P1D'));
                    break;

                case 'weeks':
                    $dof = $currentDate->format('w');
                    // if the day of week is the first one set it to the last one to ensure having the next week in the next added interval
                    if ($dof == 0) $dof = 7;

                    if ($dof > 1) {
                        // set the number of interval days
                        $dof = ($dof - 1) * -1;
                        // set the operation to add if the value is not zero
                        $operation = $dof > -1 ? 'add' : 'sub';
                        // convert the value to positive number
                        $dofAbs = abs($dof);
                        // reset the date day of week*
                        $currentDate = $currentDate->{$operation}(new DateInterval("P{$dofAbs}D"));
                    }
                    // set the week
                    $return[] = array('date_raw' => $currentDate, 'title' => "calendar_week" . $currentDate->format('W'));
                    // add 7 days interval
                    $currentDate = $currentDate->add(new DateInterval('P7D'));

                    break;

                case 'months':
                    // set the month
                    $currentDate = new DateTime($currentDate->format('Y-m-01'));
                    $return[] = array('date_raw' => $currentDate->format('Y-m-d'), 'title' => $app_list_strings['dom_cal_month_short'][$currentDate->format('n')] . ". " . $currentDate->format('Y'));
                    // add one month interval
                    $currentDate = $currentDate->add(new DateInterval('P1M'));
                    break;

                case 'quarters':
                    $resMonth = $currentDate->format('m');
                    // group months by quarter
                    switch ($resMonth) {
                        case 1:
                        case 2:
                        case 3:
                            $resMonth = 1;
                            break;
                        case 4:
                        case 5:
                        case 6:
                            $resMonth = 4;
                            break;
                        case 7:
                        case 8:
                        case 9:
                            $resMonth = 7;
                            break;
                        default:
                            $resMonth = 10;
                    }
                    // set the quarter display value
                    $currentDate = new DateTime($currentDate->format("Y-$resMonth-01"));
                    switch ($resMonth) {
                        case 2:
                        case 3:
                        case 1:
                            $qt = "I.";
                            break;
                        case 5:
                        case 6:
                        case 4:
                            $qt = "II.";
                            break;
                        case 8:
                        case 9:
                        case 7:
                            $qt = "III.";
                            break;
                        default:
                            $qt = "IV.";
                    }
                    $return[] = ['date_raw' => $currentDate, 'title' => $qt . " (" . $currentDate->format('Y') . ")"];
                    // add three months interval
                    $currentDate = $currentDate->add(new DateInterval('P3M'));
                    break;

                case 'years':
                    // set the year
                    $currentDate = new DateTime($currentDate->format("Y-01-01"));
                    $return[] = [
                        'date_raw' => $currentDate, 'title' => $currentDate->format('Y')
                    ];
                    // add one year interval
                    $currentDate = $currentDate->add(new DateInterval('P1Y'));
                    break;
            }
        }

        return $return;
    }

    /**
     * Returns the grid column (time line) in the current user language
     *
     * @param $planningVersionId: string
     * @return array $cols
     */
    public static function getGridColumns($planningVersionId)
    {
        global $current_language;

        $mod_strings = return_module_language($current_language, "SalesPlanningContents");

        $cols = [];
        // Planning Content Fields
        $cols[] = array('id' => 'description', 'header' => $mod_strings['LBL_PLANNING_CONTENT_FIELD'], 'dataIndex' => 'description', 'width' => 150);

        // Periods
        $pv = new SalesPlanningVersion();
        if ($pv->retrieve($planningVersionId)) {
            $segments = SalesPlanningContent::getPeriods($pv->date_start, $pv->periode_unit, $pv->periode_segments);
            $i = 0;
            foreach ($segments as $segment) {
                $i++;
                $cols[] = array('id' => 'p' . $i, 'header' => $segment['title'], 'tooltip' => $segment['title'], 'dataIndex' => 'data_' . $pv->periode_unit . '_p' . $i, 'width' => 1);
            }
        }

        // SUM column for all/shown periods
        $cols[] = array('id' => 'sum', 'header' => $mod_strings['LBL_SUM_PERIODS'], 'dataIndex' => 'sum', 'width' => 75);

        return $cols;
    }

    /**
     * Returns the Planning Content Fields for a planning version and caches them
     *
     * @param string $planningVersionId
     * @return array $fields
     */
    public static function getPlanningFields($planningVersionId)
    {
        $db = DBManagerFactory::getInstance();
        if (self::$fields[$planningVersionId] == null) {
            self::$fields[$planningVersionId] = [];
            $resultSet = $db->query("SELECT salesplanningcontent_id FROM salesplanningversions_salesplanningcontents WHERE salesplanningversion_id = '" . $planningVersionId . "' AND deleted = 0");
            while ($row = $db->fetchByAssoc($resultSet)) {
                $id = $row['salesplanningcontent_id'];
                $pc = new SalesPlanningContent();
                if ($pc->retrieve($id)) {
                    self::$fields[$planningVersionId] = $pc->get_linked_beans("salesplanningcontentfields", "SalesPlanningContentField");
                }
            }
        }

        return self::$fields[$planningVersionId];
    }

    /**
     * Returns the Planning Content Fields color
     *
     * @param String $planningVersionId
     * @return string
     */
    public static function getFieldColor($planningVersionId)
    {
        $fieldSection = "";
        $fields = self::getPlanningFields($planningVersionId);
        foreach ($fields as $field) {
            $fieldSection .= "if(fieldId == '" . $field->id . "') { return '" . $field->display_color . "' }\n";
        }
        return "function getFieldColor(fieldId) { {$fieldSection} return ''; }";
    }

    /**
     * Adds the formula for the planning content fields
     *
     * @param String $planningVersionId
     * @return String JavaScript function for output
     */
    public function getFormulaSection($planningVersionId)
    {
        $fieldSection = $fieldDependencySection = "";
        $fields = self::getPlanningFields($planningVersionId);
        foreach ($fields as $field) {
            if (strlen(trim($field->formula)) > 0) {
                $fieldSection .= "if(fieldId == '" . $field->id . "') { return SalesPlanningContentApp.calculateFormula('" . addslashes($field->formula) . "', rowIndex, colIndex, store); }\n";
                $fieldDependencySection .= "if(fieldId == '" . $field->id . "') { return SalesPlanningContentApp.getFormulaDependencies('" . addslashes($field->formula) . "', availableDataRows); }\n";
            }
        }
        return "function executeFormula(defaultValue, fieldId, rowIndex, colIndex, store) { {$fieldSection} return defaultValue; }\n" .
            "function checkDependencies(availableDataRows, fieldId, store) { {$fieldDependencySection} return [] }";
    }

    /**
     * Adds the editable check function for the planning content fields
     *
     * @param String $planningVersionId
     * @return String JavaScript function for output
     */
    public function getEditPrivilegeSection($planningVersionId)
    {
        $fieldSection = "";
        $fields = self::getPlanningFields($planningVersionId);
        foreach ($fields as $field) {
            $fieldSection .= "if(fieldId == '" . $field->id . "') { return " . ($field->editable == 1 ? 'true' : 'false') . "; }\n";
        }

        // 2012-01-04 C.Knoll change to reflect editbale flag
        // return "function isFieldEditable(defaultValue, fieldId, rowIndex, colIndex, store) { if(!SalesPlanningNodeApp.currentNodeIsLeaf) return false; {$fieldSection} return defaultValue; }";
        return "function isFieldEditable(defaultValue, fieldId, rowIndex, colIndex, store) { if(SalesPlanningContentApp.isDisabled) return false; {$fieldSection} return defaultValue; }";
    }

    /**
     * Adds the storable check function for the planning content fields
     *
     * @param String $planningVersionId
     * @return String JavaScript function for output
     */
    public function getStoreableFlagSection($planningVersionId)
    {
        $fieldSection = "";
        $fields = self::getPlanningFields($planningVersionId);
        foreach ($fields as $field) {
            $fieldSection .= "if(fieldId == '" . $field->id . "') { return " . ($field->storable == 1 ? 'true' : 'false') . "; }\n";
        }
        return "function isFieldStoreable(defaultValue, fieldId, rowIndex, colIndex, store) { if(!SalesPlanningNodeApp.currentNodeIsLeaf) return false; {$fieldSection} return defaultValue; }";
    }

    /**
     * Adds the sum formula for the planning content fields
     *
     * @param String $planningVersionId
     * @return String JavaScript function for output
     */
    public function getFormulaSumSection($planningVersionId)
    {
        $fieldSection = $fieldDependencySection = "";
        $fields = self::getPlanningFields($planningVersionId);
        foreach ($fields as $field) {
            if (strlen(trim($field->formula_sum)) > 0) {
                $fieldSection .= "if(fieldId == '" . $field->id . "') { return SalesPlanningContentApp.calculateFormula('" . addslashes($field->formula_sum) . "', rowIndex, colIndex, store); }\n";
                $fieldDependencySection .= "if(fieldId == '" . $field->id . "') { return SalesPlanningContentApp.getFormulaSumDependencies('" . addslashes($field->formula_sum) . "', availableDataRows); }\n";
            }
        }
        return "function executeFormulaSum(defaultValue, fieldId, rowIndex, colIndex, store) { {$fieldSection} return defaultValue; }\n" .
            "function checkSumDependencies(availableDataRows, fieldId, store) { {$fieldDependencySection} return [] }";
    }

    /**
     * transpiles the nodes array passed in by the custom function in an aray with territorry and the characteristics and named values
     *
     * @param $nodesArray
     * @return array
     */
    function transpileNodesArray($nodesArray)
    {
        $retArray = [];
        foreach ($nodesArray as $nodecharValue) {
            if ($nodecharValue == 'root') continue;
            $characteristicValue = BeanFactory::getBean('SalesPlanningCharacteristicValues', $nodecharValue);
            if (!$characteristicValue) {
                // check if it is a territory
                $territory = BeanFactory::getBean('SalesPlanningTerritories', $nodecharValue);
                if ($territory) {
                    $retArray[] = [
                        'type' => 'SalesPlanningTerritories',
                        'id' => $territory->id,
                        'name' => $territory->name
                    ];
                }
            } else {
                $characteristic = BeanFactory::getBean('SalesPlanningCharacteristics', $characteristicValue->salesplanningcharacteristic_id);
                $retArray[] = [
                    'type' => 'SalesPlanningCharacteristics',
                    'id' => $characteristic->id,
                    'name' => $characteristic->name,
                    'field_module' => $characteristic->field_module,
                    'field_link' => $characteristic->field_link,
                    'field_reference' => $characteristic->field_reference,
                    'cvkey' => $characteristicValue->cvkey
                ];
            }
        }

        return $retArray;
    }

    /**
     * translates the periodstart and the periodend into two database formatted dtae strings for the start and the end
     *
     * @param $periodstart
     * @param $perioddate
     */
    function getPeriodEndDate($periodstart, $periodtype)
    {
        $endDate = new DateTime($periodstart);
        switch ($periodtype) {
            default:
                return $endDate->format('Y-m-t 23:59:59');
                break;
        }
    }

}
