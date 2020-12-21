<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\VirtualRoom;

#Import necessary classes from the Vonage API (AKA OpenTok)

use Illuminate\Support\Facades\Auth;
use OpenTok\OpenTok;
use OpenTok\MediaMode;
use OpenTok\Role;


class SessionsController extends Controller
{
    /** Creates a new virtual class for teachers
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */

    public function createRoom(Request $request)
    {
        try {
            // Get the currently signed-in user
            $user = User::find(Auth::user()->id);
            // Instantiate a new OpenTok object with our api key & secret
            $opentok = new OpenTok(env('VONAGE_API_KEY'), env('VONAGE_API_SECRET'));

            // Creates a new session (Stored in the Vonage API cloud)
            $session = $opentok->createSession(array('mediaMode' => MediaMode::ROUTED));

            // Create a new virtual class that would be stored in db
            $class = new VirtualRoom();
            // Generate a name based on the name the teacher entered
            $class->name = 'Dr. '.$user->first_name . " " . $user->last_name . " Room - ".$user->id;
            // Store the unique ID of the session
            $class->user_id = $user->id;
            $class->session_id = $session->getSessionId();
            // Save this class as a relationship to the teacher
            $user->myRoom()->save($class);
            // Send the teacher to the classroom where real-time video goes on
            return $this->generateResponse(true,'Class Room Generate successfully!',['id' => $class->id],200);
        }catch (\Exception $exception){
            return $this->generateResponse(false,$exception->getMessage(),null,200);
        }
    }
}
