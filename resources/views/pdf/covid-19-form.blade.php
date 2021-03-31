<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>COVID-19 FORM DATA</title>
    <style type="text/css">
    body {
        font-size: 16px;
        font-family: Arial;
        margin: 0;
        padding: 20px;
        -webkit-text-size-adjust: 100% !important;
        -ms-text-size-adjust: 100% !important;
        -webkit-font-smoothing: antialiased !important;
    }

    table {
        border-collapse: collapse;
        mso-table-lspace: 0;
        mso-table-rspace: 0;
    }

    td,
    a,
    span {
        border-collapse: collapse;
        mso-line-height-rule: exactly;
    }

    th,
    td {
        text-align: left;
    }

    #myid span {
        font-weight: bold
    }

    .border_right {
        border-right: 1px solid black;
    }

    .border_left {
        border-left: 1px solid black;
    }

    .border_top {
        border-top: 1px solid black;
    }

    .border_bottom {
        border-bottom: : 1px solid black;
    }

    .width_50 {
        width: 50%;
    }

    .checkbox {
        width: 20px;
        height: 20px;
    }

    .width_100 {
        width: 100%;
    }

    .width_30 {
        width: 30%;
    }

    .radio {
        height: 17px;
        margin-right: 10px;
        width: 17px;
    }

    input {
        width: 99%;
        background-color: #e2eff3;
        background: #e2eff3;
        height: 30px;
        color: black;
        border: 1px solid #d3ceec;
        margin-top: 10px;
    }

    p {
        font-weight: bold;
        margin-bottom: 0px;
        margin-top: 5px;
        font-size: 12px;
        line-height: 18px;
    }

    div {
        padding: 10px;
    }

    .font-weight {
        font-weight: 500;
    }

    </style>
</head>

