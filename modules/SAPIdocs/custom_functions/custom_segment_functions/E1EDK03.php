<?php

function E1EDK03_in(SAPIdoc $idoc, array $segment_defintion, array &$rawFields, SugarBean &$bean, &$parent = null){

    /**
    001	Delivery date (supplier)
    002	Requested delivery date (customer)
    003	Closing date for applications
    004	Deadline for submission of quotations
    005	Quotation/inquiry valid from
    006	Binding period for a quotation (valid to)
    007	Reconciliation date for agreed cumulative quantity
    008	First firm zone
    009	Second firm zone
    010	Shipping date
    011	Date IDOC created
    012	Document date
    013	Quotation date (supplier)
    014	Inquiry date (customer)
    015	Invoice posting date (Invoice tax point date)
    016	Invoice date
    017	Payment Date
    018	Bill of exchange date
    019	Start of validity for outline agreement or inquiry
    020	End of validity for outline agreement or inquiry
    021	Billing date for invoice list
    022	Purchase order date
    023	Pricing date
    024	Fixed value date
    025	Created on
    026	Billing date for billing index and printout
    027	Date on which services rendered
    028	Due date
    029	Sales order date
    030	Goods receipt date
    031	Planned date
    032	Date of reference number
    033	Shipment start date
    034	Planned shipment end date
    035	Goods issue date
    036	Bank value date
    037	Bank offsetting date
    038	Posting to bank
    039	Ship-to party's PO date
    040	Pickup date from (delivery order)
    041	Pickup date to (delivery order)
    042	Date of old balance
    043	Date of new balance
    044	Payment baseline date
    045	Shelf life expiration data for batch
    046	Date for Delivery Relevance
    047	Tax Reporting Date
    101	Resale Invoice Date
    102	Resale Ship Date
    103	Booking from date
    104	Booking to date
    105	Shipping from date
    106	Shipping to date
    107	Billing from date
    108	Billing to date
    109	Exercise from date
    110	Exercise to date
    048	Exchange Rate
    */
    switch($rawFields['IDDAT']){
        case '002':
            $date = date_create_from_format('Ymd', $rawFields['DATUM']);
            $bean->delivery_date = $date->format($GLOBALS['timedate']->get_db_date_format());
            break;
        case '025':
            $date = date_create_from_format('YmdHis', $rawFields['DATUM'].$rawFields['UZEIT']);
            $bean->date_entered = $date->format($GLOBALS['timedate']->get_db_date_format()) . ' 00:00:00';

            $date = date_create_from_format('Ymd', $rawFields['DATUM']);
            $bean->salesdocdate = $date->format($GLOBALS['timedate']->get_db_date_format());
            break;
    }
    return true;
}
