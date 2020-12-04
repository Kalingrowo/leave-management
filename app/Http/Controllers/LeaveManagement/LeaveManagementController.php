<?php

namespace App\Http\Controllers\LeaveManagement;

use App\Http\Controllers\Controller;
use App\Models\LeaveManagement\LeaveRequest;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Http\Request;

class LeaveManagementController extends Controller
{
    public function getAllLeaveRequests()
    {
        $listRequests = LeaveRequest::with('leave')
            ->with('user')
            ->get();

        return response()->json([
            'data' => $listRequests
        ], 200);
    }

    public function storeLeaveRequest(Request $request)
    {
        DB::beginTransaction();
        try {
            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);
            $userId = $request->user()->id;
            $leaveId = $request->leave_id;

            $isExist = LeaveRequest::where('user_id', $userId)
                ->where(function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('start_date', [$startDate, $endDate])
                        ->orWhereBetween('end_date', [$startDate, $endDate]);
                })
                ->exists();

            if ($isExist) {
                throw new Exception("Tanggal terpilih sudah pernah digunakan, gunakan tanggal yang lain !", 400);
            }

            $data = new LeaveRequest();
            $data->user_id = $userId;
            $data->leave_id = $leaveId;
            $data->start_date = $startDate;
            $data->end_date = $endDate;
            $data->save();

            $listAll = $this->getAllLeaveRequests();
            $listAll = $listAll->original['data'];

            DB::commit();
            return response()->json([
                'data' => $listAll
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
