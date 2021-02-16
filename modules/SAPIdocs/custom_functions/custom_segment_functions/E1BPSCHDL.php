<?php

use SpiceCRM\data\SugarBean;

function E1BPSCHDL_out(SAPIdoc $idoc, array $segment_defintion, array &$rawFields, SugarBean &$bean, $parent = null)
{

    // set date fields
    $date = new DateTime();
    $rawFields['REQ_DATE'] = $date->format('Ymd');

    return true;
}
