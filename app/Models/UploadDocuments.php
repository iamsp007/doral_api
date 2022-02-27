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
            $directory = 'idProof';
            if ($this->type === "1") {
                $directory = 'idProof';
            } elseif ($this->type === "2") {
                $directory = 'degreeProof';
            } elseif ($this->type === "3") {
                $directory = 'medicalReport';
            } elseif ($this->type === "4") {
                $directory = 'insuranceReport';
            } elseif ($this->type === "5") {
                $directory = 'socialSecurity';
            } elseif ($this->type === "6") {
                $directory = 'professionalReferrance';
            } elseif ($this->type === "7") {
                $directory = 'mainPracticeInsurance';
            } elseif ($this->type === "8") {
                $directory = 'nycNurseCertificate';
            } elseif ($this->type === "9") {
                $directory = 'CPR';
            } elseif ($this->type === "10") {
                $directory = 'physical';
            } elseif ($this->type === "11") {
                $directory = 'forensicDrugScreen';
            } elseif ($this->type === "12") {
                $directory = 'RubellaImmunization';
            } elseif ($this->type === "13") {
                $directory = 'RubellaMeasiesImmunization';
            } elseif ($this->type === "14") {
                $directory = 'malpracticeInsurance';
            } elseif ($this->type === "15") {
                $directory = 'flu';
            } elseif ($this->type === "16") {
                $directory = 'annualPPD';
            } elseif ($this->type === "17") {
                $directory = 'chestXRay';
            } elseif ($this->type === "18") {
                $directory = 'annualTubeScreening';
            } elseif ($this->type === "19") {
                $directory = 'w4document';
            } elseif ($this->type === "20") {
                $directory = 'idProofBack';
            } elseif ($this->type === "21") {
                $directory = 'socialSecurityBack';
            } elseif ($this->type === "22") {
                $directory = 'pdfDoc';
            }  elseif ($this->type === "25") {
                $directory = 'pictureIdentification';
            }  elseif ($this->type === "26") {
                $directory = 'currentCV';
            } elseif ($this->type === "27") {
                $directory = 'ProfessionalLicense';
            } elseif ($this->type === "28") {
                $directory = 'StateRegistrationCertificate';
            } elseif ($this->type === "29") {
                $directory = 'DEALicense';
            } elseif ($this->type === "30") {
                $directory = 'ControlledSubstanceStateLicense';
            } elseif ($this->type === "31") {
                $directory = 'MalpracticeCertificateOfInsurance';
            } elseif ($this->type === "32") {
                $directory = 'ExplanationOfAllMalpractice';
            } elseif ($this->type === "33") {
                $directory = 'MedicalSchoolDiploma';
            } elseif ($this->type === "34") {
                $directory = 'ResidencyCertificate';
            } elseif ($this->type === "35") {
                $directory = 'FellowshipCertificate';
            } elseif ($this->type === "36") {
                $directory = 'InternshipCertificate';
            } elseif ($this->type === "37") {
                $directory = 'ECFMGCertificate';
            } elseif ($this->type === "38") {
                $directory = 'BoardCertificate(c)';
            } elseif ($this->type === "39") {
                $directory = 'HospitalAffiliationLetter';
            } elseif ($this->type === "40") {
                $directory = 'SanctionsQueries';
            } elseif ($this->type === "41") {
                $directory = 'MedicareWelcomeLetter';
            } elseif ($this->type === "42") {
                $directory = 'SignedW9';
            } elseif ($this->type === "43") {
                $directory = 'SignedESignatureForm';
            } elseif ($this->type === "44") {
                $directory = 'CovidCertificate';
            } elseif ($this->type === "45") {
                $directory = 'CPRACLS';
            } elseif ($this->type === "46") {
                $directory = 'passport';
            } elseif ($this->type === "47") {
                $directory = 'greencard';
            } elseif ($this->type === "48") {
                $directory = 'workpermit';
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
            $directory = 'idProof';
            if ($this->type === "1") {
                $directory = 'idProof';
            } elseif ($this->type === "2") {
                $directory = 'degreeProof';
            } elseif ($this->type === "3") {
                $directory = 'medicalReport';
            } elseif ($this->type === "4") {
                $directory = 'insuranceReport';
            } elseif ($this->type === "5") {
                $directory = 'socialSecurity';
            } elseif ($this->type === "6") {
                $directory = 'professionalReferrance';
            } elseif ($this->type === "7") {
                $directory = 'mainPracticeInsurance';
            } elseif ($this->type === "8") {
                $directory = 'nycNurseCertificate';
            } elseif ($this->type === "9") {
                $directory = 'CPR';
            } elseif ($this->type === "10") {
                $directory = 'physical';
            } elseif ($this->type === "11") {
                $directory = 'forensicDrugScreen';
            } elseif ($this->type === "12") {
                $directory = 'RubellaImmunization';
            } elseif ($this->type === "13") {
                $directory = 'RubellaMeasiesImmunization';
            } elseif ($this->type === "14") {
                $directory = 'malpracticeInsurance';
            } elseif ($this->type === "15") {
                $directory = 'flu';
            } elseif ($this->type === "16") {
                $directory = 'annualPPD';
            } elseif ($this->type === "17") {
                $directory = 'chestXRay';
            } elseif ($this->type === "18") {
                $directory = 'annualTubeScreening';
            } elseif ($this->type === "19") {
                $directory = 'w4document';
            } elseif ($this->type === "20") {
                $directory = 'idProofBack';
            } elseif ($this->type === "21") {
                $directory = 'socialSecurityBack';
            } elseif ($this->type === "22") {
                $directory = 'pdfDoc';
            }  elseif ($this->type === "25") {
                $directory = 'pictureIdentification';
            }  elseif ($this->type === "26") {
                $directory = 'currentCV';
            } elseif ($this->type === "27") {
                $directory = 'ProfessionalLicense';
            } elseif ($this->type === "28") {
                $directory = 'StateRegistrationCertificate';
            } elseif ($this->type === "29") {
                $directory = 'DEALicense';
            } elseif ($this->type === "30") {
                $directory = 'ControlledSubstanceStateLicense';
            } elseif ($this->type === "31") {
                $directory = 'MalpracticeCertificateOfInsurance';
            } elseif ($this->type === "32") {
                $directory = 'ExplanationOfAllMalpractice';
            } elseif ($this->type === "33") {
                $directory = 'MedicalSchoolDiploma';
            } elseif ($this->type === "34") {
                $directory = 'ResidencyCertificate';
            } elseif ($this->type === "35") {
                $directory = 'FellowshipCertificate';
            } elseif ($this->type === "36") {
                $directory = 'InternshipCertificate';
            } elseif ($this->type === "37") {
                $directory = 'ECFMGCertificate';
            } elseif ($this->type === "38") {
                $directory = 'BoardCertificate(c)';
            } elseif ($this->type === "39") {
                $directory = 'HospitalAffiliationLetter';
            } elseif ($this->type === "40") {
                $directory = 'SanctionsQueries';
            } elseif ($this->type === "41") {
                $directory = 'MedicareWelcomeLetter';
            } elseif ($this->type === "42") {
                $directory = 'SignedW9';
            } elseif ($this->type === "43") {
                $directory = 'SignedESignatureForm';
            } elseif ($this->type === "44") {
                $directory = 'CovidCertificate';
            } elseif ($this->type === "45") {
                $directory = 'CPRACLS';
            } elseif ($this->type === "46") {
                $directory = 'passport';
            } elseif ($this->type === "47") {
                $directory = 'greencard';
            } elseif ($this->type === "48") {
                $directory = 'workpermit';
            } 

            return $directory;
        } else {
            return '';
        }
    }
    
}
