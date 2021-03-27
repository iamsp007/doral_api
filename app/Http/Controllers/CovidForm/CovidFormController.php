<?php

namespace App\Http\Controllers\CovidForm;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
// use mikehaertl\pdftk\Pdf;
use App\Models\CovidForm;
use Exception;
use Storage;
use PDF;
use DB;

class CovidFormController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $patientList = CovidForm::all();
        return $this->generateResponse(true, 'Covid 19 patient list', $patientList, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Log::info("REQUEST ALL");
        // Log::info($request->all());
        try {
            // dd(storage_path('app/public/covid_form/'.$request->user()->id));
            $validator = \Validator::make($request->all(),[
                'recipient_sign' => 'mimes:jpg,png|max:20000',
                'interpreter_sign' => 'mimes:jpg,png|max:20000',
                'vaccination_sign' => 'mimes:jpg,png|max:20000'
            ]);
            if ($validator->fails()){
                return $this->generateResponse(false, $validator->errors()->first(), null, 200);
            }
            Log::info(json_decode(json_encode($request->all()), true));
            $covidForm = new CovidForm();
            $covidForm->user_id = $request->user()->id;

            $covidForm->dose = $request->form->dose;
            $covidForm->patient_name = $request->form->patient_name;
            $covidForm->phone = $request->form->phone;
            $covidForm->data = $request->form->data;
            $covidForm->status = $request->form->status;

            if ($covidForm->save()){
                $uploadFolder = 'covid_form/'.$request->user()->id;
                if ($request->file('recipient_sign')) {
                    $recipientSign = $request->file('recipient_sign');
                    $recipientSignUploadedPath = $recipientSign->store($uploadFolder, 'public');
                    $uploadedRecipientSignResponse = [
                        "image_name" => basename($recipientSignUploadedPath),
                        "image_url" => Storage::disk('public')->url($recipientSignUploadedPath),
                        "mime" => $recipientSign->getClientMimeType()
                    ];

                    $covidForm->recipient_sign = $uploadedRecipientSignResponse['image_name'];
                }
                if ($request->file('interpreter_sign')) {
                    $interpreterSign = $request->file('interpreter_sign');
                    $interpreterSignUploadedPath = $interpreterSign->store($uploadFolder, 'public');
                    $uploadedInterpreterSignResponse = [
                        "image_name" => basename($interpreterSignUploadedPath),
                        "image_url" => Storage::disk('public')->url($interpreterSignUploadedPath),
                        "mime" => $interpreterSign->getClientMimeType()
                    ];

                    $covidForm->interpreter_sign = $uploadedInterpreterSignResponse['image_name'];
                }
                if ($request->file('vaccination_sign')) {
                    $vaccinationSign = $request->file('vaccination_sign');
                    $vaccinationSignUploadedPath = $vaccinationSign->store($uploadFolder, 'public');
                    $uploadedVaccinationSignResponse = [
                        "image_name" => basename($vaccinationSignUploadedPath),
                        "image_url" => Storage::disk('public')->url($vaccinationSignUploadedPath),
                        "mime" => $vaccinationSign->getClientMimeType()
                    ];

                    $covidForm->vaccination_sign = $uploadedVaccinationSignResponse['image_name'];
                }

                if ($covidForm->save()) {
                    $this->savePdf($covidForm);
                    // $pdf = new Pdf(public_path('pdf\vaccine-consent-form.pdf'), [
                    //     'command' => env('PDF_COMMAND'),
                    //     'useExec' => true,  // May help on Windows systems if execution fails
                    // ]);
                    // $result = $pdf
                    //     ->fillForm($covidForm->data)
                    //     ->saveAs(storage_path('app/public/covid_form/'.$request->user()->id.'/'.time().'.pdf'));
                }
                $status = true;
                $message = "Successfully stored covid form data.";
                return $this->generateResponse($status, $message, $covidForm, 200);
            }
            return $this->generateResponse(false, 'Something Went Wrong!', null, 200);
        } catch (\Exception $e) {
            $status = false;
            $message = $e->getMessage();
            Log::error("CATCH");
            Log::error($e->getMessage());
            return $this->generateResponse($status, $message, null);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CovidForm  $covidForm
     * @return \Illuminate\Http\Response
     */
    public function show(CovidForm $covidForm)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CovidForm  $covidForm
     * @return \Illuminate\Http\Response
     */
    public function edit(CovidForm $covidForm)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CovidForm  $covidForm
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CovidForm $covidForm)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CovidForm  $covidForm
     * @return \Illuminate\Http\Response
     */
    public function destroy(CovidForm $covidForm)
    {
        //
    }

    public function savePdf($covidForm)
    {
        try {
            $pdf = PDF::loadView('pdf.pdf', [
                'data' => $covidForm->data,
            ]);
            $pdf->setPaper('a4', 'portrait');
            $path = public_path('covid/');
            $fileName =  'covid-report-'.$covidForm->id.'.pdf';
            $pdf->save($path . '/' . $fileName);

            $covidForm->file_name = $fileName;
            $covidForm->save();

            // return $pdf->stream('covid-report-'.$covidForm->id.'.pdf')->header('Content-Type','application/pdf');
        } catch(Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
