<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PatientController;
use Exception;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    protected $employeeContoller, $patientController;
    public function __construct(EmployeeController $employeeContoller, PatientController $patientController)
    {
        $this->employeeContoller = $employeeContoller;
        $this->patientController = $patientController;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $users = User::all();
            if (!$users) {
            }
            return response()->json(['status' => $status, 'data' => $users]);
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
    public function store(Request $request)
    {
        //Post data
        $status = 1;
        $request = json_decode($request->getContent(), true);
        $user = $request['data'];
        $data = array(
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'address1' => $user['address1'],
            'address2' => $user['address2'],
            'zip' => $user['zip'],
            'phone' => $user['phone'],
            'email' => $user['email'],
            'dob' => $user['dob'],
            'type' => $user['type'],
            'status' => 'inactive',
            'password' => Hash::make('test123')
        );
        try {
            \DB::beginTransaction();
            $id = User::insert($data);
            if ($id) {
                $request['data']['user_id'] = $id;
                if ($user['type'] == 'employee') {
                    $result = $this->employeeContoller->store($request);
                } else if ($user['type'] == 'patient') {
                    $result = $this->patientController->store($request);
                }
                // Check the condition if error into database
                if (!$result) {
                    throw new \ErrorException('Error in-Insert ' . $user['type']);
                }
                \DB::commit();
                return response()->json(['status' => $status, 'data' => $result]);
            } else {
                throw new \ErrorException('Error found');
            }
        } catch (\Exception $e) {
            $status = 0;
            \DB::rollback();
            return response()->json(['status' => $status, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return User::find($user);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $User = User::findOrFail($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $User = User::findOrFail($user);
        $User->update($request->all());

        return response()->json($User, 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $User = User::findOrFail($user);
        $User->delete();

        return response()->json(null, 204);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $status = 0;
        try {
            $login = json_decode($request->getContent(), true);
            $data = $login['data'];
            $password = $data['password'];
            $user = User::login($data);
            // Check user exist into database or not   
            if (!$user) {
                return response()->json(['status' => $status, 'message' => 'Login Fail, please check email id']);
            }
            // Check user password
            if (!Hash::check($password, $user->password)) {
                return response()->json(['status' => $status, 'message' => 'Login Fail, pls check password']);
            }
            return response()->json(['status' => 1, 'data' => $user]);
        } catch (\Exception $e) {
            return response()->json(['status' => $status, 'message' => $e->getMessage()]);
        }
    }
}
