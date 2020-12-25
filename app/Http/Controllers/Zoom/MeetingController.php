<?php

namespace App\Http\Controllers\Zoom;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\VirtualRoom;
use Illuminate\Http\Request;
use App\Traits\ZoomJWT;
use Illuminate\Support\Facades\Validator;

class MeetingController extends Controller
{
    use ZoomJWT;

    const MEETING_TYPE_INSTANT = 1;
    const MEETING_TYPE_SCHEDULE = 2;
    const MEETING_TYPE_RECURRING = 3;
    const MEETING_TYPE_FIXED_RECURRING_FIXED = 8;

    public function list(Request $request) {
        $path = 'users/me/meetings';
        $response = $this->zoomGet($path);

        $data = json_decode($response->body(), true);
        $data['meetings'] = array_map(function (&$m) {
            $m['start_at'] = $this->toUnixTimeStamp($m['start_time'], $m['timezone']);
            return $m;
        }, $data['meetings']);

        return [
            'success' => $response->ok(),
            'data' => $data,
        ];
    }
    public function create(Request $request) {
        $validator = Validator::make($request->all(), [
            'appointment_id' => 'required',
            'topic' => 'required|string',
            'start_time' => 'required|date',
            'agenda' => 'string|nullable',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'data' => $validator->errors(),
            ];
        }
        $data = $validator->validated();

        $path = 'users/me/meetings';
        $response = $this->zoomPost($path, [
            'topic' => $data['topic'],
            'type' => self::MEETING_TYPE_SCHEDULE,
            'start_time' => $this->toZoomTimeFormat($data['start_time']),
            'duration' => 30,
            'agenda' => $data['agenda'],
            'settings' => [
                'host_video' => false,
                'participant_video' => false,
                'waiting_room' => true,
            ]
        ]);
        $resp = json_decode($response);
        $resp->start_time = $request->start_time;
        return $this->store($resp,$request->appointment_id);

    }
    public function get(Request $request, string $id) {
        $path = 'meetings/' . $id;
        $response = $this->zoomGet($path);

        $data = json_decode($response->body(), true);
        if ($response->ok()) {
            $data['start_at'] = $this->toUnixTimeStamp($data['start_time'], $data['timezone']);
        }

        return [
            'success' => $response->ok(),
            'data' => $data,
        ];
    }
    public function update(Request $request, string $id) {
        $validator = Validator::make($request->all(), [
            'appointment_id' => 'required',
            'topic' => 'required|string',
            'start_time' => 'required|date',
            'agenda' => 'string|nullable',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'data' => $validator->errors(),
            ];
        }
        $data = $validator->validated();

        $virtualRoom = VirtualRoom::where(['appointment_id'=>$id])->first();
        if ($virtualRoom){
            $path = 'meetings/' . $virtualRoom->meeting_id;
            $response = $this->zoomPatch($path, [
                'topic' => $data['topic'],
                'type' => self::MEETING_TYPE_SCHEDULE,
                'start_time' => (new \DateTime($data['start_time']))->format('Y-m-d\TH:i:s'),
                'duration' => 30,
                'agenda' => $data['agenda'],
                'settings' => [
                    'host_video' => false,
                    'participant_video' => false,
                    'waiting_room' => true,
                ]
            ]);

            $resp = json_decode($response);
            $resp->start_time = $request->start_time;
            return $this->store($resp,$request->appointment_id);
        }
    }
    public function delete(Request $request, string $id) {
        $path = 'meetings/' . $id;
        $response = $this->zoomDelete($path);

        return [
            'success' => $response->status() === 204,
            'data' => json_decode($response->body(), true),
        ];
    }

    public function store($data,$appointment_id){
        $virtualRoom = VirtualRoom::where(['appointment_id'=>$appointment_id])->first();
        if (!$virtualRoom){
            $virtualRoom = new VirtualRoom();
            $virtualRoom->appointment_id = $appointment_id;
        }
        $virtualRoom->uuid = $data->topic;
        $virtualRoom->meeting_id = $data->id;
        $virtualRoom->host_id = $data->host_id;
        $virtualRoom->host_email = $data->host_email;
        $virtualRoom->topic = $data->topic;
        $virtualRoom->start_time = $data->start_time;
        $virtualRoom->duration = $data->duration;
        $virtualRoom->agenda = $data->agenda;
        $virtualRoom->type = $data->type;
        $virtualRoom->start_url = $data->start_url;
        $virtualRoom->join_url = $data->join_url;
        $virtualRoom->status = $data->status;
        $virtualRoom->zoom_response = json_encode($data);
        if ($virtualRoom->save()){
            return $virtualRoom;
        }
        return null;
    }
}
