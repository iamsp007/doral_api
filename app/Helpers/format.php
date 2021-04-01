<?php

   
use Illuminate\Support\Facades\Hash;

    /**
     * Remove special character from phone value and store only digit.
     * 
     * @return int
     */
    if (!function_exists('setPhone')) {
        function setPhone($value)
        {
            $phone = '';
            if ($value) {
                $phone = preg_replace("/[^0-9]+/", "", $value);
            }

            return $phone;
        }
    }

    /**
     * Change default phone format to USA phone format (xxx) xxx-xxxx
     * 
     * @return string
     */
    if (!function_exists('getPhone')) {
        function getPhone($value)
        {
            return  "(" . substr($value, 0, 3) . ") " . substr($value, 3, 3) . "-" . substr($value, 6);
        }
    }

    /**
     * Change date format.
     * 
     * @return string
     */
    if (!function_exists('dateFormat')) {
        function dateFormat($value)
        {
            $date = '';
            if ($value) {
                $date = date('Y-m-d', strtotime($value));
            }

            return $date;
        }
    }

    /**
     * Create doral id.
     * 
     * @return string
     */
    if (!function_exists('createDoralId')) {
        function createDoralId()
        {
            return 'DOR-' . mt_rand(100000, 999999);
        }
    }

    /**
     * Create password.
     * 
     * @return string
     */
    if (!function_exists('setPassword')) {
        function setPassword($value)
        {
            return Hash::make($value);
        }
    }
    
    /**
     * Set Gender field.
     * 
     * @return int
     */
    if (!function_exists('setGender')) {
        function setGender($value)
        {
            if ($value === 'Male' || $value === 'MALE') {
                $genderData = 1;
            } else if ($value === 'Female' || $value === 'FEMALE') {
                $genderData = 2;
            } else {
                $genderData = 3;
            }
            return $genderData;
        }
    }

    /**
     * Remove - from ssn.
     *
     * @return string
     */
    if (!function_exists('setSsn')) {
        function setSsn($value)
        {
            $ssn = '';
            if ($value){
                $ssn = str_replace("-","",$value);
            }

            return $ssn;
        }
    }

    /**
     * Remove - from ssn.
     *
     * @return string
     */
    if (!function_exists('getSsn')) {
        function getSsn($value)
        {
            $ssnData = '';

            if ($value) {
                return 'xxx-xx-' . substr($value, -4);
            }
    
            return $ssnData;
        }
    }

    /**
     * Get the user's gender.
     *
     * @return string
     */
    if (!function_exists('getGender')) {
        function getGender($value)
        {
            if ($value === '1') {
                $genderData = 'Male';
            } else if ($value === '2') {
                $genderData = 'Female';
            } else {
                $genderData = 'Other';
            }
            return $genderData;
        }
    }
