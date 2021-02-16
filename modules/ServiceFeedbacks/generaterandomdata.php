<?php
/**
 * Created by PhpStorm.
 * User: christian
 * Date: 21.01.2018
 * Time: 15:46
 */

use SpiceCRM\data\BeanFactory;

$i = 0;
while ($i < 500) {

    $feedback = BeanFactory::getBean('ServiceFeedbacks');

    $dayspast = rand(0, 50);

    $contactIdRand = rand(0, 500);
    $contact = $db->fetchByAssoc($db->limitQuery("SELECT id FROM contacts WHERE deleted = 0", $contactIdRand, 1));

    $feedback->contact_id = $contact['id'];

    $now = new DateTime();
    $now->sub(new DateInterval('P' . $dayspast . 'D'));
    $feedback->date_entered = $now->format('Y-m-d H:i:s');

    $feedback->responsetime = ceil(log(rand(3, 149)));
    $feedback->responsequality = ceil(log(rand(3, 149)));
    $feedback->responsefriendlyness = ceil(log(rand(3, 149)));

    $feedback->servicefeedback_status = rand(1, 10) > 3 ? 'completed' : 'sent';

    $feedback->assigned_user_id = '2';
    $feedback->save();

    $i++;
}
