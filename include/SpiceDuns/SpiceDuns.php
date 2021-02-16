<?php
/*********************************************************************************
* This file is part of SpiceCRM. SpiceCRM is an enhancement of SugarCRM Community Edition
* and is developed by aac services k.s.. All rights are (c) 2016 by aac services k.s.
* You can contact us at info@spicecrm.io
* 
* SpiceCRM is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version
* 
* The interactive user interfaces in modified source and object code versions
* of this program must display Appropriate Legal Notices, as required under
* Section 5 of the GNU Affero General Public License version 3.
* 
* In accordance with Section 7(b) of the GNU Affero General Public License version 3,
* these Appropriate Legal Notices must retain the display of the "Powered by
* SugarCRM" logo. If the display of the logo is not reasonably feasible for
* technical reasons, the Appropriate Legal Notices must display the words
* "Powered by SugarCRM".
* 
* SpiceCRM is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
* You should have received a copy of the GNU General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
********************************************************************************/

/**
 * The Dun & Bradstreet D‑U‑N‑S Number is a unique nine-digit identifier for businesses. ...
 * D‑U‑N‑S, which stands for data universal numbering system,
 * is used to maintain up-to-date and timely information on more than 300 million global businesses.
 * API provided by https://www.dnb.com/duns-number.html
 */

namespace SpiceCRM\includes\SpiceDuns;


use SoapClient;
use SpiceCRM\modules\Configurator\Configurator;

class SpiceDuns implements SpiceDunsInterface
{
    public $config = array();
    public $request_params = array();

    public function __construct()
    {
        //get duns api config
        $configurator = new Configurator();
        $configurator->loadConfig();
        $this->config = $configurator->config['duns'];
        $this->createSoapClient();
    }

    public function createSoapClient()
    {
        //prepare call
        if ($this->config['ssl']) {
            if ($this->config['ciphers'])
                $context = stream_context_create(
                    [
                        'ssl' => [
                            'ciphers' => $this->config['ciphers'],
                        ],
                    ]
                );
        }

        $params = array(
            'stream_context' => $context,
            //added maretval 2019-03-05: won't work without explicit location
            'location' => $this->config['location']
        );

        //call
        $this->client = new SoapClient($this->config['wsdl'], $params);

    }

    public function sendRequest($params = array())
    {
        // clean up params which contain "undefined"
        if(is_array($params)){
            foreach($params as $key => $value){
                $params[$key] = preg_replace("/undefined/", "", $value);
            }
        }

        $aSpecialChars1 = array('�', '�', '�', '�', "�", '�', '�');
        $aSpecialChars2 = array('ae', 'Ae', 'oe', 'Oe', "ue", 'Ue', 'ss');

        $params['name'] = str_replace($aSpecialChars1, $aSpecialChars2, $params['name']);
        $params['city'] = str_replace($aSpecialChars1, $aSpecialChars2, $params['city']);
        $params['street'] = str_replace($aSpecialChars1, $aSpecialChars2, $params['street']);

        $response = (
            $this->client->ws_LookUp(
                array(
                    'lookUpRequest' => array(
                        'UserId' => $this->config['UserId'],
                        'Password' => $this->config['Password'],
                        'lookUpInput' => array(
                            'Name' => $params['name'],
                            'Street_Address' => $params['street'],
                            'Town' => $params['city'],
                            //'PostTown'=>'',
                            //'State_or_Region'=>'CA',
                            'Post_Code' => $params['postalcode'],
                            //'DnB_DUNS_Number' => $params['dunsnumber'],
                            'Country_Code' => $params['country'],
                            //'Business_Number'=>'',
                            'Reason_Code' => '',
                            'Match_Type' => 'C',
                            'Max_Responses' => '100',
                            //'File_Id'=>'Test',
                            'Search_Rule_Code' => ''
                        )
                    )
                )
            )
        );
        // assign request parameters
        $this->request_params = $params;
        //return
        return $response;
    }

    /**
     * @param $response
     * @param $request_params params sent for request. Necessary to pass country value since not present in response
     * @return array
     */
    public function handleResponse($response)
    {
        $result = array();
        if (isset($response->lookupResponse->DGX->CREDITMSGSRSV2->LOOKUPTRNRS->LOOKUPRS->LOOKUPRSCOMPANY)) {
            if (isset($response->lookupResponse->DGX->CREDITMSGSRSV2->LOOKUPTRNRS->LOOKUPRS->LOOKUPRSCOMPANY->ArrayOfLOOKUPRSCOMPANYItem->DUNS_NBR)) {
                $item = $response->lookupResponse->DGX->CREDITMSGSRSV2->LOOKUPTRNRS->LOOKUPRS->LOOKUPRSCOMPANY->ArrayOfLOOKUPRSCOMPANYItem;
                $result[] = $this->mapResultItem($item);
            } else {
                foreach ($response->lookupResponse->DGX->CREDITMSGSRSV2->LOOKUPTRNRS->LOOKUPRS->LOOKUPRSCOMPANY->ArrayOfLOOKUPRSCOMPANYItem as $item) {
                    $result[] = $this->mapResultItem($item);
                }
            }
        }
        return $result;
    }

    /**
     * organize data for response
     * @param $item
     * @param array $request_params
     * @return array
     */
    private function mapResultItem($item)
    {
        //for sap.... try to seperate street name from house number
        $street_only = $item->ADR_LINE;
        $hsnm = "";
        if (preg_match('/([0-9]+)/', $item->ADR_LINE, $matches)) {
            $hsnm = $matches[0];
            $street_only = str_replace($hsnm, "", $item->ADR_LINE);
        }
        //store
        return array(
            'name' => $item->NME,
            'street' => $item->ADR_LINE,
            'street_only' => $street_only,
            'hsnm' => $hsnm,
            'postalcode' => $item->POST_CODE,
            'city' => $item->NON_POST_TOWN,
            'duns' => $item->DUNS_NBR,
            'country' => $this->request_params['country']
        );
    }

}
