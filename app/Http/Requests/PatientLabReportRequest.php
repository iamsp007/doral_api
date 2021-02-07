<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PatientLabReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'lab_report_type_id' => 'required',
            'lab_due_date' => 'required',
            'result' => 'required'
        ];
    }
}
