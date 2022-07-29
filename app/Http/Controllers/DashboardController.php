<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class DashboardController extends Controller
{
    public function index()
    {
        $ceksheet = DB::table('tb_checksheet')->get();
        $startDate = date('Y-m-01');
        $endDate   = date('Y-m-31');

        $currentMonthData = DB::table('tb_checksheet')->select('*')
            ->where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)
            ->orderBy('created_at')
            ->get()->toArray();

        $allData = DB::table('tb_checksheet')->select('*')
            ->orderBy('created_at')
            ->get()->toArray();

        //--- Ambil semua data yang
        $bahanWilayah = DB::table('tb_user')
            ->join('tb_checksheet', 'tb_user.id', '=', 'tb_checksheet.id_user', 'inner')
            ->select('*')
            ->orderBy('created_at')
            ->get()->toArray();

        $distinctWilayah = DB::table('tb_outlet')
            ->distinct('wilayah')
            ->groupBy('wilayah')
            ->get()->toArray();

        $shetnama   = array();
        $baik       = array();
        $kurang     = array();
        $perlu      = array();
        $na         = array();
        $data       = array();
        $wilayah    = array();

        foreach ($ceksheet as $item) {
            array_push($shetnama, $item->premises);
        }

        foreach ($allData as $item) {
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


        for ($i = 0; $i < sizeof($distinctWilayah); $i++) {
            $resultGroupBy = array_keys(array_keys(array_combine(array_keys($bahanWilayah), array_column($bahanWilayah, 'wilayah')), $distinctWilayah[$i]->wilayah));
            $wilayah[$distinctWilayah[$i]->wilayah] = sizeof($resultGroupBy);
        }

        $components['js_premises']  = $shetnama;
        $components['js_baik']      = $baik;
        $components['js_kurang']    = $kurang;
        $components['js_perlu']     = $perlu;
        $components['js_na']        = $na;
        $components['data']         = $data;
        $components['ceksheet']     = $ceksheet;
        $components['wilayah']      = $wilayah;
        return view('pages/menu', $components);
    }
}
