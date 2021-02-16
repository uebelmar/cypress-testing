<?php

use SpiceCRM\includes\database\DBManagerFactory;

$db = DBManagerFactory::getInstance();

$db->transactionStart();

$db->query('TRUNCATE TABLE questionnaireparticipations');
$db->query('UPDATE questionsetparticipations SET questionnaireparticipation_id = NULL');

$questionnaireParticipations = [];
if (( $dbResult = $db->query('SELECT date_entered, date_modified, modified_user_id, created_by, assigned_user_id, referencetype, referenceid, MIN(starttime) as starttime, questionset_id FROM questionsetparticipations WHERE deleted = 0 GROUP BY referencetype, referenceid' )) !== false ) {

    while ( $dummy = $db->fetchByAssoc( $dbResult ) ) {

        $questionnaireId = $db->getOne('SELECT q.id FROM questionsets qs INNER JOIN questionnaires q ON q.id = qs.questionnaire_id WHERE q.deleted = 0 AND qs.deleted = 0 AND qs.id = "'.$dummy['questionset_id'].'"');
        if ( !empty( $questionnaireId )) {

            $participationStatus = '';
            if ( $dummy['referencetype'] === 'SUPConsultingOrderItems' ) {
                $orderAndOrderItem = $db->fetchOne('SELECT oi.item_status, o.contact_id FROM supconsultingorderitems oi INNER JOIN supconsultingorders o ON o.id = oi.supconsultingorder_id WHERE o.deleted = 0 AND oi.deleted = 0 AND oi.id = "' . $dummy['referenceid'] . '"' );
            }
            if ( $orderAndOrderItem ) { # Ignore when there is no SUPConsultingOrder or SUPConsultingOrderItem (or deleted)
                $db->query( sprintf( 'INSERT INTO questionnaireparticipations ( id, date_entered, date_modified, modified_user_id, created_by, assigned_user_id, parent_type, parent_id, starttime, questionnaire_id, completed, contact_id ) VALUES( "%s", "%s", "%s", "%s", "%s", "%s", "%s", "%s", "%s", "%s", %s, "%s" )', $guid = create_guid(), $dummy['date_entered'], $dummy['date_modified'], $dummy['modified_user_id'], $dummy['created_by'], $dummy['assigned_user_id'], $dummy['referencetype'], $dummy['referenceid'], $dummy['starttime'], $questionnaireId, $orderAndOrderItem['item_status'] === 'completed' ? 1 : 0, $orderAndOrderItem['contact_id'] ) );
                echo $guid . ' ' . $dummy['referencetype'] . ' ' . $dummy['referencetype'];
                echo "<br>\n";
                $db->query( 'UPDATE questionsetparticipations SET questionnaireparticipation_id = "' . $guid . '" WHERE referencetype = "' . $dummy['referencetype'] . '" AND referenceid = "' . $dummy['referenceid'] . '"' );
            }

        }
    }

} else echo "error";

#$db->transactionRollback();
$db->transactionCommit();
