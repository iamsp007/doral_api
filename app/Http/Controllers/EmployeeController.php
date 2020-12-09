<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\employee as ModelsEmployee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
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
            
            $employee = Employee::all()->toArray();
            if (!$employee) {
                throw new Exception("No employee are registered");
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
        $status = 0;
        $data = array();
        $message = 'Something wrong';
        try {
            //Post data
            $employee = $request;

            // User Add
            $user = new User;
            $user->first_name = $employee['first_name'];
            $user->last_name = $employee['last_name'];
            $user->email = $employee['email'];
            $user->password = Hash::make('test123');
            $user->dob = $employee['dob'];
            $user->phone = $employee['phone'];
            $user->type = 'admin';
            $user->save();

            $userId = $user->id;

            $data = array(
                'first_name' => $employee['first_name'],
                'last_name' => $employee['last_name'],
                'dob' => $employee['dob'],
                'gender' => $employee['gender'],
                'address1' => $employee['address1'],
                'city' => $employee['city'],
                'state' => $employee['state'],
                'zip' => $employee['zip'],
                'country' => $employee['country'],
                'home_phone' => $employee['home_phone'],
                'phone' => $employee['phone'],
                'alternate_phone' => $employee['alternate_phone'],
                'email' => $employee['email'],
                'marital_status' => $employee['marital_status'],
                'blood_group' => $employee['blood_group'],
                'user_id' => $userId
            );
            $record = Employee::create($data);

            if ($record->id) {
                $status = true;
                $message = 'Employee store properly';
            }
            $data = [
                'Employee_id' => $record->id
            ];
            return $this->generateResponse($status, $message, $data);
        } catch (Exception $e) {
            $status = false;
            $message = $e->getMessage();
            return $this->generateResponse($status, $message, $data);
        }  
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function work(Request $request)
    {
        $status = 0;
        $data = array();
        $message = 'Something wrong';
        try {
            $request = json_decode($request->getContent(), true);
            $data = array(
                'role_id' => $request['role_id'],
                'experience' => $request['experience'],
                'current_job_location' => $request['current_job_location'],
                'employeement_type' => $request['employeement_type'],
                'language_known' => $request['language_known']
            );

            $record = Employee::where('id', $request['employee_id'])
                            ->update($data);
            if ($record) {
                $status = true;
                $message = 'Employee update properly';
            }
            
            return $this->generateResponse($status, $message, $record);
        } catch (Exception $e) {
            $status = false;
            $message = $e->getMessage();
            return $this->generateResponse($status, $message, $data);
        }  
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function show(Employee $employee)
    {
        $status = 0;
        $data = [];
        $message = 'Something wrong';
        try {
            $Employee = Employee::find($employee);
            if (!$Employee) {
                throw new Exception("Employee information is not found");
            }
            $data = [
                'employee' => $Employee
            ];
            $status = true;
            $message = "Employee information";
            return $this->generateResponse($status, $message, $data);
        } catch (\Exception $e) {
            $status = false;
            $message = $e->getMessage() . " " . $e->getLine();
            return $this->generateResponse($status, $message, $data);
        }
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
        $employee = Employee::findOrFail($employee);
        $employee->delete();

        return response()->json(null, 204);
    }
}
