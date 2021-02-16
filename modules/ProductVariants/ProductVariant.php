<?php

/*
 * Copyright notice
 *
 * (c) 2016 twentyreasons business solutions GmbH <office@twentyreasons.com>
 *
 * All rights reserved
 */
namespace SpiceCRM\modules\ProductVariants;

use SpiceCRM\data\BeanFactory;
use SpiceCRM\data\SugarBean;

class ProductVariant extends SugarBean
{

    public $table_name = "productvariants";
    public $object_name = "ProductVariant";
    public $module_dir = 'ProductVariants';
    public $unformated_numbers = true;

    public function __construct()
    {
        parent::__construct();
    }


    public function bean_implements($interface)
    {
        switch ($interface) {
            case 'ACL':
                return true;
        }
        return false;
    }


    public function get_summary_text()
    {
        return $this->name;
    }

    function fill_in_additional_detail_fields()
    {
        parent::fill_in_additional_detail_fields();

        $product = BeanFactory::getBean('Products', $this->product_id);
        $productGroup = BeanFactory::getBean('ProductGroups', $product->productgroup_id);

        $this->productgroup_id = $productGroup->id;
        $this->productgroup_name = $productGroup->name;
    }

    public function add_fts_metadata()
    {
        return array(
            'productgroups' => array(
                'type' => 'keyword',
                'index' => true,
                'aggregate' => 'term',
                'search' => false
            ),
            'productid' => array(
                'type' => 'keyword',
                'index' => true,
                'aggregate' => 'term',
                'search' => false
            )
        );
    }

    public function add_fts_fields()
    {
        $attribArray = Array();

        // get the attribute Arrays
        /*
        $this->load_relationship('productattributevalues');
        $attributevalues = $this->get_linked_beans('productattributevalues', 'ProductAttrbiuteValue');
        foreach ($attributevalues as $attributevalue) {
            if (!empty($attributevalue->pratvalue))
                $attribArray['attrib->' . $attributevalue->productattribute_id] = $attributevalue->pratvalue;
        }
        */

        // select the values
        $attribObj = $this->db->query("SELECT pav.pratvalue, pav.productattribute_id, pa.prat_datatype, pa.prat_length, pa.prat_precision FROM productattributevalues pav, productattributes pa WHERE pa.id=pav.productattribute_id AND pav.parent_id='$this->id' AND pav.deleted = 0 AND pa.deleted = 0");
        while ($attrib = $this->db->fetchByAssoc($attribObj)) {
            if (!empty($attrib['pratvalue'])) {
                switch (strtolower($attrib['prat_datatype'])) {
                    case 'n':
                        $precision = $attrib['prat_precision'] || $attrib['prat_precision'] === '0' ? $attrib['prat_precision'] : 2;
                        $length = $attrib['prat_length'] ?: $attrib['prat_precision'] + 5;

                        $attribValue = floatval($attrib['pratvalue']);
                        $attribValue = (string) round($attribValue * pow(10, $precision ));
                        while(strlen($attribValue) < $length)
                            $attribValue = '0' . $attribValue;

                        $attribArray['attrib->' . $attrib['productattribute_id']] = $attrib['pratvalue'];
                        break;
                    case 's':
                        $attribArray['attrib->' . $attrib['productattribute_id']] = explode(',', $attrib['pratvalue']);
                        break;
                    default:
                        $attribArray['attrib->' . $attrib['productattribute_id']] = $attrib['pratvalue'];
                        break;
                }
            }
        }

        // add the array with the product groups this belongs to
        $product = BeanFactory::getBean('Products', $this->product_id);
        if ($product) {
            $attribArray['productgroups'] = $product->getProductGroups();
            $attribArray['productid'] = $product->id;
        }

        return $attribArray;
    }
}
