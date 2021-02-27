<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PatientReport extends Model
{
    use HasFactory;

    public function getFileNameAttribute($fileName)
    {
        return env('APP_URL').'patient_report/'.$fileName;
    }
}
