<?php
/***** SPICE-SUGAR-HEADER-SPACEHOLDER *****/
namespace SpiceCRM\includes\SugarObjects\templates\basic;

use SpiceCRM\data\SugarBean;

class Basic extends SugarBean
{
    /** 
     * Constructor
     */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * @see SugarBean::get_summary_text()
	 */
	public function get_summary_text()
	{
		return "$this->name";
	}
}
