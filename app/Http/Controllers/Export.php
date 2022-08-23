<?php

namespace App\Http\Controllers;

use App\Exports\SheetExport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class Export extends Controller
{
    function index()
    {
        if (isset($_POST['wilayah']) && isset($_POST['year']) && isset($_POST['month'])) {
            $wilayah = $_POST['wilayah'];
            $year    = $_POST['year'];
            $month   = $_POST['month'];

            $premises = DB::table('tb_checklist')->select('premises')->get()->toArray();
            $allPremises = sizeof($premises) - 1;
            $headings    = ['USERID', 'WILAYAH', 'CABANG', 'OUTLET', 'STATUS', 'CREATED_AT'];
            $query = "SELECT UPPER(tb_user.email), tb_user.wilayah, tb_user.cabang, tb_user.outlet, tb_checksheet.verifikasi, tb_checksheet.created_at,";
            foreach ($premises as $index => $premise) {
                $suffix = ",";
                if ($index == $allPremises) $suffix = " ";
                $query .= "(SELECT kondisi_smw FROM tb_checksheet WHERE premises = '$premise->premises' LIMIT 1) as '$index' " . $suffix;
                array_push($headings, strval($premise->premises));
            }
            $query .= " FROM tb_checksheet INNER JOIN tb_user ON tb_checksheet.id_user = tb_user.id";
            $query .= " WHERE wilayah='$wilayah' AND YEAR(created_at) = $year AND MONTH(created_at) = $month";
            $query .= " GROUP BY tb_user.email";

            $data     = DB::select(DB::raw($query)); # data yang sudah fixed
            return Excel::download(new SheetExport($data, $headings), 'users.xlsx');
        }
    }
}
