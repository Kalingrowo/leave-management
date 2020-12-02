<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\Leave;
use DB;
use Exception;
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

            $getListLeave = $this->getAllLeave();
            $getListLeave = $getListLeave->original['data'];

            DB::commit();
            return response()->json([
                'data' => $getListLeave
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * @param Illuminate\Http\Request $request
     * @return json
     */
    public function update(Request $request)
    {
        DB::beginTransaction();
        try {
            $leave = Leave::where('id', $request->leave_id)->first();

            if (!$leave) {
                throw new Exception("Data tidak ditemukan !", 404);
            }

            $leave->name = $request->name;
            $leave->allocated_days = $request->allocated_days;
            $leave->note = $request->note;
            $leave->save();

            $getListLeave = $this->getAllLeave();
            $getListLeave = $getListLeave->original['data'];

            DB::commit();
            return response()->json([
                'data' => $getListLeave
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * @param Illuminate\Http\Request $request
     * @return json
     */
    public function toggleStatus(Request $request)
    {
        DB::beginTransaction();
        try {
            $leave = Leave::where('id', $request->leave_id)->first();

            if (!$leave) {
                throw new Exception("Data tidak ditemukan !", 404);
            }

            if ($leave->is_active == 'Y') {
                $leave->is_active = 'N';
            } else {
                $leave->is_active = 'Y';
            }
            $leave->save();

            $getListLeave = $this->getAllLeave();
            $getListLeave = $getListLeave->original['data'];

            DB::commit();
            return response()->json([
                'data' => $getListLeave
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
