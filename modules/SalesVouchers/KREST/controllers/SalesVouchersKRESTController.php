<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
namespace SpiceCRM\modules\SalesVouchers\KREST\controllers;

use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\ErrorHandlers\BadRequestException;
use SpiceCRM\includes\authentication\AuthenticationController;

class SalesVouchersKRESTController {

    /**
     *  Creates the sales document for a voucher sale.
     *  In case the request comes from outside the CRM, all the parameters must be checked exactly!
     */
    function buyVouchers( $req, $res, $args ) {
        $db = DBManagerFactory::getInstance();

        /*

        example of json body parameters:

        {
            "buyer": {
                "last_name": "Huber",
                "email_address": "abc@abc4711.com"
            },
            "vouchers": [
                {
                    "quantity": "1",
                    "value": 100,
                    "product_id":"53f4ce27-c4d3-110d-5ae4-6272d1688fcf"
                },
                {
                    "quantity": 2,
                    "value": "50",
                    "product_id":"53f4ce27-c4d3-110d-5ae4-6272d1688fcf"
                }
            ],
            "companycode":"c15fe9e9-9b4f-83e7-7944-5119f2953304",
            "incoterm1":"email",
            "incoterm2":"abc@abc4711.com"
        }

        */

        $params = json_decode( $req->getBody(), true);
        if ( json_last_error() !== JSON_ERROR_NONE ) throw new BadRequestException('Invalid JSON data.');

        if ( !isset( $params['buyer'] )) {
            throw new BadRequestException('Buyer data missing.');
        }
        if ( !isset( $params['vouchers'] ) or !is_array( $params['vouchers'] )) {
            throw new BadRequestException('Voucher data missing.');
        }
        $buyer = $params['buyer'];
        $vouchers = $params['vouchers'];

        # The fields of the consumer bean that are accepted:
        $consumerInputConfig = [
            'salutation' => self::getFieldConfig('Consumer','salutation'),
            'first_name' => self::getFieldConfig('Consumer','first_name'),
            'last_name' => self::getFieldConfig('Consumer','last_name'),
            'degree1' => self::getFieldConfig('Consumer','degree1'),
            'degree2' => self::getFieldConfig('Consumer','degree2'),
            # 'title_dd' => self::getFieldConfig('Consumer','title_dd'),
            # 'title' => self::getFieldConfig('Consumer','title'),
            'primary_address_street' => self::getFieldConfig('Consumer','primary_address_street'),
            'primary_address_street_number' => self::getFieldConfig('Consumer','primary_address_street_number'),
            'primary_address_street_number_suffix' => self::getFieldConfig('Consumer','primary_address_street_number_suffix'),
            'primary_address_attn' => self::getFieldConfig('Consumer','primary_address_attn'),
            'primary_address_city' => self::getFieldConfig('Consumer','primary_address_city'),
            'primary_address_state' => self::getFieldConfig('Consumer','primary_address_state'),
            'primary_address_postalcode' => self::getFieldConfig('Consumer','primary_address_postalcode'),
            'primary_address_pobox' => self::getFieldConfig('Consumer','primary_address_pobox'),
            'primary_address_country' => self::getFieldConfig('Consumer','primary_address_country'),
        ];

        # The fields of the voucher bean that are accepted:
        $vouchersInputConfig = [
            'value' => self::getFieldConfig('SalesDocItem','amount_net_per_uom'),
            'quantity' => self::getFieldConfig('SalesDocItem','quantity'),
            'design' => ['required' => false, 'type' => 'string'],
            'product_id' => ['required' => true, 'type' => 'string']
        ];#var_dump($vouchersInputConfig);exit;

        # trim fields of consumer data:
        foreach ( $consumerInputConfig as $k => $v ) {
            if ( isset( $buyer[$k] ) and is_string( $buyer[$k] )) $buyer[$k] = trim( $buyer[$k] );
        }
        # trim fields of voucher data (multiple vouchers):
        foreach ( $vouchers as $voucherKey => $voucherInput ) {
            foreach ( $vouchersInputConfig as $configKey => $v ) {
                if ( isset( $voucherInput[$k] ) and is_string( $voucherInput[$k] ) ) $vouchers[$voucherKey][$configKey] = trim( $voucherInput[$k] );
            }
        }
        if ( isset( $buyer['email_address'] )) $buyer['email_address'] = trim( $buyer['email_address'] ); # trim email address
        if ( isset( $params['companycode'] )) $params['companycode'] = trim( $params['companycode'] ); # trim companycode
        # if ( isset( $params['shipping_method'] )) $params['shipping_method'] = trim( $params['shipping_method'] ); # trim shipping_method
        if ( isset( $params['incoterm1'] )) $params['incoterm1'] = trim( $params['incoterm1'] ); # trim companycode
        if ( isset( $params['incoterm1'] )) $params['incoterm1'] = trim( $params['incoterm1'] ); # trim companycode

        # The email address is required!
        if ( empty( $buyer['email_address'] ) or !is_string( $buyer['email_address'] )) {
            throw new BadRequestException('Email Address missing or invalid.');
        }
        # The company code is required!
        if ( empty( $params['companycode'] ) or !is_string( $params['companycode'] )) {
            throw new BadRequestException('Email Address missing or invalid.');
        }
        # The shipping method is required!
        #if ( empty( $params['shipping_method'] ) or !is_string( $params['shipping_method'] ) or ( $params['shipping_method'] !== 'email' and $params['shipping_method'] !== 'letter_post' )) {
        #    throw new BadRequestException('Shipping method missing or invalid.');
        #}
        # todo: Are incoterm1/incoterm2 required?

        # Do we have a consumer with the given email address already? Then use/load the existing consumer. Otherwise create a new consumer.
        $consumer = null;
        $emailAddress =  BeanFactory::getBean("EmailAddresses");
        $emailAddress->retrieve_by_string_fields([ 'email_address_caps' => strtoupper( $buyer['email_address'] )]);
        if ( $emailAddress ) {
            $consumerRecord = $db->fetchOne( 'SELECT bean_id FROM email_addr_bean_rel WHERE bean_module = "Consumers" AND email_address_id = "' . $emailAddress->id . '" AND deleted <> 1' );
            if ( !empty( $consumerRecord ) ) {
                $consumer = BeanFactory::getBean('Consumers', $consumerRecord['bean_id'] );
            }
        }
        if ( !$consumer ) {
            $consumer = BeanFactory::getBean('Consumers');
            foreach ( $consumerInputConfig as $fieldName => $config ) {
                if ( isset( $buyer[$fieldName] )) $buyer[$fieldName] = self::sanitizeInput( $buyer[$fieldName], $fieldName, $consumerInputConfig[$fieldName] );
                self::checkInput( $buyer[$fieldName], $fieldName, $consumerInputConfig[$fieldName] );
                if ( isset( $buyer[$fieldName] )) $consumer->$fieldName = $buyer[$fieldName];
            }
            $consumer->save();

            # Save/assign the email address to the consumer bean:
            $emailAddresses = BeanFactory::getBean('EmailAddresses');
            $emailAddresses->addresses = [[
                'primary_address' => true,
                'email_address' => $buyer['email_address'],
                'email_address_caps' => strtoupper( $buyer['email_address'] )
            ]];
            $emailAddresses->save( $consumer->id, 'Consumers');
        }

        # Create the sales document:
        $salesDoc = BeanFactory::getBean('SalesDocs');
        $salesDoc->consumer_op_id = $consumer->id;
        $salesDoc->salesdoccategory = 'VS';
        $salesDoc->saleddoc_status = 'created';
        $salesDoc->companycode_id = $params['companycode'];
        $salesDoc->salesdoctype = 'VS_B2CONS';
        $salesDoc->currency_id = -99; # todo?
        $salesDoc->salesdocparty = 'I';
        $salesDoc->paymentterms = '30DN'; # todo?
        $salesDoc->salesdocdate = date('Y-m-d');
        $salesDoc->assigned_user_id = AuthenticationController::getInstance()->getCurrentUser()->id;
        # $salesDoc->shipping_method = $params['shipping_method']; # ???
        $salesDoc->incoterm1 = $params['incoterm1'];
        $salesDoc->incoterm2 = $params['incoterm2'];

        $salesDoc->save();

        # Create the sales document items:
        $itemNr = 0;
        foreach ( $vouchers as $voucherInput ) {
            $itemNr += 10;
            foreach ( $vouchersInputConfig as $fieldName => $field ) {
                if ( isset( $voucherInput[$fieldName] )) $voucherInput[$fieldName] = self::sanitizeInput( $voucherInput[$fieldName], $fieldName, $vouchersInputConfig[$fieldName] );
                self::checkInput( $voucherInput[$fieldName], $fieldName, $vouchersInputConfig[$fieldName] );
            }
            $salesDocItem = BeanFactory::getBean( 'SalesDocItems' );
            $salesDocItem->tax_category = 'V0';
            $salesDocItem->quantity = $voucherInput['quantity'];
            $salesDocItem->amount_net_per_uom = $voucherInput['value'];
            $salesDocItem->amount_net = $voucherInput['value'] * $voucherInput['quantity'];
            $salesDocItem->amount_gross = $salesDocItem->amount_net;
            $salesDocItem->uom_id = 'e2fc3ab2-fab7-cf77-8a15-d90519cd6f98';
            $salesDocItem->salesdoc_id = $salesDoc->id;
            $salesDocItem->itemnr = $itemNr;
            # $salesDocItem->design = $voucherInput['design']; # ???
            $salesDocItem->product_id = $voucherInput['product_id'];
            $salesDocItem->itemtype = 'VSP';
            # todo: check if product exists, when not: bad request error
            $salesDocItem->save();
        }
        return $res->withJson(['success'=>true,'salesdoc_number'=>$salesDoc->salesdocnumber]);

    }

