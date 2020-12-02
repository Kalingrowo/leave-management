<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\Leave;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    public function getAllLeave()
    {
        $getLeave = Leave::get();

        return response()->json([
            'data' => $getLeave
        ], 200);
    }
}
