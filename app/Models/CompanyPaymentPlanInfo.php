<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyPaymentPlanInfo extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table='company_payment_plan_info';
    protected $fillable = [
        'service_id','service_payment_plan_id', 'service_payment_plan_details_id', 'company_id'
    ];
}
