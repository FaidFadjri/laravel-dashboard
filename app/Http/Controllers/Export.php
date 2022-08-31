<?php

namespace App\Http\Controllers;

use App\Exports\SheetExport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use ZipArchive;
use File;

class Export extends Controller
{
    function index()
    {
        if (isset($_POST['wilayah']) && isset($_POST['startDate']) && isset($_POST['endDate'])) {
            $wilayah   = $_POST['wilayah'];
            $startDate = $_POST['startDate'];
            $endDate   = $_POST['endDate'];

            $premises = DB::table('tb_checklist')->select('premises')->get()->toArray();

            $allPremises = sizeof($premises) - 1;
            $headings    = ['USERID', 'WILAYAH', 'CABANG', 'OUTLET', 'STATUS', 'CREATED_AT'];
            $query = "SELECT id_user, wilayah, cabang, outlet, MAX(verifikasi), DATE(created_at) as date, ";
            foreach ($premises as $index => $premise) {
                $suffix = ", ";
                if ($index == $allPremises) $suffix = " ";
                $premis = $premise->premises;
                $query .= "MAX(CASE WHEN premises = '$premis' THEN kondisi_smw END) as '$index'" . $suffix;
                array_push($headings, strval($premise->premises));
            }
            $query .= " FROM tb_checksheet JOIN tb_user ON tb_checksheet.id_user = tb_user.id";
            $query .= " WHERE verifikasi = 'closing'";

            $query .= " AND DATE(created_at) >= '$startDate'";
            $query .= " AND DATE(created_at) <= '$endDate'";

            if ($wilayah) {
                $query .= " AND wilayah = '$wilayah'";
            }
            $query .= " GROUP BY DATE(created_at), id_user"; # Group By according date and userID
            $data     = DB::select(DB::raw($query)); # data yang sudah fixed

            return Excel::download(new SheetExport($data, $headings), 'users.xlsx');
        }
    }

    //---- FIXED QUERY
    // SELECT id_user, 
    // DATE(created_at) as date,
    // MAX(CASE WHEN premises = 'Pole Sign' THEN kondisi END) as PoleSign,
    // MAX(CASE WHEN premises = 'APAR' THEN kondisi END) as APAR,
    // MAX(CASE WHEN premises = 'CCTV' THEN kondisi END) as CCTV
    // FROM tb_checksheet GROUP BY DATE(created_at), id_user;


    function toZip()
    {
        $zip = new ZipArchive;

        $fileName = 'myNewFile.zip';

        if ($zip->open(public_path($fileName), ZipArchive::CREATE) === TRUE) {
            $files = File::files(public_path('myFiles'));

            foreach ($files as $key => $value) {
                $relativeNameInZipFile = basename($value);
                $zip->addFile($value, $relativeNameInZipFile);
            }

            $zip->close();
        }

        return response()->download(public_path($fileName));
    }
}
