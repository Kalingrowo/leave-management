<?php

namespace App\Http\Controllers\LeaveManagement;

use App\Http\Controllers\Controller;
use App\Models\LeaveManagement\LeaveRequest;
use Carbon\Carbon;
use Crypt;
use DB;
use Exception;
use Illuminate\Http\Request;

class LeaveManagementController extends Controller
{
    /**
     * @param Illuminate\Http\Request $request
     * @return json
     */
    public function getAllLeaveRequests(Request $request)
    {
        $listRequests = LeaveRequest::with('leave')
            ->with('user');
            
        $userId = $request->user()->id;
        if ($request->user()->hasRole('admin', 'general-affair')) {
            if ($request->user_id) {
                $userId = Crypt::decrypt($request->user_id);
                $listRequests = $listRequests->where('user_id', $userId);
            }
        } else {
            $listRequests = $listRequests->where('user_id', $userId);
        }

        if ($request->start_date && $request->end_date) {
            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);
            $listRequests = $listRequests->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate]);
            });
        }

        $listRequests = $listRequests->get();

        return response()->json([
            'data' => $listRequests
        ], 200);
    }

    /**
     * @param Illuminate\Http\Request $request
     * @return json
     */
    public function storeLeaveRequest(Request $request)
    {
        DB::beginTransaction();
        try {
            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);
            $leaveId = $request->leave_id;
            $userId = $request->user()->id;
            
            if ($request->user_id) {
                if (!$request->user()->hasRole('admin', 'general-affair')) {
                    throw new Exception("Anda tidak memiliki akses untuk mencatatkan cuti atas nama user lain !", 1);
                }
                $userId = Crypt::decrypt($request->user_id);
            }

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

            $tempRequest = new Request();
            $tempRequest->setUserResolver($request->getUserResolver());
            $listAll = $this->getAllLeaveRequests($tempRequest);
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
