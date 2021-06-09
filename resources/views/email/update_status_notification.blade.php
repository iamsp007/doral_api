@extends('emails.layouts.app')
@section('content')
	<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#ffffff">
    <tbody>
      <tr>
        <td align="center" valign="top" bgcolor="#ffffff">
          <table width="600" cellpadding="0" cellspacing="0" border="0" align="center" class="hc_main_table" style="table-layout:fixed;">
            <tbody>
              <tr>
                <td style="line-height:0px; font-size:0px;" width="600" class="hc_hide" bgcolor="#ffffff "><img src="./UPDATE_files/spacer.gif" height="1" width="600" style="max-height:1px; min-height:1px; display:block; width:600px; min-width:600px;" border="0" alt=""></td>
              </tr>
            </tbody>
          </table>
        </td>
      </tr>
      <tr>
        <td align="center" valign="top" bgcolor="#ffffff">
          <table width="600" cellpadding="0" cellspacing="0" border="0" align="center" class="hc_main_table" style="table-layout:fixed;">
            <tbody>
              <tr>
                <td height="10" class="hc_height" style="line-height:0px; font-size:0px;">&nbsp;</td>
              </tr>
              <tr>
                <td align="center"><a href="#" target="_blank" style="text-decoration:none;"><img src="{{ asset('assets/img/banner.png') }}" class="hc_banner" width="369" height="246" style="display:block;font-family: Arial, sans-serif; font-size:15px; line-height:18px; color:#30373b;  font-weight:bold;" border="0" alt="LoGo Here"></a></td>
              </tr>
              <tr>
                <td valign="top" class="hc_aside">
                  <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td align="center" class="hc_txt_20" style="font-family:Montserrat, Arial, sans-serif; font-size:24px; font-weight:700; line-height:26px; color:#24272A;">Hi {{ $details['first_name'] }} {{ $details['last_name'] }},</td>
                      </tr>
                      <tr>
                        <td height="20" style="font-size:1px; line-height:1px;">&nbsp;</td>
                      </tr>
                      <tr>
                        <td align="center" class="hc_txt_14" style="font-family:Montserrat, Arial, sans-serif; font-size:16px; line-height:22px; font-weight: 400; color:#707070;">{{ $details['message']}}</td>
                      </tr>
                      <tr>
                        <td height="30" class="hc_height">&nbsp;</td>
                      </tr>
                      <tr>
                        <td align="center" class="hc_txt_14" style="font-family:Montserrat, Arial, sans-serif; font-size:16px; line-height:22px; font-weight: 400; color:#707070;">Thank you for Connecting with DORAL HEALTH CONNECT.</td>
                      </tr>
                      <tr>
                        <td height="30" class="hc_height">&nbsp;</td>
                      </tr>
                      <tr>
                        <td align="" class="hc_txt_14" style="font-family:Montserrat, Arial, sans-serif; font-size:16px; line-height:22px; font-weight: 400; color:#707070;">Thanks,</td>
                      </tr>
                      <tr>
                        <td align="" class="hc_txt_14" style="font-family:Montserrat, Arial, sans-serif; font-size:16px; line-height:22px; font-weight: 400; color:#707070;">The Doral Team.</td>
                      </tr>
                      <tr>
                        <td height="30" class="hc_height">&nbsp;</td>
                      </tr>
                    </tbody>
                  </table>
                </td>
              </tr>
            </tbody>
          </table>
        </td>
      </tr>
    </tbody>
  </table>
<div style="display:none; white-space:nowrap; font:20px courier; color:#ffffff; background-color:#ffffff;">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</div>
@endsection
