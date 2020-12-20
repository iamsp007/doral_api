<?php

namespace App\Http\Controllers;

use App\Models\Services;
use App\Models\ServiceMaster;
use Exception;
use Illuminate\Http\Request;

class ServicesController extends Controller
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
            $services = Services::all();
            if (!$services) {
                throw new Exception("No Services are found into database");
            }
            $data = [
                'services' => $services
            ];
            $status = true;
            $message = "services List";
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
            $request = json_decode($request->getContent(), true);
            $service = Services::insert($request);
            $data = [
                'service' => $service
            ];
            $status = true;
            $message = "service Inserted successfully";
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
     * @param  \App\Models\Services  $services
     * @return \Illuminate\Http\Response
     */
    public function show(Services $services)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Services  $services
     * @return \Illuminate\Http\Response
     */
    public function edit(Services $services)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Services  $services
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $services)
    {
        $status = 0;
        $data = [];
        $message = 'Something wrong';
        try {
            $request = json_decode($request->getContent(), true);
            $services = Services::setServices($services, $request);
            if (!$services) {
                throw new Exception("Error in update the services");
            }
            $data = [
                'services' => $services
            ];
            $status = true;
            $message = "Services updated Succesfully";
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
     * @param  \App\Models\Services  $services
     * @return \Illuminate\Http\Response
     */
    public function destroy(Services $services)
    {
        //
    }

    public function serviceMaster()
    {
        $status = 0;
        $data = [];
        $message = 'Something wrong';
        try {
            $services = ServiceMaster::all();
            if (!$services) {
                throw new Exception("No Services are found into database");
            }
            $data = [
                'services' => $services
            ];
            $status = true;
            $message = "Services List";
            return response()->json([$status, $message, $data]);
        } catch (\Exception $e) {
            $status = false;
            $message = $e->getMessage() . " " . $e->getLine();
            return $this->generateResponse($status, $message, $data);
        }
    }
}
