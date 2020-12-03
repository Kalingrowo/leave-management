<?php

namespace App\Http\Controllers;

use App\Models\User;
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
}
