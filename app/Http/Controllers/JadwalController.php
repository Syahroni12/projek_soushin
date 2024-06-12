<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;

class JadwalController extends Controller
{
    public function index(Request $request)
    {
        $title = "Data Jadwal";
        $data = Jadwal::orWhere('jam_awal', 'like', '%' . $request->search . '%')->orWhere('jam_akhir', 'like', '%' . $request->search . '%')->orWhere('tanggal', 'like', '%' . $request->search . '%')->orWhere('kelas', 'like', '%' . $request->search . '%')->paginate(10);
        $offset = ($data->currentPage() - 1) * $data->perPage();
        return view('jadwal.index', compact('title', 'data', 'offset'));
    }

    public function tambah_jadwal(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kelas' => 'required|string|max:255',
            'jam_awal' => 'required',
            'jam_akhir' => 'required',
            'tanggal' => 'required|date',
        ]);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            Alert::error($messages)->flash();
            return back()->withErrors($validator)->withInput();
        }
        $tanggal = Carbon::parse($request->tanggal)->format('Y-m-d');
        $jam_awal = $this->convertTo24HourFormat($request->jam_awal);
        $jam_akhir = $this->convertTo24HourFormat($request->jam_akhir);
        $data = new Jadwal();
        $data->jam_awal = $jam_awal;
        $data->jam_akhir = $jam_akhir;
        $data->tanggal = $tanggal;
        $data->kelas = $request->kelas;
        $data->save();
        Alert::success('Success', 'Data Berhasil di tambah')->flash();
        return redirect()->route('jadwal');
    }

    public function update_jadwal(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'jam_awal' => 'required',
            'jam_akhir' => 'required',
            'tanggal' => 'required|date',
            'kelas' => 'required|string|max:255',
        ]);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            Alert::error($messages)->flash();
            return back()->withErrors($validator)->withInput();
        }
        $data = Jadwal::find($request->id);
        $data->jam_awal = $this->convertTo24HourFormat($request->jam_awal);
        $data->jam_akhir = $this->convertTo24HourFormat($request->jam_akhir);
        $data->tanggal = Carbon::parse($request->tanggal)->format('Y-m-d');
        $data->kelas = $request->kelas;
        $data->save();
        Alert::success('Success', 'Data Berhasil di update')->flash();
        return redirect()->route('jadwal');
    }

    public function hapus_jadwal($id)
    {
        $data = Jadwal::find($id);
        $data->delete();
        Alert::success('Success', 'Data Berhasil di hapus')->flash();
        return redirect()->route('jadwal');
    }

    public function convertTo24HourFormat($time)
    {
        // Pisahkan jam, menit, dan AM/PM
        list($hour, $minute, $ampm) = sscanf($time, "%d:%d %s");

        // Jika waktu di atas 12 (PM), tambahkan 12 jam
        if (strcasecmp($ampm, 'pm') == 0) {
            $hour += 12;
        }

        // Jika waktu adalah 12 AM (midnight), ubah menjadi 00 jam
        if ($hour == 12 && strcasecmp($ampm, 'am') == 0) {
            $hour = 0;
        }

        // Kembalikan waktu dalam format 24 jam
        return sprintf("%02d:%02d", $hour, $minute);
    }
}
