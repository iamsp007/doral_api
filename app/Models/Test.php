<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    use HasFactory;
    
    protected $table='sub_tests';

    public function subTestName()
    {
        return $this->hasOne(Category::class, 'id', 'category_id');
    }
}