<body>
    <img src="/assets/img/img1.png" />
    <div style="font-size:18px; color:#000; text-align:center; font-weight:bold; margin-bottom:10px;">COVID-19 Immunization Screening and Consent Form*</div>
    <table width="100%" border="0" cellspacing="0" cellpadding="0" style=" border:1px solid black;" class="width_100">
        <tr>
            <td>
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td class="width_50 border_right">
                            <div>
                                <p>Recipient Name (please print)</p>
                                <input type="text" name="value" value="{{ isset($data['recipient_name']) ? $data['recipient_name'] : '' }}">
                            </div>
                        </td>
                        <td class="width_50 border_right  ">
                            <div>
                                <p>Preferred Name</p>
                                <input type="text" name="value" value="{{ isset($data['preferred_name']) ? $data['preferred_name'] : '' }}">
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td class="border_top" style="padding:0">
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td class="width_50  border_right" valign="top" style="padding:0">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td>
                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td class="width_50 ">
                                                    <div>
                                                        <p>DOB</p>
                                                        <input type="text" name="value" value="{{ isset($data['dob']) ? $data['dob'] : '' }}">
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td class="  width_50" valign="top">
                            <div>
                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td>
                                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                <tr>
                                                    <td width="50%">
                                                        <p>Current Gender ID</p>
                                                        <input type="text" name="value" value="{{ isset($data['gender_id']) ? $data['gender_id'] : '' }}">
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr id="myid">
                                        <td class="">
                                            <table style="border:1px solid black;font-size:12px;" width="100%" cellpadding="3" border="1">
                                                <p style="margin-bottom: 10px;">Key:</p>
                                                <tr>
                                                    <td><span> W</span> – Woman/Girl </td>
                                                    <td><span> TW</span> – Transgender Woman/Girl </td>
                                                    <td> <span>M</span> – Man/Boy </td>
                                                </tr>
                                                <tr>
                                                    <td><span> TM</span> – Transgender Man/Boy </td>
                                                    <td><span> NB </span>– Non-Binary Person </td>
                                                    <td> GNC – Gender Non-Conforming </td>
                                                </tr>
                                                <tr>
                                                    <td> <span>Q </span>– Not Sure/Questioning </td>
                                                    <td><span> NR</span> – Chose not to Respond </td>
                                                    <td><span> GNL</span> – Gender not Listed (write-in) </td>
                                                </tr>
                                            </table>
                                            <br>
                                            <span style="font-size:12px;">* Gender Pronouns: write-in by client’s name</span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td class="border_top">
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td class=" border_right width_50" valign="top">
                            <div>
                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td>
                                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                <tr>
                                                    <td width="100%">
                                                        <p>Sex Assigned at Birth
                                                            indicate Sex Below:</p>
                                                        <input type="text" name="value" value="{{ isset($data['sex_at_birth']) ? $data['sex_at_birth'] : '' }}">
                                                    </td>
                                                    <td></td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <table style="border:1px solid black;padding:5px; font-size:12px;" width="100%" cellpadding="3" id="myid" border="1">
                                                <p style="margin-bottom: 10px;">Key:</p>
                                                <tr>
                                                    <td>
                                                        <span> M</span> – Male
                                                    </td>
                                                    <td>
                                                        <span>F</span> – Female
                                                    </td>
                                                    <td>
                                                        <span>I</span> – Intersex
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <span>NR</span> – Chose not to Respond
                                                    </td>
                                                    <td colspan="2">
                                                        <span>SNL</span> – Sexual Orientation not Listed (write-in)
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                        <td class="  width_50" valign="top">
                            <div>
                                <table width="100%" border="0" cellspacing="0" cellpadding="0" id="myid">
                                    <tr>
                                        <td style="width: 20%;" class="">
                                            <p>Marital Status:</p>
                                            <input type="text" name="value" value="{{ isset($data['marital_staus']) ? $data['marital_staus'] : '' }}">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <table width="100%" border="0" cellspacing="0" cellpadding="3">
                                                <tr>
                                                    <td class="">
                                                        <table style="font-size:12px;" border="1" cellspacing="0" cellpadding="3" width="100%">
                                                            <p style="margin-bottom: 10px;">Key:</p>
                                                            <tr>
                                                                <td>
                                                                    <span> S </span> – Single
                                                                </td>
                                                                <td>
                                                                    <span> D</span> – Divorced
                                                                </td>
                                                                <td>
                                                                    <span> M</span> – Married
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <span> W</span> – Widowed
                                                                </td>
                                                                <td>
                                                                    <span> V</span> – Civil Union
                                                                </td>
                                                                <td>
                                                                    <span> U </span> – Unknown </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <span> U SEPARATED</span> – Legally Separated
                                                                </td>
                                                                <td colspan="2">
                                                                    <span> PARTNER</span> – Life Partner
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td style="width: 33%;" class="border_right border_top">
                            <div>
                                <p>Parent/Guardian/ Surrogate (if applicable, please print)</p>
                                <input type="text" name="value" value="{{ isset($data['parent_name']) ? $data['parent_name'] : '' }}">
                            </div>
                        </td>
                        <td style="width: 33%;" class="border_right border_top">
                            <div>
                                <p>Phone</p>
                                <input type="text" name="value" value="{{ isset($data['phone']) ? $data['phone'] : '' }}">
                            </div>
                        </td>
                        <td style="width: 33%;" class=" border_top">
                            <div>
                                <p>Preferred Language</p>
                                <input type="text" name="value" value="{{ isset($data['preferred_language']) ? $data['preferred_language'] : '' }}">
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td class="border_top">
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td class=" border_right width_50" valign="top">
                            <div>
                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td>
                                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                <tr>
                                                    <td width="100%">
                                                        <p>Ethnicity
                                                            Indicate Ethnicity Below:</p>
                                                        <input type="text" name="value" value="{{ isset($data['ethnicity']) ? $data['ethnicity'] : '' }}">
                                                    </td>
                                                    <td></td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <table style="border:1px solid black;padding:5px; font-size:12px;" width="100%" cellpadding="3" id="myid" border="1">
                                                <p style="margin-bottom: 10px;">Ethnicity Key:</p>
                                                <tr>
                                                    <td>
                                                        <span>DECL</span> – Declined
                                                    </td>
                                                    <td>
                                                        <span>HIS</span> – Hispanic Origin
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <span>NHL </span>– Non-Hispanic Origin
                                                    </td>
                                                    <td>
                                                        <span>UNK</span> - Unknown </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                        <td class="  width_50" valign="top">
                            <div>
                                <table width="100%" border="0" cellspacing="0" cellpadding="0" id="myid">
                                    <tr>
                                        <td style="width: 20%;" class="">
                                            <p>Race
                                                Indicate Race Below:</p>
                                            <input type="text" name="value" value="{{ isset($data['race']) ? $data['race'] : '' }}">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <table width="100%" border="0" cellspacing="0" cellpadding="3">
                                                <tr>
                                                    <td class="">
                                                        <table style="font-size:12px;" border="1" cellspacing="0" cellpadding="3" width="100%">
                                                            <p style="margin-bottom: 10px;">Race Key:</p>
                                                            <tr>
                                                                <td>
                                                                    <span>AIA </span>– Native American or Alaskan ASN – Asian
                                                                </td>
                                                                <td>
                                                                    <span>ASN </span>- Asian
                                                                </td>
                                                                <td>
                                                                    <span>BAA</span> – African American or Black
                                                                </td>
                                                                <td>
                                                                    <span>DECL</span> – Declined
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <span>NHP</span> – Native Hawaiian or Pacific Islander
                                                                </td>
                                                                <td>
                                                                    <span> WHT</span> – White
                                                                </td>
                                                                <td colspan="2">
                                                                    <span> OTH </span>– Other or Multiracial
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td>
                            <table width="100%" border="0" style="font-size:12px;">
                                <tr>
                                    <td style="width: auto;" class=" border_top border_right">
                                        <div>
                                            <p>Address:</p>
                                            <input type="text" name="value" value="{{ isset($data['address']) ? $data['address'] : '' }}">
                                        </div>
                                    </td>
                                    <td style="width: auto;" class="border_right border_top">
                                        <div>
                                            <p>City:</p>
                                            <span class=" border_top" style="width: auto;">
                                                <input type="text" name="value2" value="{{ isset($data['city']) ? $data['city'] : '' }}" />
                                            </span>
                                        </div>
                                    </td>
                                    <td style="width: auto;" class="border_right border_top">
                                        <div>
                                            <p>State:</p>
                                            <input type="text" name="value" value="{{ isset($data['state']) ? $data['state'] : '' }}">
                                        </div>
                                    </td>
                                    <td style="width: auto;" class="border_right border_top">
                                        <div>
                                            <p>Zip:</p>
                                            <input type="text" name="value" value="{{ isset($data['zip']) ? $data['zip'] : '' }}">
                                        </div>
                                    </td>
                                    <td style="width: auto;" class=" border_top">
                                        <div>
                                            <p>Email Address:</p>
                                            <input type="text" name="value" value="{{ isset($data['email']) ? $data['email'] : '' }}">
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td class="border_top">
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td style="width: 25%;" class="border_right  ">
                            <div>
                                <p>Primary Insurance Name</p>
                                <input type="text" name="value" value="{{ isset($data['primary_insurance_name']) ? $data['primary_insurance_name'] : '' }}">
                            </div>
                        </td>
                        <td style="width: 25%;" class="border_right">
                            <div>
                                <p>Primary Insurance ID#</p>
                                <input type="text" name="value" value="{{ isset($data['primary_insurance_id']) ? $data['primary_insurance_id'] : '' }}">
                            </div>
                        </td>
                        <td style="width: 25%;" class="border_right ">
                            <div>
                                <p>Subscriber Name/DOB</p>
                                <input type="text" name="value" value="{{ isset($data['primary_subscriber_dob']) ? $data['primary_subscriber_dob'] : '' }}">
                            </div>
                        </td>
                        <td style="width:25%;" class=" ">
                            <div>
                                <p>Subscriber Relation
                                    to Patient</p>
                                <input type="text" name="value" value="{{ isset($data['primary_relation_patient']) ? $data['primary_relation_patient'] : '' }}">
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td class="border_top">
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td style="width: 25%;" class="border_right  ">
                            <div>
                                <p>Primary Insurance Address</p>
                                <input type="text" name="value" value="{{ isset($data['primary_insurance_address']) ? $data['primary_insurance_address'] : '' }}">
                            </div>
                        </td>
                        <td style="width: 25%;" class="border_right">
                            <div>
                                <p>Primary Insurance Group #</p>
                                <input type="text" name="value" value="{{ isset($data['primary_insurance_group']) ? $data['primary_insurance_group'] : '' }}">
                            </div>
                        </td>
                        <td style="width:50%;" class="border_right ">
                            <div>
                                <p>Primary Insurance Phone #</p>
                                <input type="text" name="value" value="{{ isset($data['primary_insurance_phone']) ? $data['primary_insurance_phone'] : '' }}">
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td class="border_top">
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td style="width: 25%;" class="border_right  ">
                            <div>
                                <p>Secondary Insurance Name</p>
                                <input type="text" name="value" value="{{ isset($data['secondary_insurance_name']) ? $data['secondary_insurance_name'] : '' }}">
                            </div>
                        </td>
                        <td style="width: 25%;" class="border_right">
                            <div>
                                <p>Secondary Insurance ID#</p>
                                <input type="text" name="value" value="{{ isset($data['secondary_insurance_id']) ? $data['secondary_insurance_id'] : '' }}">
                            </div>
                        </td>
                        <td style="width: 25%;" class="border_right ">
                            <div>
                                <p>Subscriber Name/DOB</p>
                                <input type="text" name="value" value="{{ isset($data['secondary_subscriber_dob']) ? $data['secondary_subscriber_dob'] : '' }}">
                            </div>
                        </td>
                        <td style="width:25%;" class=" ">
                            <div>
                                <p>Subscriber Relation
                                    to Patient</p>
                                <input type="text" name="value" value="{{ isset($data['secondary_relation_patient']) ? $data['secondary_relation_patient'] : '' }}">
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td class="border_top">
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td style="width: 25%;" class="border_right  ">
                            <div>
                                <p>Secondary Insurance Address</p>
                                <input type="text" name="value" value="{{ isset($data['secondary_insurance_address']) ? $data['secondary_insurance_address'] : '' }}">
                            </div>
                        </td>
                        <td style="width: 25%;" class="border_right">
                            <div>
                                <p>Secondary Insurance Group #</p>
                                <input type="text" name="value" value="{{ isset($data['secondary_insurance_group']) ? $data['secondary_insurance_group'] : '' }}">
                            </div>
                        </td>
                        <td style="width:50%;" class="border_right ">
                            <div>
                                <p>Secondary Insurance Phone #</p>
                                <input type="text" name="value" value="{{ isset($data['secondary_insurance_phone']) ? $data['secondary_insurance_phone'] : '' }}">
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td style="width: 50%;" class="border_right border_top">
                            <div>
                                <p>Clinic/Office Site Where Vaccine is Administered</p>
                                <input type="text" name="value" value="{{ isset($data['clinic_site']) ? $data['clinic_site'] : '' }}">
                            </div>
                        </td>
                        <td style="width: 50%;" class=" border_top">
                            <div>
                                <p>Primary Care Physician Address/Phone Number</p>
                                <input type="text" name="value" value="{{ isset($data['primary_care']) ? $data['primary_care'] : '' }}">
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>
                <table width="100%" border="1" cellspacing="0" cellpadding="5" style="font-size:14px;">
                    <tr>
                        <td valign="top" colspan="5" style="background-color:#CCC; font-size:14px; text-align:center">
                            <h2 style="background-color:#CCC; font-size:14px; text-align:center"> Screening Questionnaire</h2>
                        </td>
                    </tr>
                    <tr>
                        <td width="15" valign="top">1</td>
                        <td>Are you feeling sick today?</td>
                        <td width="100"><input type="checkbox" class="checkbox" style="padding-right:20px;" />Yes </td>
                        <td width="100"><input type="checkbox" class="checkbox" style="padding-right:20px;" />No </td>
                        <td width="100"><input type="checkbox" class="checkbox" style="padding-right:20px;" />Unknown </td>
                    </tr>
                    <tr>
                        <td width="15" valign="top">2</td>
                        <td>In the last 10 days, have you had a COVID-19 test because you had symptoms and are still
                            awaiting your test results or been told by a health care provider or health department to
                            isolate or quarantine at home due to COVID-19 infection, exposure or travel?</td>
                        <td width="100" valign="top"><input type="checkbox" class="checkbox" style="padding-right:20px;" />Yes </td>
                        <td width="100" valign="top"><input type="checkbox" class="checkbox" style="padding-right:20px;" />No </td>
                        <td width="100" valign="top"><input type="checkbox" class="checkbox" style="padding-right:20px;" />Unknown </td>
                    </tr>
                    <tr>
                        <td width="15" valign="top">3</td>
                        <td valign="top">Have you been treated with antibody therapy or convalescent plasma for COVID-19 in the past
                            90 days (3 months)? If <input type="checkbox" class="checkbox" style="padding-right:20px;" />Yes, when did you receive the last dose?
                            <p style="display:inline-block; ">Date:</p> <input type="text" name="value" width="50%"></td>
                        <td width="100" valign="top"><input type="checkbox" class="checkbox" style="padding-right:20px;" />Yes </td>
                        <td width="100" valign="top"><input type="checkbox" class="checkbox" style="padding-right:20px;" />No </td>
                        <td width="100" valign="top"><input type="checkbox" class="checkbox" style="padding-right:20px;" />Unknown </td>
                    </tr>
                    <tr>
                        <td width="15" valign="top">4</td>
                        <td>Have you ever had an immediate allergic reaction (e.g., hives,facialswelling, difficulty breathing,
                            anaphylaxis)to any vaccine, injection, orshot orto any component ofthe COVID-19 vaccine, or a
                            severe allergic reaction (anaphylaxis) to anything?</td>
                        <td width="100" valign="top"><input type="checkbox" class="checkbox" style="padding-right:20px;" />Yes </td>
                        <td width="100" valign="top"><input type="checkbox" class="checkbox" style="padding-right:20px;" />No </td>
                        <td width="100" valign="top"><input type="checkbox" class="checkbox" style="padding-right:20px;" />Unknown </td>
                    </tr>
                    <tr>
                        <td width="15" valign="top">5</td>
                        <td>Have you had any vaccines in the past 14 days (2 weeks) including flu shot?
                            If <input type="checkbox" class="checkbox" style="padding-right:20px;" />Yes, how long ago was your mostrecent vaccine?
                            <p style="display:inline-block; ">Date:</p> <input type="text" name="value" width="50%"></td>
                        <td width="100" valign="top"><input type="checkbox" class="checkbox" style="padding-right:20px;" />Yes </td>
                        <td width="100" valign="top"><input type="checkbox" class="checkbox" style="padding-right:20px;" />No </td>
                        <td width="100" valign="top"><input type="checkbox" class="checkbox" style="padding-right:20px;" />Unknown </td>
                    </tr>
                    <tr>
                        <td width="15" valign="top">6</td>
                        <td>Are you pregnant or considering becoming pregnant?</td>
                        <td width="100" valign="top"><input type="checkbox" class="checkbox" style="padding-right:20px;" />Yes </td>
                        <td width="100" valign="top"><input type="checkbox" class="checkbox" style="padding-right:20px;" />No </td>
                        <td width="100" valign="top"><input type="checkbox" class="checkbox" style="padding-right:20px;" />Unknown </td>
                    </tr>
                    <tr>
                        <td width="15" valign="top">7</td>
                        <td>Do you have cancer,leukemia,HIV/AIDS, a historyof autoimmunediseaseor anyother condition
                            that weakens the immune system?</td>
                        <td width="100" valign="top"><input type="checkbox" class="checkbox" style="padding-right:20px;" />Yes </td>
                        <td width="100" valign="top"><input type="checkbox" class="checkbox" style="padding-right:20px;" />No </td>
                        <td width="100" valign="top"><input type="checkbox" class="checkbox" style="padding-right:20px;" />Unknown </td>
                    </tr>
                    <tr>
                        <td width="15" valign="top">8</td>
                        <td>Do youtake anymedicationsthat affect yourimmune system,such as cortisone, prednisone or
                            othersteroids, anticancer drugs, or have you had any radiation treatments?</td>
                        <td width="100" valign="top"><input type="checkbox" class="checkbox" style="padding-right:20px;" />Yes </td>
                        <td width="100" valign="top"><input type="checkbox" class="checkbox" style="padding-right:20px;" />No </td>
                        <td width="100" valign="top"><input type="checkbox" class="checkbox" style="padding-right:20px;" />Unknown </td>
                    </tr>
                    <tr>
                        <td width="15" valign="top">9</td>
                        <td>Do you have a bleeding disorder or are you taking a blood thinner?</td>
                        <td width="100" valign="top"><input type="checkbox" class="checkbox" style="padding-right:20px;" />Yes </td>
                        <td width="100" valign="top"><input type="checkbox" class="checkbox" style="padding-right:20px;" />No </td>
                        <td width="100" valign="top"><input type="checkbox" class="checkbox" style="padding-right:20px;" />Unknown </td>
                    </tr>
                    <tr>
                        <td width="15" valign="top">10</td>
                        <td>Have you received a previous dose ofthe COVID-19 vaccine?
                            <br />
                            If <input type="checkbox" class="checkbox" style="padding-right:20px;" />Yes,which vaccine?
                            <p> <input type="checkbox" class="checkbox" style="padding-right:20px;" />Moderna</p>
                            <p> <input type="checkbox" class="checkbox" style="padding-right:20px;" />Pfizer</p>
                        </td>
                        <td width="100" valign="top"><input type="checkbox" class="checkbox" style="padding-right:20px;" />Yes </td>
                        <td width="100" valign="top"><input type="checkbox" class="checkbox" style="padding-right:20px;" />No </td>
                        <td width="100" valign="top"><input type="checkbox" class="checkbox" style="padding-right:20px;" />Unknown </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>
                <div>
                    <h2 style="font-size:14px;">Emergency Use Authorization
                    </h2>
                    <p class="font-weight">The FDA has made the COVID-19 vaccine available under an emergency use authorization (EUA). The EUA is used when circumstances exist
                        to justify the emergency use of drugs and biological products during an emergency, such as the COVID-19 pandemic. This vaccine has not
                        undergone the same type of review as an FDA-approved or cleared product. However, the FDA’s decision to make the vaccine available is
                        based on the totality of scientific evidence available, showing that known and potential benefits of the vaccine outweigh the known and
                        potential risks.</p>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div style="padding-top:0px;">
                    <h2 style="font-size:14px;">Consent</h2>
                    <p class="font-weight">I have read, or had explained to me, the information sheet about the COVID-19 vaccination. I understand that if my vaccine requires two
                        doses, I will need to be administered (given) two doses of this vaccine in order for it to be effective. I have had a chance to ask questions
                        which were answered to my satisfaction (and ensured the person named above for whom I am authorized to provide surrogate consent
                        was also given a chance to ask questions). I understand the benefits and risks of the vaccination asdescribed.
                        I request that the COVID-19 vaccination be given to me (or the person named above for whom I am authorized to make this request and
                        provide surrogate consent). I understand there will be no cost to me for this vaccine. I understand that any monies or benefits for
                        administering the vaccine will be assigned and transferred to the vaccinating provider, including benefits/monies from my health plan,
                        Medicare or other third parties who are financially responsible for my medical care. I authorize release of all information needed
                        (including but not limited to medical records, copies of claims and itemized bills) to verify payment and as needed for other public health
                        purposes, including reporting to applicable vaccine registries.</p>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <table width="100%" border="0" cellspacing="0" cellpadding="0" style="text-align:center">
                    <tr>
                        <td style="width:25%; text-align:center">
                            <div>
                                <div style="height:40px; background-color:#CCC;"></div>
                                <p>Recipient/Surrogate/Guardian (Signature)
                                    recipient</p>
                            </div>
                        </td>
                        <td style="width:25%; text-align:center" valign="top">
                            <div><input type="text" name="value" />
                                <p>Date / Time</p>
                            </div>
                        </td>
                        <td style="width:25%; text-align:center" valign="top">
                            <div><input type="text" name="value" />
                                <p>Print Name</p>
                            </div>
                        </td>
                        <td style="width:25%; text-align:center" valign="top">
                            <div><input type="text" name="value" />
                                <p>Relationship to patient,<br />
                                    (if other than recipient)</p>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>
                <table width="100%" border="0" cellspacing="0" cellpadding="0" style="text-align:center">
                    <tr>
                        <td style="width:50%; text-align:center" valign="top">
                            <div><input type="text" name="value" />
                                <p>Telephonic Interpreter&rsquo;s ID #</p>
                            </div>
                        </td>
                        <td style="width:50%; text-align:center" valign="top">
                            <div><input type="text" name="value" />
                                <p>Date / Time</p>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>
                <h3 style="background-color:#999; text-align:center; color:#fff; background:#999;">or</h3>
            </td>
        </tr>
        <tr>
            <td>
                <table width="100%" border="0" cellspacing="0" cellpadding="0" style="text-align:center">
                    <tr>
                        <td style="width:33%; text-align:center">
                            <div>
                                <div style="height:40px; background-color:#CCC;"></div>
                                <p>Signature: Interpreter</p>
                            </div>
                        </td>
                        <td style="width:33%; text-align:center" valign="top">
                            <div><input type="text" name="value" />
                                <p>Date/ Time</p>
                            </div>
                        </td>
                        <td style="width:33%; text-align:center" valign="top">
                            <div><input type="text" name="value" />
                                <p>Print: Interpreter&rsquo;s Name and Relationship to Patient</p>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>
                <table width="100%" border="1" cellspacing="0" cellpadding="5" style="font-size:12px;">
                    <tr>
                        <td valign="top" colspan="4" style="background-color:#CCC; font-size:14px; text-align:center">
                            <h2 style="background-color:#CCC; font-size:14px; text-align:center"> Area Below to be Completed by Vaccinator
                            </h2>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top" colspan="4" style="background-color:#FFF; font-size:14px; text-align:center">
                            <h2 style="background-color:#FFF; font-size:14px; text-align:center">Which vaccine is the patient receiving today?
                            </h2>
                        </td>
                    </tr>
                    <tr>
                        <td width="25%" style="text-align:center">Vaccine Name</td>
                        <td width="25%" style="text-align:center">Administration</td>
                        <td width="25%" style="text-align:center">EUA Fact Sheet Date</td>
                        <td width="25%" style="text-align:center">Manufacturer &amp; Lot Number</td>
                    </tr>
                    <tr>
                        <td>Pfizer/ BioNTech</td>
                        <td valign="top">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td><input type="checkbox" class="checkbox" style="padding-right:20px;" />First Dose</td>
                                    <td><input type="checkbox" class="checkbox" style="padding-right:20px;" />Second Dose</td>
                                </tr>
                            </table>
                        </td>
                        <td valign="top"><input value="" type="text" /></td>
                        <td valign="top"><input value="" type="text" /></td>
                    </tr>
                    <tr>
                        <td>Moderna
                        </td>
                        <td valign="top">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td><input type="checkbox" class="checkbox" style="padding-right:20px;" />First Dose</td>
                                    <td><input type="checkbox" class="checkbox" style="padding-right:20px;" />Second Dose</td>
                                </tr>
                            </table>
                        </td>
                        <td valign="top"><input value="" type="text" /></td>
                        <td valign="top"><input value="" type="text" /></td>
                    </tr>
                    <tr>
                        <td>Astra-Zeneca</td>
                        <td valign="top">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td><input type="checkbox" class="checkbox" style="padding-right:20px;" />First Dose</td>
                                    <td><input type="checkbox" class="checkbox" style="padding-right:20px;" />Second Dose</td>
                                </tr>
                            </table>
                        </td>
                        <td valign="top"><input value="" type="text" /></td>
                        <td valign="top"><input value="" type="text" /></td>
                    </tr>
                    <tr>
                        <td>Janssen
                        </td>
                        <td valign="top">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td><input type="checkbox" class="checkbox" style="padding-right:20px;" />First Dose</td>
                                    <td><input type="checkbox" class="checkbox" style="padding-right:20px;" />Second Dose</td>
                                </tr>
                            </table>
                        </td>
                        <td valign="top"><input value="" type="text" /></td>
                        <td valign="top"><input value="" type="text" /></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>
                <table width="100%" border="0" cellspacing="0" cellpadding="0" style="text-align:center">
                    <tr>
                        <td style="width:20%; text-align:left" valign="top">
                            <div>
                                <p> Telephonic Interpreter&rsquo;s ID #</p>
                            </div>
                        </td>
                        <td style="width:20%; text-align:left" valign="top">
                            <p><input type="radio" name="value" class="radio" />Left Deltoid</p>
                        </td>
                        <td style="width:20%; text-align:left" valign="top">
                            <p><input type="radio" name="value" class="radio" />Right Deltoid</p>
                        </td>
                        <td style="width:20%; text-align:left" valign="top">
                            <p><input type="radio" name="value" class="radio" />Left Thigh</p>
                        </td>
                        <td style="width:20%; text-align:left" valign="top">
                            <p><input type="radio" name="value" class="radio" />Right Thigh</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>
                <table width="100%" border="0" cellspacing="0" cellpadding="0" style="text-align:center">
                    <tr>
                        <td style="width:20%; text-align:left" valign="top">
                            <div>
                                <p>Dosage</p>
                            </div>
                        </td>
                        <td style="width:20%; text-align:left" valign="top">
                            <p><input type="radio" name="value" class="radio" />0.5 m</p>
                        </td>
                        <td style="width:20%; text-align:left" valign="top">
                            <p><input type="radio" name="value" class="radio" />0.3 ml</p>
                        </td>
                        <td width="20%"></td>
                        <td width="20%"></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>
                <p><input type="checkbox" class="checkbox" style="padding-right:40px;" />I have provided the patient (and/or parent, guardian or surrogate, as applicable) with information about the vaccine and consent
                    to vaccination was obtained.</p>
            </td>
        </tr>
        <tr>
            <td style="width:33%; text-align:center">
                <div>
                    <div style="height:40px; background-color:#CCC;"></div>
                    <p>Vaccinator Signature </p>
                </div>
            </td>
        </tr>
        <tr>
            <td style="font-style:italic; font-weight:normal; font-size:12px; padding:10px;">*Use of this form is optional. In the ongoing effort to address health disparities it is essential that all demographic information is collected at
                the time of COVID-19 vaccination including sex/gender identity and race/ethnicity.</td>
        </tr>
    </table>
</body>

</html>
