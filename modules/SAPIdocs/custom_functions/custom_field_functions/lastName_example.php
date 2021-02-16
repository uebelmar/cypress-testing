<?php


use SpiceCRM\data\SugarBean;

function lastName_example_in(SAPIdoc $idoc, SugarBean $seed, $field_defintion, $rawFields = array())
{
    if (!empty($seed->last_name)) {
        return true;
    }
    return false;
}

function lastName_example_out(SAPIdoc $idoc, SugarBean $seed, $field_defintion, $rawFields = array())
{
    if (!empty($seed->last_name)) {
        return true;
    }
    return false;
}

