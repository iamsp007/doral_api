<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    use HasFactory;

    public function dose(){
        return $this->hasOne(DoseMaster::class,'id','does');
    }

    public function from(){
        return $this->hasOne(MedicineFromMaster::class,'id','from');
    }

    public function route(){
        return $this->hasOne(MedicineMaster::class,'id','route');
    }

    public function frequency(){
        return $this->hasOne(FrequencyMaster::class,'id','frequency');
    }

    public function preferredPharmacy(){
        return $this->hasOne(PreferredPharmacyMaster::class,'id','preferred_pharmacy');
    }
}
