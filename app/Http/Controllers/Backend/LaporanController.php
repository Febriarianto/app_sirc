<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use Yajra\DataTables\DataTables;
use App\Traits\ResponseStatus;

class LaporanController extends Controller
{
    use ResponseStatus;

    function __construct()
    {
        $this->middleware('can:laporan-bulanan-list', ['only' => ['bulanan_index', 'bulanan_show']]);
        $this->middleware('can:laporan-referral-list', ['only' => ['referral_index', 'referral_show']]);
    }

    public function bulanan_index(Request $request)
    {
        $config['title'] = "Laporan Bulanan";
        $config['breadcrumbs'] = [
            ['url' => '#', 'title' => "Laporan Bulanan"],
        ];
        if ($request->ajax()) {
            $data = Invoice::with('penyewa')->where('id_kendaraan', $request['kendaraan'])
                ->where('keberangkatan', 'LIKE', '%' . $request['bulan'] . '%');
            return DataTables::of($data)
                ->addIndexColumn()
                ->make();
        }
        return view('backend.laporan.bulanan', compact('config'));
    }

    public function bulanan_show()
    {
    }

    public function referral_index(Request $request)
    {
        $config['title'] = "Laporan Referral";
        $config['breadcrumbs'] = [
            ['url' => '#', 'title' => "Laporan Referral"],
        ];
        if ($request->ajax()) {
            $data = Invoice::with('penyewa')
                ->whereHas('penyewa', function ($query) use ($request) {
                    return $query->where('penyewa.referral_id', '=', $request['referral']);
                })
                ->where('keberangkatan', 'LIKE', '%' . $request['bulan'] . '%');
            return DataTables::of($data)
                ->addIndexColumn()
                ->make();
        }

        return view('backend.laporan.referral', compact('config'));
    }

    public function referral_show()
    {
    }
}
