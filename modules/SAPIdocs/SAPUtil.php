<?php
/**
 * Created by PhpStorm.
 * User: maretval
 * Date: 12.09.2018
 * Time: 15:09
 */

class SAPUtil
{
    public static function sapidocExtractPhoneDataForCrm($phone_data, $type = "TEL")
    {
        switch($type){
            case 'FAX':
                $key_phone_string = 'FAX';
                $key_phone_no = 'FAX_NO';
                $key_caller_no = 'SENDER_NO';
                $key_extension = 'EXTENSION';
                break;

            case 'TEL':
                $key_phone_string = 'TELEPHONE'; //02001 / 3232
                $key_phone_no = 'TEL_NO'; //+492001323222
                $key_caller_no = 'CALLER_NO'; //02001323222
                $key_extension = 'EXTENSION'; //22
                break;
        }
        //trim starting 0
        $phone_caller_no = (substr($phone_data[$key_caller_no],0,1) == "0" ? substr($phone_data[$key_caller_no],1) : $phone_data[$key_caller_no]);
        $phone_country = str_replace($phone_caller_no, "", $phone_data[$key_phone_no]);
        //trim starting 0
        $phone_number = (substr($phone_data[$key_phone_string],0,1) == "0" ? substr($phone_data[$key_phone_string],1) : $phone_data[$key_phone_string]);
        //check on slash. Add if not present to ensure correct formatting in editview
        if(!preg_match("/\//", $phone_number)) {
            if(preg_match("/\s/", $phone_number)){
                $phone_number = preg_replace("/\s/", " / ", $phone_number, 1);
            }
            else
                $phone_number.=" / ";
        }

        //return full phone string
        $phone = $phone_country . " " . $phone_number;

        if(isset($phone_data['R_3_USER']) && $phone_data['R_3_USER'] == 3) {
            return $phone;
        }
        return $phone . " - " . $phone_data[$key_extension];
    }
}


//$phone_data = array('TELEPHONE' => '2000 / 3245', 'TEL_NO' => '+492000324533', 'EXTENSION' => '33');
//echo SAPUtil::sapidocExtractPhoneDataForCrm($phone_data);