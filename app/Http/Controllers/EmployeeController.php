<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\employee as ModelsEmployee;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $result = Employee::getAll();
        } catch (\Exception $e) {
        }
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
    public function store($request)
    {
        //$request = json_decode($request->getContent(), true);
        $data = Employee::insert($request);
        return $data;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function show(Employee $employee)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function edit(Employee $employee)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Employee $employee)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function destroy(Employee $employee)
    {
        //
    }
    /**
     * Search employee by id
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getAppoinment(Request $request)
    {
        $status = false;
        $data = [];
        $message = "";
        try {
            $res = [];
            $request = $request->all();
            if (!$request['type'] && !$request['value']) {
                throw new Exception("Invalide parameter");
            }
            $type = $request['type'];
            switch ($type) {
                case 'employee_id':
                    $res = Employee::getAppoinmentByEmployeeId($request['value']);
                    break;

                default:
                    throw new Exception("Invalide parameter type");
                    break;
            }
            if (!$res['status']) {
                throw new Exception($res['message']);
            }
            $status = true;
            $message = "Employee Appointments";
            $data = [
                'data' => $res['data']
            ];
            return $this->generateResponse($status, $message, $data);
        } catch (\Exception $e) {
            $status = false;
            $message = $e->getFile() . $e->getMessage() . $e->getLine();
            return $this->generateResponse($status, $message, $data);
        }
    }
}
