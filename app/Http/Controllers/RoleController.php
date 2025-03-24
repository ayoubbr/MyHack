<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function update($userId, Request $request)
    {
        $user =  User::find($userId);
        $role =  Role::where('role_name', $request['role_name'])->first();

        $user->role()->associate($role);
        $user->save();

        return response()->json([
            'user' => $user
        ]);
    }
}
