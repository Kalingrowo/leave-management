<?php

namespace App\Http\Controllers\UserAccess;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Crypt;
use DB;
use Illuminate\Http\Request;

class UserAccessController extends Controller
{
    /**
     * @return json
     */
    public function getAllPermissions()
    {
        $getPersmissions = Permission::get();

        return response()->json([
            'data' => $getPersmissions
        ], 200);
    }

    /**
     * @return json
     */
    public function getAllRoles()
    {
        $getPersmissions = Role::get();

        return response()->json([
            'data' => $getPersmissions
        ], 200);
    }

    /**
     * @param int role_id
     * @return json
     */
    public function getRoleDetail($roleId)
    {
        $roleDetail = Role::where('id', $roleId)
            ->with('permissions')
            ->first();

        return response()->json([
            'data' => $roleDetail
        ], 200);
    }

    /**
     * @param int encrypted_user_id
     * @return json
     */
    public function assignPermissionsToUser(Request $request)
    {
        DB::beginTransaction();
        try {
            $listPermissions = $request->permissions;
            $targetUser = Crypt::decrypt($request->user_id);
            $targetUser = User::where('id', $targetUser)->first();

            if (is_null($targetUser)) {
                throw new Exception("Data tidak ditemukan !", 404);
            }

            $targetUser->givePermissionsTo($listPermissions);
            $targetUser->refresh();

            DB::commit();
            return response()->json([
                'target_user' => $targetUser
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * @param int encrypted_user_id
     * @return json
     */
    public function revokePermissionsFromUser(Request $request)
    {
        DB::beginTransaction();
        try {
            $listPermissions = $request->permissions;
            $targetUser = Crypt::decrypt($request->user_id);
            $targetUser = User::where('id', $targetUser)->first();

            if (is_null($targetUser)) {
                throw new Exception("Data tidak ditemukan !", 404);
            }

            $targetUser->deletePermissions($listPermissions);
            $targetUser->refresh();

            DB::commit();
            return response()->json([
                'target_user' => $targetUser
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function storePermission(Request $request)
    {
        DB::beginTransaction();
        try {
            if (Permission::where('slug', $request->slug)->exists()) {
                throw new Exception("Nama 'permission' sudah digunakan, gunakan nama yang lain !", 400);
            }

            $data = new Permission();
            $data->name = $request->name;
            $data->slug = $request->slug;
            $data->save();

            DB::commit();
            return response()->json([
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

}
