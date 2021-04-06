<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UploadDocuments extends Model
{
    use HasFactory;

    protected $appends = ['file_url','file_type'];
    /**
     * Relation with user
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
    /**
     * Get the user's Date Of Birth.
     *
     * @return string
     */
    public function getFileUrlAttribute()
    {
        if (isset($this->file_name) && !empty($this->file_name)) {
            $directory='idProof';
            if ($this->type==="1"){
                $directory='idProof';
            }elseif ($this->type==="2"){
                $directory='degreeProof';
            }elseif ($this->type==="3"){
                $directory='medicalReport';
            }elseif ($this->type==="4"){
                $directory='insuranceReport';
            }elseif ($this->type==="5"){
                $directory='socialSecurity';
            }elseif ($this->type==="6"){
                $directory='professionalReferrance';
            }elseif ($this->type==="7"){
                $directory='mainPracticeInsurance';
            }elseif ($this->type==="8"){
                $directory='nycNurseCertificate';
            }elseif ($this->type==="9"){
                $directory='nycNurseCertificate';
            }elseif ($this->type==="10"){
                $directory='CPR';
            }elseif ($this->type==="11"){
                $directory='physical';
            }elseif ($this->type==="12"){
                $directory='forensicDrugScreen';
            }elseif ($this->type==="13"){
                $directory='RubellaImmunization';
            }elseif ($this->type==="14"){
                $directory='malpracticeInsurance';
            }elseif ($this->type==="15"){
                $directory='flu';
            }elseif ($this->type==="16"){
                $directory='annualPPD';
            }elseif ($this->type==="17"){
                $directory='chestXRay';
            }elseif ($this->type==="18"){
                $directory='annualTubeScreening';
            }

            return url('storage/documents/'.$this->user_id.'/'.$directory.'/'.$this->file_name);
        } else {
            return null;
        }
    }
    /**
     * Get the user's Date Of Birth.
     *
     * @return string
     */
    public function getFileTypeAttribute()
    {
        if (isset($this->type) && !empty($this->type)) {
            $directory='idProof';
            if ($this->type==="1"){
                $directory='idProof';
            }elseif ($this->type==="2"){
                $directory='degreeProof';
            }elseif ($this->type==="3"){
                $directory='medicalReport';
            }elseif ($this->type==="4"){
                $directory='insuranceReport';
            }elseif ($this->type==="5"){
                $directory='socialSecurity';
            }elseif ($this->type==="6"){
                $directory='professionalReferrance';
            }elseif ($this->type==="7"){
                $directory='mainPracticeInsurance';
            }elseif ($this->type==="8"){
                $directory='nycNurseCertificate';
            }elseif ($this->type==="9"){
                $directory='nycNurseCertificate';
            }elseif ($this->type==="10"){
                $directory='CPR';
            }elseif ($this->type==="11"){
                $directory='physical';
            }elseif ($this->type==="12"){
                $directory='forensicDrugScreen';
            }elseif ($this->type==="13"){
                $directory='RubellaImmunization';
            }elseif ($this->type==="14"){
                $directory='malpracticeInsurance';
            }elseif ($this->type==="15"){
                $directory='flu';
            }elseif ($this->type==="16"){
                $directory='annualPPD';
            }elseif ($this->type==="17"){
                $directory='chestXRay';
            }elseif ($this->type==="18"){
                $directory='annualTubeScreening';
            }
            return $directory;
        } else {
            return '';
        }
    }
}
