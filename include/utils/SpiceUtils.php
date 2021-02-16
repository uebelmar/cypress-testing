<?php

namespace SpiceCRM\includes\utils;

use DateTime;
use SpiceCRM\includes\SugarObjects\SpiceConfig;

/**
 * Class SpiceUtils
 *
 * A Helper class containing static functions used throughout the system.
 */
class SpiceUtils
{
    /**
     * catches the request_uri and php_self and checks if one of them matches the allowed backend paths
     * @return mixed|string|null
     */
    public static function determineAppBasePath()
    {
        /**
         * todo soap testen...
         * localhost/_project/backend
         * localhost/_projects/kundenname/backend
         * spicecrm.local/
         * localhost/api
         * spicecrm.local/api
         */
        return dirname($_SERVER['PHP_SELF']);
    }

    /**
     * A temporary method of generating GUIDs of the correct format for our DB.
     * @return String contianing a GUID in the format: aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee
     *
     * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
     * All Rights Reserved.
     * Contributor(s): ______________________________________..
     */
    public static function createGuid()
    {
        $microTime = microtime();
        list($a_dec, $a_sec) = explode(" ", $microTime);

        $dec_hex = dechex($a_dec * 1000000);
        $sec_hex = dechex($a_sec);

        ensure_length($dec_hex, 5);
        ensure_length($sec_hex, 6);

        $guid = "";
        $guid .= $dec_hex;
        $guid .= create_guid_section(3);
        $guid .= '-';
        $guid .= create_guid_section(4);
        $guid .= '-';
        $guid .= create_guid_section(4);
        $guid .= '-';
        $guid .= create_guid_section(4);
        $guid .= '-';
        $guid .= $sec_hex;
        $guid .= create_guid_section(6);

        return $guid;
    }

    /**
     * Returns a translated abbreviation for a week day.
     * In case no translation is provided, just the default english version is returned.
     *
     * @param DateTime $date
     * @param string $language
     * @return string
     */
    public static function getShortWeekdayName(DateTime $date, string $language = 'de_DE'): string
    {
        // todo move that array once it grows.
        $weekdayStrings = [
            'de_DE' => [
                'Mon' => 'Mo',
                'Tue' => 'Di',
                'Wed' => 'Mi',
                'Thu' => 'Do',
                'Fri' => 'Fr',
                'Sat' => 'Sa',
                'Sun' => 'So',
            ],
        ];

        $weekday = $date->format('D');

        return ($weekdayStrings[$language][$weekday]) ?? $weekday;
    }


    /**
     * get max value for property in array of objects
     * @param array $arr
     * @param string $property
     * @return mixed
     */
    public static function getMaxDate($arr, $property)
    {
        return max(array_column($arr, $property));
    }

    /**
     * get min value for property in array of objects
     * @param array $arr
     * @param string $property
     * @return mixed
     */
    public static function getMinDate($arr, $property)
    {
        return min(array_column($arr, $property));
    }
}
