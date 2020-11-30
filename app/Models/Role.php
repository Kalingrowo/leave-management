<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_role');
    }

    /**
     * functions
     * */
    public static function getAllRoles()
    {
        return self::get();
    }
    public static function getRoleDetail($id)
    {
        return self::where('id', $id)
            ->with('permissions')
            ->first();
    }
}
