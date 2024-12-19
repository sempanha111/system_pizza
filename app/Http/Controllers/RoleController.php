<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function get_role(){
        $role = Role::all();
        return response()->json($role,200);
    }
}