    function getFieldConfig( $beanname, $fieldname ) {
        $config = ['required'=>false];
        $dummy = new $beanname(); // workaround to fill dictionary
        $vardefs = $GLOBALS['dictionary'][$beanname]['fields'];
        if ( @$vardefs[$fieldname]['required'] ) $config['required'] = true;
        if ( preg_match( '/^varchar|char|text$/', $vardefs[$fieldname]['type'])) {
            $config['type'] = 'string';
            if ( isset( $vardefs[$fieldname]['len'] )) $config['length'] = $vardefs[$fieldname]['len'];
        }
        elseif ( preg_match( '/^uint|ulong|long|short|tinyint|int$/', $vardefs[$fieldname]['type'])) $config['type'] = 'int';
        elseif ( $vardefs[$fieldname]['type'] === 'enum' ) {
            $config['type'] = 'enum';
            $config['enumValues'] = array_keys( $GLOBALS['app_list_strings'][$vardefs[$fieldname]['options']] );
        }
        return $config;
    }

    function checkInput( $value = null, $fieldname, $config )
    {
        if ( !isset( $value )) {
            if ( $config['required'] ) throw new BadRequestException('Missing field "' . $fieldname . '"');
            return;
        }
        switch ( $config['type'] ) {
            case 'string':
                if ( !is_string( $value ) ) throw new BadRequestException('Field "' . $fieldname . '" is not a string.');
                if ( $config['required'] and empty( $value ) ) throw new BadRequestException('Missing field "' . $fieldname . '"');
                break;
            case 'int':
                if ( $value <= 0 ) throw new BadRequestException('Invalid value in field "' . $fieldname . '"');
                break;
            case 'enum':
                if ( 0 and !in_array( $value, $config['enumValues'] )) throw new BadRequestException('Invalid value in field "' . $fieldname . '"');
                break;
        }
    }

    function sanitizeInput( $value = null, $fieldname, $config )
    {
        # 123 -> "123" if string required
        if ( is_int( $value ) and $config['type'] === 'string') $value = (string)$value;
        # "123" -> 123 if integer required
        elseif ( is_string( $value ) and $config['type'] === 'int') {
            $value = trim( $value );
            if ( $value === '' ) $value = null;
            elseif ( !is_numeric( $value )) throw new BadRequestException( 'Invalid value in field "' . $fieldname . '"' );
            else $value = (int)$value;
        }
        return $value;
    }

}

