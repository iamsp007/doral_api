<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiesesMaster extends Model
{
    use HasFactory;

    public function getImgAttribute($value)
    {
        if ($value) {
            return asset('images/' . $value);
        } else {
            return asset('images/no-image.png');
        }
    }

}
