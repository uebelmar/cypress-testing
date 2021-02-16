<?php
namespace SpiceCRM\modules\SalesDocuments;

use DateInterval;
use DateTime;
use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\database\DBManagerFactory;

class SalesDocumentsPlanningFunctions{

    function getDocumentsForPeriod($nodes, $periodstart, $periodtype){
        $db = DBManagerFactory::getInstance();

        $contents = BeanFactory::getBean('SalesPlanningContents');
        $valArray = $contents->transpileNodesArray($nodes);

        // move dates to last year
        $periodstart = new DateTime($periodstart);
        $periodstart = $periodstart->sub(new DateInterval('P1Y'));
        $periodstart = $periodstart->format('Y-m-d');
        $periodend = $contents->getPeriodEndDate($periodstart, $periodtype);

        $total = $db->fetchByAssoc($db->query("SELECT SUM(sdi.amount_net) total FROM salesdocs sd, salesdocitems sdi WHERE sdi.salesdoc_id = sd.id AND sd.salesdocdate  > '$periodstart' AND sd.salesdocdate<'$periodend'"));

        return $total['total'];
    }

    function getDocumentsForPeriodSum($nodes, $values, $whatever){
        return 1200;
    }
}
