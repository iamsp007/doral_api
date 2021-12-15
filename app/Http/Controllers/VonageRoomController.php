<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\VonageRoom;
use Illuminate\Http\Request;
use OpenTok\MediaMode;
use OpenTok\OpenTok;

class VonageRoomController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        try {
            $user = User::find($request->user_id);
            if ($user){
                // Instantiate a new OpenTok object with our api key & secret
                $opentok = new OpenTok(env('VONAGE_API_KEY'), env('VONAGE_API_SECRET'));

                // Creates a new session (Stored in the Vonage API cloud)
                $session = $opentok->createSession(array('mediaMode' => MediaMode::RELAYED));

                // Create a new virtual class that would be stored in db
                $class = new VonageRoom();
                // Generate a name based on the name the teacher entered
                $class->name = 'Dr. '.$user->first_name . " " . $user->last_name . " Room - ".$user->id;
                // Store the unique ID of the session
                $class->user_id = $user->id;
                $class->session_id = $session->getSessionId();
                // Save this class as a relationship to the teacher
                $user->myRoom()->save($class);
            }
        }catch (\Exception $exception){

        }
    }
}
