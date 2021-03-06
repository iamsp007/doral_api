<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaregiverInfo extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'company_id',
        'service_id',
        'caregiver_id',
        'intials',
        'caregiver_gender_id',
        'caregiver_code',
        'alternate_caregiver_code',
        'time_attendance_pin',
        'mobile',
        'ethnicity',
        'country_of_birth',
        'employee_type',
        'marital_status',
        'dependents',
        'status',
        'employee_id',
        'application_date',
        'hire_date',
        'rehire_date',
        'first_work_date',
        'last_work_date',
        'registry_number',
        'registry_checked_date',
        'referral_source',
        'referral_person',
        'notification_preferences',
        'caregiver_offices',
        'inactive_reason_detail',
        'professional_licensenumber',
        'npi_number',
        'signed_payroll_agreement_date',
    ];
}
