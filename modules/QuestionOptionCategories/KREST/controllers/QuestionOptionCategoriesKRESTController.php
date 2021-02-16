<?php
namespace SpiceCRM\modules\QuestionOptionCategories\KREST\controllers;

use SpiceCRM\modules\QuestionOptionCategories\QuestionOptionCategory;
use SpiceCRM\includes\database\DBManagerFactory;

class QuestionOptionCategoriesKRESTController
{

    public function getList( $req, $res, $args ) {
        $db = DBManagerFactory::getInstance();

        if ( isset( $args['name'][0])) {
            $whereClause = sprintf('name like "\%%s\%"', $db->quote( $args['name'] ));
        } else $whereClause = '';

        return $res->withJson( array_values( QuestionOptionCategory::getAll( $whereClause )));
    }

}
