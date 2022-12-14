<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Js;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\Session;
use DateTime;

class DashboardController extends Controller
{

    function test()
    {
    }

    //--------- Auth
    public function login()
    {
        $components['active'] = 'login';
        return view('auth.login', $components);
    }

    public function logout()
    {
        Session::flush();
        return redirect()->to('/login');
    }

    public function authorization()
    {
        $email    = $_POST['email'];
        $password = $_POST['password'];

        # cek email
        $user = DB::table('tb_user')->select('*')->where('email', '=', $email)->where(function ($query) {
            $query->where('role', '=', 'Smw')->orWhere('role', '=', 'Pusat');
        })->where('is_verify', '=', 'verified')->get()->first();
        if (!$user) {
            return response()->json('Email tidak ditemukan', 404);
        } else {
            # verifikasi Password
            $verify = password_verify($password, $user->password);
            if (!$verify) {
                return response()->json('Password salah', 401);
            } else {

                # simpan session
                $data = [
                    'nama'    => $user->nama,
                    'role'    => $user->role,
                    'email'   => $email,
                    'pass'    => $password,
                    'wilayah' => $user->wilayah
                ];
                Session::put('user', json_encode($data));
                return response()->json('Login Success', 200);
            }
        }
    }

    public function index()
    {
        $checklist = DB::table('tb_checklist')->select('*')->get()->toArray();
        $components['checklist']    = $checklist;
        $components['active']       = 'dashboard';
        return view('pages/menu', $components);
    }

