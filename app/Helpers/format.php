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
            $genderData = '';
            if ($value === 'Male' || $value === 'MALE' || $value == '1') {
                $genderData = 1;
            } else if ($value === 'Female' || $value === 'FEMALE' || $value == '2') {
                $genderData = 2;
            } else if ($value === 'Transgender Male' || $value == '3') {
                $genderData = 3;
            } else if ($value === 'Transgender Female' || $value == '4') {
                $genderData = 4;
            } else if ($value === 'Transgender (as non-binary)' || $value == '5') {
                $genderData = 5;
            } else if ($value === 'Non-binary' || $value == '6') {
                $genderData = 6;
            } else if ($value === 'Gender-queer' || $value == '7') {
                $genderData = 7;
            } else if ($value === 'Two-spirit' || $value == '8') {
                $genderData = 8;
            } else if ($value === 'Questioning/not sure' || $value == '9') {
                $genderData = 9;
            } else if ($value === 'Choose not to disclose' || $value == '10') {
                $genderData = 10;
            } else if ($value === 'Not listed, please describe' || $value == '11') {
                $genderData = 11;
            } else if ($value === 'Unknown' || $value == '12') {
                $genderData = 12;
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
            $genderData = '';

            if ($value == '1') {
                $genderData = 'Male';
            } else if ($value == '2') {
                $genderData = 'Female';
            } else if ($value == '3') {
                $genderData = 'Transgender Male';
            } else if ($value == '4') {
                $genderData = 'Transgender Female';
            } else if ($value == '5') {
                $genderData = 'Transgender (as non-binary)';
            } else if ($value == '6') {
                $genderData = 'Non-binary';
            } else if ($value == '7') {
                $genderData = 'Gender-queer';
            } else if ($value == '8') {
                $genderData = 'Two-spirit';
            } else if ($value == '9') {
                $genderData = 'Questioning/not sure';
            } else if ($value == '10') {
                $genderData = 'Choose not to disclose';
            } else if ($value == '11') {
                $genderData = 'Not listed, please describe';
            } else if ($value == '12') {
                $genderData = 'Unknown';
            }
            return $genderData;
        }
    }
