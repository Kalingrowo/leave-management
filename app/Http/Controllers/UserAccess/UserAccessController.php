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
     * @param Illuminate\Http\Request $request
     * @return json
     */
    public function storePermission(Request $request)
    {
        DB::beginTransaction();
        try {
            if (Permission::where('slug', $request->slug)->exists()) {
                throw new \Exception("Nama permission '" . $request->name . "' sudah digunakan, gunakan nama yang lain !", 400);
            }

            $data = new Permission();
            $data->name = $request->name;
            $data->slug = $request->slug;
            $data->save();

            $allPermissions = $this->getAllPermissions();
            $allPermissions = $allPermissions->original['data'];

            DB::commit();
            return response()->json([
                'data' => $allPermissions
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
                'data' => $targetUser
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

            $targetUser->revokePermissions($listPermissions);
            $targetUser->refresh();

            DB::commit();
            return response()->json([
                'data' => $targetUser
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
     * used to sync permissions ( detach all permission then attach new permissions )
     */
    public function refreshUserPermissions(Request $request)
    {
        DB::beginTransaction();
        try {
            $listPermissions = $request->permissions;
            $targetUser = Crypt::decrypt($request->user_id);
            $targetUser = User::where('id', $targetUser)->first();

            if (is_null($targetUser)) {
                throw new Exception("Data tidak ditemukan !", 404);
            }

            $targetUser->refreshPermissions($listPermissions);
            $targetUser->refresh();

            DB::commit();
            return response()->json([
                'data' => $targetUser->permissions
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * @param int $permissionId
     * @return json
     */
    public function deletePermission($permissionId)
    {
        DB::beginTransaction();
        try {
            Permission::where('id', $permissionId)->delete();

            $allPermissions = $this->getAllPermissions();
            $allPermissions = $allPermissions->original['data'];

            DB::commit();
            return response()->json([
                'data' => $allPermissions
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
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
     * @param Illuminate\Http\Request $request
     * @return json
     */
    public function storeRole(Request $request)
    {
        DB::beginTransaction();
        try {
            if (Role::where('slug', $request->slug)->exists()) {
                throw new \Exception("Nama role '" . $request->name . "' sudah digunakan, gunakan nama yang lain !", 400);
            }

            $data = new Role();
            $data->name = $request->name;
            $data->slug = $request->slug;
            $data->save();

            $allRoles = $this->getAllRoles();
            $allRoles = $allRoles->original['data'];

            DB::commit();
            return response()->json([
                'data' => $allRoles
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
    public function assignRolesToUser(Request $request)
    {
        DB::beginTransaction();
        try {
            $listRoles = $request->roles;
            $targetUser = Crypt::decrypt($request->user_id);
            $targetUser = User::where('id', $targetUser)->first();

            if (is_null($targetUser)) {
                throw new Exception("Data tidak ditemukan !", 404);
            }

            $targetUser->giveRolesTo($listRoles);
            $targetUser->refresh();

            DB::commit();
            return response()->json([
                'data' => $targetUser
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
    public function revokeRolesFromUser(Request $request)
    {
        DB::beginTransaction();
        try {
            $listRoles = $request->roles;
            $targetUser = Crypt::decrypt($request->user_id);
            $targetUser = User::where('id', $targetUser)->first();

            if (is_null($targetUser)) {
                throw new Exception("Data tidak ditemukan !", 404);
            }

            $targetUser->revokeRoles($listRoles);
            $targetUser->refresh();

            DB::commit();
            return response()->json([
                'data' => $targetUser->roles
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
     * used to sync permissions ( detach all permission then attach new permissions )
     */
    public function refreshUserRoles(Request $request)
    {
        DB::beginTransaction();
        try {
            $listRoles = $request->roles;
            $targetUser = Crypt::decrypt($request->user_id);
            $targetUser = User::where('id', $targetUser)->first();

            if (is_null($targetUser)) {
                throw new Exception("Data tidak ditemukan !", 404);
            }

            $targetUser->refreshRoles($listRoles);
            $targetUser->refresh();

            DB::commit();
            return response()->json([
                'data' => $targetUser
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }


    /**
     * @param int $permissionId
     * @return json
     */
    public function deleteRole($roleId)
    {
        DB::beginTransaction();
        try {
            Role::where('id', $roleId)->delete();

            $allRoles = $this->getAllRoles();
            $allRoles = $allRoles->original['data'];

            DB::commit();
            return response()->json([
                'data' => $allRoles
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
