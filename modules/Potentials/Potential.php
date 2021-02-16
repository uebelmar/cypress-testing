<?php
namespace SpiceCRM\modules\Potentials;

use SpiceCRM\data\SugarBean;

class Potential extends SugarBean {

    public $table_name = 'potentials';
    public $object_name = 'Potential';
    public $module_dir = 'Potentials';

    public function __construct() {
        parent::__construct();
    }

    public function get_summary_text() {
        return $this->name;
    }

    public function bean_implements($interface) {
        switch ($interface) {
            case 'ACL':return true;
        }

        return false;
    }

    public function retrieve($id = -1, $encode = false, $deleted = true, $relationships = true)
    {
        $bean = parent::retrieve($id, $encode, $deleted, $relationships);

        // gather potential captured: only for custom needs
        // create a custom hook that calculates and assigns the value to property potential_captured or the adequate custom one
        // $total = $this->db->fetchByAssoc($this->db->query("SELECT SUM(amount_net) realizedrevenue FROM salesdocitems, salesdocs, productvariants, products WHERE salesdocitems.salesdoc_id = salesdocs.id AND salesdocitems.deleted = 0 AND salesdocs.deleted = 0 AND salesdocs.companycode_id = '$this->companycode_id' AND salesdocs.account_op_id = '$this->account_id' AND salesdocitems.productvariant_id = productvariants.id AND productvariants.product_id = products.id AND products.productgroup_id = '$this->productgroup_id'"));
        // $this->potential_captured = $total['realizedrevenue'];

        return $bean;
    }

}
