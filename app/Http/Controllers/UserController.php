<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UsersModel;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use PDO;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function index()
    {
        $wilayah   = DB::table('tb_outlet')->select(DB::raw("DISTINCT(wilayah)"))->get()->toArray();
        $component = [
            'active'  => 'user',
            'wilayah' => $wilayah
        ];

        return view('pages.user', $component);
    }



    //--------- NON PAGES FUNCTION
    public function _loadUser()
    {
        $data = DB::table('tb_user')->select('*');

        # filtering
        if (request()->has('role')) {
            $role = request()->get('role');
            if ($role) {
                $data->where('role', '=', $role);
            }
        }

        if (request()->has('wilayah')) {
            $wilayah = request()->get('wilayah');
            if ($wilayah) {
                $data->where('wilayah', '=', $wilayah);
            }
        }

        if (request()->has('cabang')) {
            $cabang = request()->get('cabang');
            if ($cabang) {
                $cabangOutlet = DB::table('tb_outlet')->select('*')->where('cabang', $cabang)->get()->first();
                $data->where('cabang', '=', $cabangOutlet->outlet);
            }
        }

        if (request()->has('outlet')) {
            $outlet = request()->get('outlet');
            if ($outlet) {
                $data->where('outlet', '=', $outlet);
            }
        }

        return DataTables::of($data->get())
            ->addIndexColumn()
            ->addColumn('action', function ($data) {
                $html = '<button class="btn btn-primary btn-detail" style="margin-left:10px" data-id="' . $data->id . '"><ion-icon name="eye-outline"></ion-icon></button>';
                $html .= '<button class="btn btn-danger btn-delete" style="margin-left:10px" data-id="' . $data->id . '"><ion-icon name="close-circle"></ion-icon></button>';
                return $html;
            })
            ->make(true);
    }

    public function _loadCabang()
    {
        if (request()->ajax()) {
            if (isset($_POST['wilayah'])) {
                $wilayah = $_POST['wilayah'];
                if ($wilayah) {
                    $cabang = DB::table('tb_outlet')->select('*')->where('wilayah', $wilayah)->groupBy('cabang')->get()->toArray();
                    return response()->json($cabang, 200);
                }
            }
        }
    }

    public function _loadOutlet()
    {
        if (request()->ajax()) {
            if (isset($_POST['cabang'])) {
                $cabang = $_POST['cabang'];
                if ($cabang) {
                    $outlet = DB::table('tb_outlet')->select('*')->where('cabang', $cabang)->get()->toArray();
                    return response()->json($outlet, 200);
                }
            }
        }
    }

    public function _loadOutlet2()
    {
        if (request()->ajax()) {
            if (isset($_POST['cabang'])) {
                $cabang         = $_POST['cabang'];
                $realCabang     = DB::table('tb_outlet')->select('*')->where('outlet', $cabang)->get()->first()->cabang;

                if ($cabang) {
                    $outlet = DB::table('tb_outlet')->select('*')->where('cabang', $realCabang)->get()->toArray();
                    return response()->json($outlet, 200);
                }
            }
        }
    }

    public function _passwordConfirmation()
    {
        if (request()->ajax()) {
            if (isset($_POST['password']) && isset($_POST['userId'])) {
                $password = $_POST['password'];

                #verifying password
                $userSession = json_decode(request()->session()->get('user'));
                $userDB      = DB::table('tb_user')->select('*')->where('email', $userSession->email)->get()->first();

                $verify      = password_verify($password, $userDB->password);
                if ($verify) {

                    # hapus user
                    $userId = $_POST['userId'];
                    $delete = DB::table('tb_user')->delete($userId);

                    if ($delete) {
                        return response()->json($verify, 200);
                    }
                }
            }
        }
    }

    public function _getUser()
    {
        if (request()->ajax()) {
            if (isset($_POST['id'])) {
                $id   = $_POST['id'];
                $user   = DB::table('tb_user')->select('*')->where('id', '=', $id)->get()->first();
                $cabang = DB::table('tb_outlet')->select('*')->where('outlet', $user->cabang)->get()->first();

                if ($user) {
                    return response()->json(array(
                        'user'   => $user,
                        'cabang' => $cabang
                    ), 200);
                }
            }
        }
    }

    public function _saveUser()
    {
        $user     = UsersModel::findOrFail($_POST['id']);

        $data = [
            'email'     => $_POST['email'],
            'nama'      => $_POST['nama'],
            'hp'        => $_POST['hp'],
            'wilayah'   => $_POST['detailWilayah']
        ];


        if (isset($_POST['detailRole'])) {
            if ($_POST['detailRole']) {
                $data['role'] = $_POST['detailRole'];
            }
        }


        if (isset($_POST['detailCabang'])) {
            if ($_POST['detailCabang']) {
                $data['cabang'] = $_POST['detailCabang'];
            }
        };

        if (isset($_POST['detailOutlet'])) {
            if ($_POST['detailOutlet']) {
                $data['outlet'] = $_POST['detailOutlet'];
            }
        };

        if (isset($_POST['password'])) {
            if ($_POST['password']) {
                $data['password'] = password_hash($_POST['password'], PASSWORD_BCRYPT);
            }
        };

        $user->update($data);
        return redirect()->to('/user')->with('success', 'Berhasil melakukan perubahan data');
    }
}
