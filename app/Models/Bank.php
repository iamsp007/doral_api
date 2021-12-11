<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    use HasFactory;

    /**
    * Relation with referances
    */
   public function bankRouting()
   {
       return $this->hasMany('App\Models\BankRouting', 'bank_id', 'id');
   }
}
