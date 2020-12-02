<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\Leave;
use DB;
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

    /**
     * @param Illuminate\Http\Request $request
     * @return json
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $newLeave = new Leave();
            $newLeave->name = $request->name;
            $newLeave->allocated_days = $request->allocated_days;
            $newLeave->note = $request->note;
            $newLeave->save();

            DB::commit();
            return response()->json([], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'data' => $th->getMessage()
            ], 500);
        }
    }
}
