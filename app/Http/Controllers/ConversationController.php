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
        $response = User::whereHas('conversation',)->whereHas('roles', function($q) {
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
        $input = $request->all();
      
        $rules = [
            'senderID' => 'required',
            'receiverId' => 'required',
            'message' => 'required',
            'senderType' => 'required',
        ];

        $message = [
            'senderID.required' => 'Please enter sender id.',
            'receiverId.required' => 'Please enter receiver id.',
            'message.required' => 'Please enter message.',
            'senderType.required' => 'Please enter sender type.',
        ];

        $validator = Validator::make($input, $rules, $message);

        if($validator->fails()){
            return $this->generateResponse(false, $validator->errors()->first(), null, 200);
        } 
        try {
            $conversation = new Conversation();
            $message = 'Conversation added successfully!';

            $input['chat'] = $input['message'];

            $input['sender_id'] = $input['senderID'];
            $input['receiver_id'] = $input['receiverId'];
            $input['parentID'] = $input['parentID'];

            if ($input['senderType'] === 'patient') {
                $input['user_id'] = $input['senderID'];
            } elseif ($input['senderType'] === 'clinician') {
                $input['user_id'] = $input['receiverId'];
            }

            if($conversation->fill($input)->save()) {
                return $this->generateResponse(true, $message, $conversation, 200);
            } else {
                return $this->generateResponse(false, 'Something Went Wrong!', null, 200);
            }
        } catch (\Exception $e) {
            $status = false;
            $message = $e->getMessage();
            return $this->generateResponse($status, $message, null);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Conversation  $conversation
     * @return \Illuminate\Http\Response
     */
    public function getConversation(Request $request)
    {
        $input = $request->all();
      
        $conversation = Conversation::with('user')->where('parentID',$input['parentID'])->get();
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
    public function destroy($id)
    {
        $conversation = Conversation::find($id);
        if ($conversation) {
            $conversation->delete();

            return $this->generateResponse(true, 'Conversation remove successfully.', null, 200);
        }

        return $this->generateResponse(false, 'Conversation not found.', null);
    }
}
