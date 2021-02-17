<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FailRecodeImport extends Model
{
    use HasFactory;
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table='fail_import_recode11';
    protected $fillable = [
        'row', 'attribute','errors','values','file_name'
    ];
}
