<?php

namespace App\Http\Controllers;

use App\Models\Request as Support;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;

class RequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $status = 0;
        $data = [];
        $message = 'Something wrong';
        try {
            $support = Support::all();
            if (!$support) {
                throw new Exception("No support data are found into database");
            }
            $data = [
                'support' => $support
            ];
            $status = true;
            $message = "Support List";
            return response()->json([$status, $message, $data]);
        } catch (\Exception $e) {
            $status = false;
            $message = $e->getMessage() . " " . $e->getLine();
            return $this->generateResponse($status, $message, $data);
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
        try {
            $check = Support::where([
                'role_id' => $request->role_id,
                'user_id' => $request->user_id,
                'support_type' => $request->support_type
            ])->first();
            if (isset($check) && $check->count()) {
                $status = false;
                $message = "Request already exist";
                return $this->generateResponse($status, $message);
            }
            $user = User::findOrFail($request->user_id);
            if (isset($user->roles->first()->id) && $user->roles->first()->id != $request->role_id) {
                $status = false;
                $message = "The user role mismatched";
                return $this->generateResponse($status, $message);
            }
            $support = Support::insert($request->all());
            $data = [
                'support' => $support
            ];
            $status = true;
            $message = "Support Inserted successfully";
            return $this->generateResponse($status, $message, $data);
        } catch (\Exception $e) {
            $status = false;
            $message = $e->getMessage() . " " . $e->getLine();
            return $this->generateResponse($status, $message, $data);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Support  $support
     * @return \Illuminate\Http\Response
     */
    public function show(Support $support)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Support  $support
     * @return \Illuminate\Http\Response
     */
    public function edit(Support $support)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Support  $support
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $support)
    {
        $status = 0;
        $data = [];
        $message = 'Something wrong';
        try {
            $support = Support::setRequest($support, $request->all());
            if (!$support) {
                throw new Exception("Error in update the services");
            }
            $data = [
                'support' => $support
            ];
            $status = true;
            $message = "Support updated Succesfully";
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
     * @param  \App\Models\Support  $support
     * @return \Illuminate\Http\Response
     */
    public function destroy(Support $support)
    {
        //
    }
}
