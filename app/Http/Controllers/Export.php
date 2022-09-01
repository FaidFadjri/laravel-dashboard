<?php

namespace App\Http\Controllers;

use App\Exports\SheetExport;
use Exception;
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

        $wilayah = 'W05';
        $outlet  = 'PANDANARAN';


        if (request()->has('wilayah')) {
            $getWilayah = request()->get('wilayah');
            if ($getWilayah) {
                $wilayah = $getWilayah;
            }
        }

        if (request()->has('outlet')) {
            $getOutlet = request()->get('outlet');
            if ($getOutlet) {
                $outlet = $getOutlet;
            }
        }
        // $cabang  = 'SEMARANG';

        //--- Get Evidence
        $evidence = DB::table('tb_evidence')->select('file_name', 'premises', 'cabang', 'outlet', 'created_at', 'email')
            ->join('tb_checksheet', 'tb_evidence.id_checksheet', '=', 'tb_checksheet.id', 'inner')
            ->join('tb_user', 'tb_checksheet.id_user', '=', 'tb_user.id', 'inner')
            ->where('tb_user.wilayah', $wilayah)
            ->where('tb_user.outlet', $outlet)
            ->whereMonth('created_at', date('m'))
            ->whereYear('created_at', date('Y'))->get()->toArray();

        $users = DB::table('tb_evidence')->select('file_name', 'premises', 'cabang', 'outlet', 'created_at', 'email')
            ->join('tb_checksheet', 'tb_evidence.id_checksheet', '=', 'tb_checksheet.id', 'inner')
            ->join('tb_user', 'tb_checksheet.id_user', '=', 'tb_user.id', 'inner')
            ->where('tb_user.wilayah', $wilayah)
            ->where('tb_user.outlet', $outlet)
            ->whereMonth('created_at', date('m'))
            ->whereYear('created_at', date('Y'))
            ->groupBy('email')
            ->get()->toArray();

        $premises = DB::table('tb_checklist')->select('*')->get()->toArray();

        //--- Create directory
        if (File::exists($wilayah)) {
            File::deleteDirectory($wilayah);
            File::makeDirectory($wilayah);
        } else {
            File::makeDirectory($wilayah);
        }

        // dd($premises);
        foreach ($users as $user) {
            File::makeDirectory($wilayah . "/" . $user->email);

            foreach ($premises as $premis) {
                if ($premis->premises != 'Tisu/Sabun') {
                    File::makeDirectory($wilayah . "/" . $user->email . "/" . $premis->premises);
                }
            }
        }

        foreach ($evidence as $eviden) {
            if ($eviden->premises != 'Tisu/Sabun') {
                $url =
                    'https://elvis-premises.online/assets/evidence/' . $eviden->file_name;
                $file_name = basename($url);

                if (file_put_contents('./' . $wilayah . "/" . $eviden->email . "/" . $eviden->premises . "/" . $file_name, file_get_contents($url))) {
                    // echo "File downloaded successfully";
                } else {
                    // echo "File downloading failed.";
                }
            }
        }


        // dd($evidence);

        //----- COMPRESS FOLDER
        $zip = new ZipArchive();
        $fileName = "evidence.zip";
        if ($zip->open(public_path($fileName), ZipArchive::CREATE) === TRUE) {

            foreach ($users as $user) {
                $zip->addEmptyDir($user->email);
                foreach ($premises as $index => $premis) {
                    if ($premis->premises != 'Tisu/Sabun') {
                        $zip->addEmptyDir($user->email . '/' . $premis->premises);
                        $files = File::files(public_path($wilayah . '/' . $user->email . "/" . $premis->premises));
                        foreach ($files as $key => $value) {
                            if (file_exists($value)) {
                                $relativeNameInZipFile = basename($value);
                                $zip->addFile($value, $wilayah . '/' . $user->email . "/" . $premis->premises . '/' . $relativeNameInZipFile);
                            }
                        }
                    }
                }
            }

            $zip->close();


            // $directories = glob("$wilayah/*", GLOB_ONLYDIR);
            // foreach ($directories as $emailDir) {

            //     $zip->addEmptyDir($emailDir); # buat direktori berdasarkan nama email di file zip
            //     $premisesDirectories = glob("$emailDir/*", GLOB_ONLYDIR);
            //     foreach ($premisesDirectories as $premisDir) {
            //         $zip->addEmptyDir($premisDir);

            //         $files = File::files(public_path($premisDir));
            //         if ($files) {
            //             foreach ($files as $key => $value) {
            //                 $relativeNameInZipFile = basename($value);
            //                 $zip->addFile($value,  $outlet . "/" . $relativeNameInZipFile);
            //             }
            //             $zip->close();
            //         }
            //     }
            // }

            // $files = File::files(public_path('W05/W05PANDANARAN/ACP'));
            // foreach ($files as $key => $value) {
            //     $relativeNameInZipFile = basename($value);
            //     $zip->addFile($value,  $outlet . "/" . $relativeNameInZipFile);
            // }
            // $zip->close();
        }

        return response()->download(public_path($fileName));
    }
}
