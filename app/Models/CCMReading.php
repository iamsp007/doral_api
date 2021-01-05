<?php

namespace App\Models;

use App\Events\SendingSMS;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CCMReading extends Model
{
    use HasFactory;


    protected $fillable=['user_id','reading_type','reading_value','reading_level'];
//    /**
//     * Get the user's Date Of Birth.
//     *
//     * @return string
//     */
//    public function setReadingTypeAttribute($value)
//    {
////dd($this->reading_value);
////        $reading_level = 1;
////        $reading_type = 'Blood Pressure';
////        $user = User::find($this->user_id);
////        $level='slightly';
////        if ($value==="1"){
////            $reading_type = 'Blood Pressure';
////            $val = explode('/',$this->reading_value);
////            if (count($val)>1){
////                if ($val[0]>=130 && $val[1]<=139){
////                    $reading_level=2;
////                }else if ($val[0]>=140){
////                    $reading_level=3;
////                }
////                $reading_type = 'Blood Pressure';
////            }
////            $message = 'Doral Health Connect | Your patient '.$user->first_name.' sugar is slightly '.$value.' regular. http://app.doralhealthconnect.com/caregiver/'.$value;
////        }elseif ($value==="2"){
////            $reading_type = 'Blood Sugar';
////            if ($this->reading_value > 250){
////                $reading_level=3;
////            }elseif ($this->reading_value < 60){
////                $reading_level=3;
////            }
////            if($reading_level == 3) {
////                $le = 'lower';
////            }else {
////                $le = 'higher';
////            }
////            $message = 'Doral Health Connect | Your patient '.$user->first_name.' sugar is slightly '.$le.' regular. http://app.doralhealthconnect.com/caregiver/'.$value;
////        }elseif ($value==="3"){
////            $reading_type = 'Pulse';
////            if ($this->reading_value > 110){
////                $reading_level=3;
////            }
////            $message = 'Doral Health Connect | Your patient '.$user->first_name.' sugar is slightly '.$value.' regular. http://app.doralhealthconnect.com/caregiver/'.$value;
////        }
////
////
////        // send sms
////        event(new SendingSMS($this->user_id,$message));
////        // Save CCM Reading
////        $this->attributes['reading_level']=$reading_level;
////        $this->attributes['reading_type']=$reading_type;
//
//
//    }
}
