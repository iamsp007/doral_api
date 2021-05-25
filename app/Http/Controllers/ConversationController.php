<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\User;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ConversationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $response = User::whereHas('conversation','roles', function($q) {
            $q->where('name','=', 'patient');
        })->get();
        if (count($response)>0){
            return $this->generateResponse(true,'Patient List',$response,200);
        }
        return $this->generateResponse(false,'No Patient Exists',null,200);
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
      
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Conversation  $conversation
     * @return \Illuminate\Http\Response
     */
    public function show(Conversation $conversation)
    {
        if ($conversation){
            return $this->generateResponse(true,'Conversation List',$conversation,200);
        }
        return $this->generateResponse(false,'No Conversation Exists',null,200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Conversation  $conversation
     * @return \Illuminate\Http\Response
     */
    public function edit(Conversation $conversation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Conversation  $conversation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Conversation $conversation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Conversation  $conversation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Conversation $conversation)
    {
        if ($conversation) {
            $conversation->delete();

            return $this->generateResponse(true, 'Conversation remove successfully.', null, 200);
        }

        return $this->generateResponse(false, 'Conversation not found.', null);
    }
}
