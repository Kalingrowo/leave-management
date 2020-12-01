<?php

namespace App\Traits;

use App\Models\Permission;
use App\Models\Role;

/**
 * 
 */
trait HasRolesAndPermissions
{
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_user');
    }

    /**
     * @param mixed ...$roles
     * @return bool
     */
    public function hasRole(... $roles)
    {
        foreach ($roles as $role) {
            if ($this->roles->contains('slug', $role)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $permission
     * @return bool
     */
    protected function hasPermission($permission)
    {
        return (bool) $this->permissions->where('slug', $permission->slug)->count();
    }

    /**
     * @param $permission
     * @return bool
     */
    protected function hasPermissionTo($permission)
    {
        return $this->hasPermissionThroughRole($permission) || $this->hasPermission($permission);
    }

    /**
     * @param $permission
     * @return bool
     */
    public function hasPermissionThroughRole($permission)
    {
        foreach ($permission->roles as $role) {
            if ($this->roles->contains($role)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param array $permission
     * @return mixed
     */
    protected function getAllPermissions(array $permissions)
    {
        return Permission::whereIn('slug', $permissions)->get();
    }

    /**
     * @param array $permissions
     * @return $this
     */
    public function givePermissionsTo(array $permissions)
    {
        $permissions = $this->getAllPermissions($permissions);
        if ($permissions === null) {
            return $this;
        }

        $this->permissions()->syncWithoutDetaching($permissions);;
        return $this;
    }

    /**
     * @param mixed ...$permissions
     * @return $this
     */
    public function revokePermissions(array $permissions)
    {
        $permissions = $this->getAllPermissions($permissions);
        $this->permissions()->detach($permissions);
        return $this;
    }

    /**
     * @param mixed ...$permissions
     * @return HasRolesAndPermissions
     */
    public function refreshPermissions(... $permissions)
    {
        $this->permissions()->detach();
        return $this->givePermissionsTo($permissions);
    }

    /**
     * @param array $roles
     * @return mixed
     */
    protected function getAllRoles(array $roles)
    {
        return Role::whereIn('slug', $roles)->get();
    }

    /**
     * @param array $roles
     * @return $this
     */
    public function giveRolesTo(array $roles)
    {
        $roles = $this->getAllRoles($roles);
        if ($roles === null) {
            return $this;
        }

        $this->roles()->syncWithoutDetaching($roles);;
        return $this;
    }

}
