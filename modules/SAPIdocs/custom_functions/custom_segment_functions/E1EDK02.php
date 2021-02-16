<?php

use SpiceCRM\data\BeanFactory;

function E1EDK02_in(SAPIdoc $idoc, array $segment_defintion, array &$rawFields, SugarBean &$bean, &$parent = null){

    /**
    001	Customer Purchase Order
    002	Vendor Order
    003	Customer Inquiry
    004	Vendor Quotation
    005	Customer Contract Number
    006	Vendor Contract Number
    007	Collective Number for Quotations
    008	Last Purchase Order Number (SPEC2000 Acknowl.)
    009	Invoice Number
    010	Internal Number (Document)
    011	Referenced Document Number
    012	Delivery Note Number
    013	Internal PO Number
    014	Accounting Document
    015	Billing Document Number of Invoicing Party
    016	Number of Preceding Document
    017	Assignment Number
    018	Customer Order Number
    019	ISR Number
    020	Invoice List Number
    021	ID for Cost Assignment
    022	Payment Document Number
    023	Banker's Acceptance
    024	Matured Certificate of Deposit (CD)
    025	Loan
    026	Check Number
    027	Foreign Exchange Contract Number
    028	Credit Memo
    029	Payment Advice Note Number
    030	Original Purchase Order Number (ALE)
    031	Return Leg Number
    032	Reference Bank
    033	Third-Party Reference Number
    034	Reference Number of Beneficiary's Bank
    035	Message Reference
    036	Credit Card Number
    037	Statement Number
    038	Account Statement No. (Deposit No.)
    039	Account Statement No. (Deposit Seq. No)
    040	Payee Code
    041	MICR Line
    042	Imported Line
    043	Vendor Contract Number
    044	Ship-To Party's PO Order
    045	Cost Center
    046	Profitability Segment No.
    047	Work Breakdown Structure Object
    048	Profit Center
    049	Business Area
    050	Delivery Order
    051	Delivery Order Route Number
    052	Sequence Number
    053	Scheduling Agreement Number
    054	External Transaction
    055	Promotion Number
    056	Customer Quotation Number
    057	Customer Buying Group
    058	Customer Contract Number
    059	Check Number from Check Register
    060	JIT Call Number
    061	Internal Delivery Note Number
    062	Customer PO no. for consignment issue by ext. service agent
    063	External Delivery Note Number
    064	Goods Receipt/Issue Slip Number
    065	Repetitive Wire Nummer
    066	External Order Number
    067	Quality Notification Number
    068	External Inquiry Number
    069	Business Partner Reference Key
    070	Reference Text for Settled Items
    071	Customer ID no.
    072	Agreement Number
    073	Credit Advice Number
    074	Transfer Number
    075	Check Number
    076	Credit Posting Number
    077	Transfer number (just transferred)
    078	Delivering Profit Center
    079	Batch Number
    080	Certificate Profile
    081	Collective Daily Delivery Note
    082	Summarized JIT call
    083	External Delivering Plant
    084	Tax Number Tax Office �14
    085	KANBAN ID
    086	Kanban Control Cycle
    087	Reference Document Number Billing Doc.
    090	Higher-Level Billing Item
    102	Resale Ship and Debit Agreement No.
    103	Customer Claim Reference Number
    104	Design Registration Number
    105	TPOP Order Number
    106	TPOP Reference Order Number
    111	Partner Business Area
    APY	Approval Year of Official Document Numbers
    APN	Registration Number of Official Document Numbers
    SNC	Serial Number of Statement
    120	Contract Account Number
    88	FI-CA Contract Account Number
    */
    switch($rawFields['QUALF']){
        case '001':
            $bean->ponumber = $rawFields['BELNR'];
            break;
        case '007':
            $lead = BeanFactory::getBean('LEADS');
            if($lead->retrieve_by_string_fields(['sch_lead_number' => $rawFields['BELNR']])){
                $bean->lead_id = $lead->id;
            }
            break;
    }
    return true;
}
