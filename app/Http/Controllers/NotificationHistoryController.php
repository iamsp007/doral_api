<?php

namespace App\Http\Controllers;

use App\Models\NotificationHistory;
use Illuminate\Http\Request;

class NotificationHistoryController extends Controller
{
    public function index()
    {
        $notificationHistory = NotificationHistory::with('sender','receiver','request')->get();

        return $this->generateResponse(true, 'Notification history!', $notificationHistory);
    }
}
