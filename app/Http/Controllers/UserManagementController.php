<?php

namespace App\Http\Controllers;

use App\Models\User;
use Crypt;
use DB;
use Exception;
use Hash;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    public function getAllUsers()
    {
        $listUsers = User::get();

        return response()->json([
            'data' => $listUsers
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

            $registerData = $request->all();
            $registerUser = new User();
            $registerUser->name = $request->name;
            $registerUser->email = $request->email;
            $registerUser->password = Hash::make($request->password);
            $registerUser->save();

            $getListUser = $this->getAllUsers();
            $getListUser = $getListUser->original['data'];

            DB::commit();
            return response()->json([
                'data' => $getListUser
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * @param string $id
     * @return json
     */
    public function delete($id)
    {
        DB::beginTransaction();
        try {

            $userId = Crypt::decrypt($id);
            
            $user = User::where('id', $userId)->first();
            if (!$user) {
                throw new Exception("Data tidak ditemukan !", 404);
            }

            $user->delete();

            $getListUser = $this->getAllUsers();
            $getListUser = $getListUser->original['data'];

            DB::commit();
            return response()->json([
                'data' => $getListUser
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
