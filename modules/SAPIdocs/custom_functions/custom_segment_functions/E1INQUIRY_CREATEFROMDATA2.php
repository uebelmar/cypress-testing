<?php

use SpiceCRM\data\BeanFactory;

if(!function_exists('textformat')) {
    function textformat($params, $content)
    {
        if (is_null($content)) {
            return;
        }

        $style = null;
        $indent = 0;
        $indent_first = 0;
        $indent_char = ' ';
        $wrap = 80;
        $wrap_char = "\n";
        $wrap_cut = false;
        $assign = null;

        foreach ($params as $_key => $_val) {
            switch ($_key) {
                case 'style':
                case 'indent_char':
                case 'wrap_char':
                case 'assign':
                    $$_key = (string)$_val;
                    break;

                case 'indent':
                case 'indent_first':
                case 'wrap':
                    $$_key = (int)$_val;
                    break;

                case 'wrap_cut':
                    $$_key = (bool)$_val;
                    break;

                default:
                    die("textformat: unknown attribute '$_key'");
            }
        }

        if ($style == 'email') {
            $wrap = 72;
        }

        // split into paragraphs
        $_paragraphs = preg_split('![\r\n][\r\n]!', $content);
        $_output = '';

        for ($_x = 0, $_y = count($_paragraphs); $_x < $_y; $_x++) {
            if ($_paragraphs[$_x] == '') {
                continue;
            }
            // convert mult. spaces & special chars to single space
            $_paragraphs[$_x] = preg_replace(array('!\s+!', '!(^\s+)|(\s+$)!'), array(' ', ''), $_paragraphs[$_x]);
            // indent first line
            if ($indent_first > 0) {
                $_paragraphs[$_x] = str_repeat($indent_char, $indent_first) . $_paragraphs[$_x];
            }
            // wordwrap sentences
            $_paragraphs[$_x] = wordwrap($_paragraphs[$_x], $wrap - $indent, $wrap_char, $wrap_cut);
            // indent lines
            if ($indent > 0) {
                $_paragraphs[$_x] = preg_replace('!^!m', str_repeat($indent_char, $indent), $_paragraphs[$_x]);
            }
        }
        $_output = implode($wrap_char . $wrap_char, $_paragraphs);

        return $_output;
    }
}


function E1INQUIRY_CREATEFROMDATA2_out(SAPIdoc $idoc, array $segment_defintion, array &$rawFields, SugarBean &$bean, $parent = null)
{

    $ag = BeanFactory::getBean('Accounts', $bean->customer_id);
    $rawFields['E1BPPARNR'] = array(
        array(
            '@attributes' => array('SEGMENT' => '1'),
            'PARTN_ROLE' => 'AG',
            'PARTN_NUMB' => $ag->k_sap_customerid
        )
    );

    if (!empty($bean->consignee_id)) {
        $we = BeanFactory::getBean('Accounts', $bean->consignee_id);
        $rawFields['E1BPPARNR'][] = array(
                '@attributes' => array('SEGMENT' => '1'),
                'PARTN_ROLE' => 'WE',
                'PARTN_NUMB' => $we->k_sap_customerid
        );
    }

    if (!empty($bean->final_customer_id)) {
        $ze = BeanFactory::getBean('Accounts', $bean->final_customer_id);
        $rawFields['E1BPPARNR'][] = array(
                '@attributes' => array('SEGMENT' => '1'),
                'PARTN_ROLE' => 'ZE',
                'PARTN_NUMB' => $ze->k_sap_customerid
        );
    }

    // kopftext
    /*
    $rawFields['E1BPSDTEXT'] = [];
    $texts = explode("\n", textformat(array('wrap' => 130, 'wrap_char' => "\r\n"), html_entity_decode($bean->freetext, ENT_QUOTES)));
    foreach($texts as $line) {
        $rawFields['E1BPSDTEXT'][] = array(
            '@attributes' => array('SEGMENT' => '1'),
            'ITM_NUMBER' => '',
            'TEXT_ID' => "Z006", // Z006 = Angebotskopftext
            'LANGU' => "DE",
            'TEXT_LINE' => $line,
        );
    }
    */
    return true;
}
