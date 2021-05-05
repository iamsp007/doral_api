<?php

namespace App\Http\Controllers;

use App\Models\EmployeePhysicalExaminationReport;
use Exception;
use Illuminate\Support\Facades\Log;

class EmployeePhysicalExaminationReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = array();
        try {
            $employee = EmployeePhysicalExaminationReport::all()->toArray();
            if (!$employee) {
                throw new Exception("No employee info are registered");
            }
            $data = [
                'employee' => $employee
            ];
            return $this->generateResponse(true, 'employee listing!', $data);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            return $this->generateResponse(false, $message, $data);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($id, $input)
    {
        Log::info($id, $input);
        $status = 0;
        $data = [];
        $message = 'Something went wrong';

        try {

            $data = new EmployeePhysicalExaminationReport();
            $data->patient_id = $id;
            $data->report_details = $input;

            if ($data->save()) {
                $status = true;
                $message = 'Employee report data saved';
            }

            $data = [
                'data' => $data
            ];

            return $this->generateResponse($status, $message, $data);

        } catch (Exception $e) {

            $status = false;
            $message = $e->getMessage();
            return $this->generateResponse($status, $message, $data);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $status = 0;
        $data = [];
        $message = 'Something went wrong';

        try {

            $data = EmployeePhysicalExaminationReport::find($id);

            if (!$data) {
                throw new Exception("Employee report information is not found");
            }

            $data = [
                'data' => $data
            ];

            $status = true;
            $message = "Employee report information";
            return $this->generateResponse($status, $message, $data);

        } catch (\Exception $e) {

            $status = false;
            $message = $e->getMessage() . " " . $e->getLine();
            return $this->generateResponse($status, $message, $data);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = EmployeePhysicalExaminationReport::findOrFail($id);
        $data->delete();

        return response()->json(null, 204);
    }
}
