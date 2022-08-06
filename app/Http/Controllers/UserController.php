<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function index()
    {
        $component = [
            'active' => 'user'
        ];

        return view('pages.user', $component);
    }


    public function _loadUser()
    {
        $data = DB::table('tb_user')->select('*')->get();
        return DataTables::of($data)->make(true);
    }
}
