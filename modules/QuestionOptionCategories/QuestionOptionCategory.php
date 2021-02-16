<?php
namespace SpiceCRM\modules\QuestionOptionCategories;

use SpiceCRM\data\BeanFactory;
use SpiceCRM\data\SugarBean;
use SpiceCRM\KREST\handlers\ModuleHandler;

class QuestionOptionCategory extends SugarBean
{

    public $table_name = "questionoptioncategories";
    public $object_name = "QuestionOptionCategory";
    public $module_dir = 'QuestionOptionCategories';

    public function __construct()
    {
        parent::__construct();
    }

    public function bean_implements( $interface )
    {
        switch ( $interface ) {
            case 'ACL':
                return true;
        }
        return false;
    }

    public function get_summary_text()
    {
        return $this->name;
    }

    public function getAll( $whereClause = '' )
    {

        $seed = BeanFactory::getBean( 'QuestionOptionCategories' );
        $list = $seed->get_full_list( 'name', $whereClause );

        $retArray = array();
        $resthandler = new ModuleHandler();
        foreach ( $list as $listItem ) {
            $retArray[$listItem->abbreviation] = $resthandler->mapBeanToArray( 'QuestionOptionCategories', $listItem );
        }

        return $retArray;

    }

}
