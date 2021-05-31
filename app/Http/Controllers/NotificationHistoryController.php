<?php

namespace App\Http\Controllers;

use App\Models\NotificationHistory;
use Illuminate\Http\Request;

class NotificationHistoryController extends Controller
{
    public function index()
    {
        $notificationHistory = NotificationHistory::where('is_read', '0')->with('sender','receiver','request')->get();

        return $this->generateResponse(true, 'Notification history!', $notificationHistory);
    }

    public function readNotification($id)
    {
        try {
            NotificationHistory::find($id)->update([
                'is_read' => '1',
            ]);

            return $this->generateResponse(true, 'Notification read successfully', null, 200);
        } catch (\Exception $ex) {
            return $this->generateResponse(false, $ex->getMessage());
        }
    }
}
