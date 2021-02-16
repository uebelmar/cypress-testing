<?php



function E1MVKE_in(SAPIdoc $idoc, array $segment_defintion, array &$rawFields, SugarBean &$bean, &$parent = null){


    /*
    $trkorgobjectutil = TRKOrgObjectsUtils::getInstance();
    $korgobject_id = $trkorgobjectutil->getorgobject(array('VKORG' => $rawFields['VKORG']));

    $bean->korgobjectmain = $korgobject_id;
    $orgobjectArray = array($korgobject_id);
    $multipleObject = new stdClass;
    $multipleObject->primary = $bean->korgobjectmain;
    $multipleObject->secondary  = $orgobjectArray;
    $bean->korgobjectmultiple = json_encode($multipleObject);


    // set the parent as well
    $product = \SpiceCRM\data\BeanFactory::getBean('KProducts', $bean->kproduct_id);

    if(empty($product->korgobjectmain)){
        $product->korgobjectmain = $bean->korgobjectmain;
        $product->korgobjectmultiple = $bean->korgobjectmultiple;
        $product->save();
    } else {
        $orgObjects = $trkorgobjectutil->getOrgObjectsForHash($product->korgobjecthash);

        if(array_search($korgobject_id, $orgObjects) === false){
            $orgObjects[] = $korgobject_id;
            $multipleObject = new stdClass;
            $multipleObject->primary = $product->korgobjectmain;
            $multipleObject->secondary  = $orgObjects;
            $product->korgobjectmultiple = json_encode($multipleObject);
            $product->save();
        }
    }
    */

    return true;
}