    public function _getPremisesData()
    {
        if (request()->has('premises')) {

            $startDate = date('Y-m-01');
            $endDate   = date('Y-m-31');

            $premises = request()->get('premises');

            $currentMonthData = DB::table('tb_checksheet')->select('*')
                ->whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate)
                ->where('premises', $premises)
                ->orderBy('created_at')
                ->where('verifikasi', 'closing')
                ->get()->toArray();

            $baik       = array();
            $kurang     = array();
            $perlu      = array();
            $na         = array();
            $data       = array();

            foreach ($currentMonthData as $item) {
                if ($item->kondisi == 'baik') {
                    array_push($baik, $item);
                } else if ($item->kondisi == 'kurang baik') {
                    array_push($kurang, $item);
                } else if ($item->kondisi == 'perlu perbaikan' || $item->kondisi == 'perlu penggantian') {
                    array_push($perlu, $item);
                } else if ($item->kondisi == 'not available') {
                    array_push($na, $item);
                }

                array_push($data, $item);
            }

            $pieData = [
                [
                    'category' => "baik",
                    'sector'   => "Baik",
                    'size'     => sizeOf($baik)
                ],
                [
                    'category' => "kurang baik",
                    'sector'   => "kurang baik",
                    'size'     => sizeOf($kurang)
                ],
                [
                    'category' => "Perlu Perbaikan",
                    'sector'   => "Perlu Perbaikan",
                    'size'     => sizeOf($perlu)
                ],
                [
                    'category' => "Not Available",
                    'sector'   => "Not Available",
                    'size'     => sizeOf($na)
                ]
            ];

            $result = [
                'pieData' => $pieData,
                'data'    => $data
            ];
            return response()->json($result, 200);
        }
    }

    public function load_barchart()
    {
        if (request()->ajax()) {

            $startDate = date('Y-m-01');
            $endDate   = date('Y-m-31');

            //---- Ambil data Wilayah Berdasarkan parameter
            $premises = request()->get('premises');
            $kondisi  = request()->get('kondisi');

            $data  = DB::table('tb_user')
                ->join('tb_checksheet', 'tb_user.id', '=', 'tb_checksheet.id_user', 'inner')
                ->select('*')
                ->where('kondisi', '=', $kondisi)
                ->where('premises', '=', $premises)
                ->whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate)
                ->where('verifikasi', 'closing')
                ->get()->toArray();

            $semuaWilayah = DB::table('tb_outlet')
                ->distinct('wilayah')
                ->groupBy('wilayah')
                ->get()->toArray();

            for ($i = 0; $i < sizeof($semuaWilayah); $i++) {
                $resultGroupBy = array_keys(array_keys(array_combine(array_keys($data), array_column($data, 'wilayah')), $semuaWilayah[$i]->wilayah));
                $result[$i] = [
                    'label' => $semuaWilayah[$i]->wilayah,
                    'value' => sizeof($resultGroupBy)
                ];
            }

            return json_encode(array(
                'code'      => 200,
                'message'   => 'Berhasil Mendapatkan Data',
                'data'      => $result
            ));
        }
    }



    public function get_cabang($wilayah, $kondisi, $premises)
    {
        $startDate   = date('Y-m-01');
        $endDate     = date('Y-m-31');
        $checksheet  = DB::table('tb_user')
            ->join('tb_checksheet', 'tb_user.id', '=', 'tb_checksheet.id_user', 'inner')
            ->select('*')
            ->where('kondisi', '=', $kondisi)
            ->where('premises', '=', $premises)
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->where('verifikasi', '=', 'closing')
            ->get()->toArray();
        $getCabang   = DB::table('tb_outlet')->select('*')->where('wilayah', '=', $wilayah)->groupBy('cabang')->get()->toArray();
        $data = array();
        foreach ($getCabang as $index => $value) {
            $resultGroupBy = array_keys(array_keys(array_combine(array_keys($checksheet), array_column($checksheet, 'cabang')), $getCabang[$index]->outlet));
            $data[$index] = [
                'cabang' => $value->cabang,
                'label'  => $value->outlet,
                'value'  => sizeof($resultGroupBy)
            ];
        }

        $components['active']   = 'dashboard';
        $components['data']     = $data;
        $components['premises'] = $premises;
        $components['category'] = $kondisi;
        return view('pages.cabang', $components);
    }

    public function get_outlet($cabang, $kondisi, $premises)
    {
        $startDate   = date('Y-m-01');
        $endDate     = date('Y-m-31');
        $checksheet  = DB::table('tb_user')
            ->join('tb_checksheet', 'tb_user.id', '=', 'tb_checksheet.id_user', 'inner')
            ->select('*')
            ->where('kondisi', '=', $kondisi)
            ->where('premises', '=', $premises)
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->where('verifikasi', 'closing')
            ->get()->toArray();

        $getOutlet   = DB::table('tb_outlet')->select('*')->where('cabang', '=', $cabang)->groupBy('outlet')->get()->toArray();
        $data = array();
        foreach ($getOutlet as $index => $value) {
            $resultGroupBy = array_keys(array_keys(array_combine(array_keys($checksheet), array_column($checksheet, 'outlet')), $getOutlet[$index]->outlet));
            $data[$index] = [
                'label' => $getOutlet[$index]->outlet,
                'value' => sizeof($resultGroupBy)
            ];
        }

        $components['active'] = 'dashboard';
        $components['data']   = $data;
        $components['premises'] = $premises;
        $components['category'] = $kondisi;
        return view('pages.outlet', $components);
    }



    //--------------- DATATABLE ----------------//

    public function datatable()
    {
        $getYear       = DB::table('tb_checksheet')->select(DB::raw('YEAR(created_at) as year'))->distinct()->get();
        $existingYear  = $getYear->pluck('year')->toArray();
        $getMonth      = DB::table('tb_checksheet')->select(DB::raw('MONTH(created_at) as month'))->distinct()->get();
        $numberOfMonth = $getMonth->pluck('month')->toArray();
        $existingMonth = array();

        foreach ($numberOfMonth as $key) {
            $dateObj   = DateTime::createFromFormat('!m', $key);
            $monthName = $dateObj->format('F');
            array_push($existingMonth, $monthName);
        }

        $components = [
            'active'        => 'datatable',
            'year'          => $existingYear,
            'month'         => $existingMonth,
            'numberOfMonth' => $numberOfMonth
        ];

        return view('pages.datatable', $components);
    }

    public function report()
    {
        $getYear       = DB::table('tb_checksheet')->select(DB::raw('YEAR(created_at) as year'))->distinct()->get();
        $existingYear  = $getYear->pluck('year')->toArray();
        $getMonth      = DB::table('tb_checksheet')->select(DB::raw('MONTH(created_at) as month'))->distinct()->get();
        $numberOfMonth = $getMonth->pluck('month')->toArray();
        $existingMonth = array();

        $wilayah       = DB::table('tb_outlet')->select('wilayah')->where('wilayah', '!=', 'wilayah');
        $user          = json_decode(session()->get('user'));
        if ($user) {
            if ($user->role == 'Smw') {
                $wilayah->where('wilayah', '=', $user->wilayah);
            }
        }
        $wilayah->orderBy('wilayah', 'ASC')->distinct();

        foreach ($numberOfMonth as $key) {
            $dateObj   = DateTime::createFromFormat('!m', $key);
            $monthName = $dateObj->format('F');
            array_push($existingMonth, $monthName);
        }

        $components = [
            'active'        => 'report',
            'year'          => $existingYear,
            'month'         => $existingMonth,
            'numberOfMonth' => $numberOfMonth,
            'wilayah'       => $wilayah->get()->toArray()
        ];

        return view('pages.report', $components);
    }

    public function datatable_with_parameter($premises, $category, $outlet)
    {
        $getYear       = DB::table('tb_checksheet')->select(DB::raw('YEAR(created_at) as year'))->distinct()->get();
        $existingYear  = $getYear->pluck('year')->toArray();
        $getMonth      = DB::table('tb_checksheet')->select(DB::raw('MONTH(created_at) as month'))->distinct()->get();
        $numberOfMonth = $getMonth->pluck('month')->toArray();
        $existingMonth = array();

        foreach ($numberOfMonth as $key) {
            $dateObj   = DateTime::createFromFormat('!m', $key);
            $monthName = $dateObj->format('F');
            array_push($existingMonth, $monthName);
        }

        $components = [
            'active'        => 'datatable',
            'premises'      => $premises,
            'kondisi'       => $category,
            'outlet'        => $outlet,
            'year'          => $existingYear,
            'month'         => $existingMonth,
            'numberOfMonth' => $numberOfMonth
        ];

        return view('pages.datatable', $components);
    }


    public function load_datatable()
    {

        $date               = request()->get('date');
        $parameter_premises = request()->get('parameter_premises');
        $parameter_kondisi  = request()->get('parameter_kondisi');
        $parameter_outlet   = request()->get('parameter_outlet');


        $data = DB::table('tb_checksheet')->select(array('img', 'kondisi_smw', 'catatan_smw', 'nama_smw', 'nama_pusat', 'catatan_pusat', 'tb_checksheet.id', 'wilayah', 'cabang', 'outlet', 'premises', 'kategori', 'kondisi', 'verifikasi', DB::raw('DATE(created_at) AS submitDate')))
            ->join('tb_user', 'tb_checksheet.id_user', '=', 'tb_user.id')->where('verifikasi', 'closing');

        if ($date) {
            $data->where('created_at', '>=', $date);
        }

        // Pengecekan parameter tahun dan bulan
        if (request()->has('year')) {
            $year = request()->get('year');
            if ($year) {
                $data->whereYear('created_at', '=', $year);
            }
        }

        if (request()->has('month')) {
            $month = request()->get('month');
            if ($month) {
                $data->whereMonth('created_at', '=', $month);
            }
        }

        if ($parameter_premises && $parameter_kondisi && $parameter_outlet) {
            $startDate = date('Y-m-01');
            $endDate   = date('Y-m-31');

            $data->where('premises', '=', $parameter_premises)
                ->where('kondisi', '=', $parameter_kondisi)
                ->where('created_at', '>=', $startDate)
                ->where('created_at', '<=', $endDate)
                ->where('outlet', '=', $parameter_outlet);
        }

        return DataTables::of($data->get())
            ->addIndexColumn()
            ->addColumn('action', function ($data) {
                $html = '<button class="btn btn-primary btn-detail" style="margin-left:10px" data-id="' . $data->id . '"><ion-icon name="eye-outline"></ion-icon></button>';
                return $html;
            })->make(true);
    }

    public function load_report()
    {

        $parameter_premises = request()->get('parameter_premises');
        $parameter_kondisi  = request()->get('parameter_kondisi');
        $parameter_outlet   = request()->get('parameter_outlet');


        $data = DB::table('tb_checksheet')->select(array('img', 'kondisi_smw', 'catatan_smw', 'nama_smw', 'nama_pusat', 'catatan_pusat', 'tb_checksheet.id', 'wilayah', 'cabang', 'outlet', 'premises', 'kategori', 'kondisi', 'verifikasi', DB::raw('DATE(created_at) AS submitDate')))
            ->join('tb_user', 'tb_checksheet.id_user', '=', 'tb_user.id');

        if (request()->has('startDate')) {
            $startDate = request()->get('startDate');
            if ($startDate) {
                $data->whereDate('created_at', '>=', $startDate);
            }
        }

        if (request()->has('endDate')) {
            $endDate = request()->get('endDate');
            if ($endDate) {
                $data->whereDate('created_at', '<=', $endDate);
            }
        }

        if (request()->has('wilayah')) {
            $wilayah = request()->get('wilayah');
            if ($wilayah) {
                $data->where('wilayah', $wilayah);
            }
        }


        $user          = json_decode(session()->get('user'));
        if ($user) {
            if ($user->role == 'Smw') {
                $data->where('wilayah', '=', $user->wilayah);
            }
        }

        if ($parameter_premises && $parameter_kondisi && $parameter_outlet) {
            $startDate = date('Y-m-01');
            $endDate   = date('Y-m-31');

            $data->where('premises', '=', $parameter_premises)
                ->where('kondisi', '=', $parameter_kondisi)
                ->where('created_at', '>=', $startDate)
                ->where('created_at', '<=', $endDate)
                ->where('outlet', '=', $parameter_outlet);
        }

        $data->orderBy('tb_user.outlet', 'ASC');

        return DataTables::of($data->get())
            ->addIndexColumn()
            ->addColumn('action', function ($data) {
                $html = '<button class="btn btn-primary btn-detail" style="margin-left:10px" data-id="' . $data->id . '"><ion-icon name="eye-outline"></ion-icon></button>';
                return $html;
            })->make(true);
    }

    public function get_detail()
    {
        if (request()->ajax()) {

            $checkId     = $_POST['id'];
            $data        = DB::table('tb_user')
                ->join('tb_checksheet', 'tb_user.id', '=', 'tb_checksheet.id_user', 'inner')
                ->select('*')
                ->where('tb_checksheet.id', $checkId)
                ->first();;
            $evidence    = DB::table('tb_evidence')->select('*')->where('id_checksheet', $checkId)->get();

            return json_encode(array(
                'code'      => 200,
                'message'   => 'Berhasil Mendapatkan Data Detail',
                'data'      => $data,
                'id'        => $checkId,
                'evidence'  => $evidence
            ));
        }
    }


    public function dashboard_smw($email, $password)
    {
        # cek user apakah ada atau tidak
        $user = DB::table('tb_user')->select('*')->where('email', $email)->where('role', 'Smw')->get()->first();
        if (!$user) {
            return redirect()->to('/');
        } else {
            # verifikasi password
            $verify = password_verify($password, $user->password);
            if (!$verify) {
                return redirect()->to('/');
            } else {
                # simpan session
                $data = [
                    'nama'    => $user->nama,
                    'role'    => $user->role,
                    'email'   => $email,
                    'pass'    => $password,
                    'wilayah' => $user->wilayah
                ];
                Session::put('user', json_encode($data));
                return redirect()->to('/');
            }
        }
    }

    //--------------- END DATATABLE --------------//
}
