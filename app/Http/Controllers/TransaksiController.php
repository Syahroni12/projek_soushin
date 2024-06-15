<?php

namespace App\Http\Controllers;

use App\Models\Detailtransaksi;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;

class TransaksiController extends Controller
{
    public function pembayaran(Request $request)
    {

        $title = "Halaman Pembayaran dan Pengambilan";
        $keyword = $request->search;
        $limit = 20;
        $data = Transaksi::with('pelanggan')
        ->where('status_pesanan', 'belum')->where('status_ambil', 'belum') // atau '0', tergantung nilai yang digunakan di database
            ->where(function ($query) use ($keyword) {
                $query->where('tanggal_pesan', 'like', '%' . $keyword . '%')
                    ->orWhere('tanggal_ambil', 'like', '%' . $keyword . '%')
                   
                
                 
                    // ->orWhere('tanggal_kembali', 'like', '%' . $keyword . '%')
                    ->orWhereHas('pelanggan', function ($query) use ($keyword) {
                        $query->where('name', 'like', '%' . $keyword . '%');
                    });
            })
            ->paginate($limit);
        return view('transaksi.pembayaran', compact('title', 'data', 'keyword'));
    }
    public function pesanan_selesai(Request $request)
    {

        $title = "Halaman Transaksi Selesai";
        $keyword = $request->search;
        $limit = 20;
        $data = Transaksi::with('pelanggan')
        ->where('status_pesanan', 'selesai')->where('status_ambil', 'sudah') // atau '0', tergantung nilai yang digunakan di database
            ->where(function ($query) use ($keyword) {
                $query->where('tanggal_pesan', 'like', '%' . $keyword . '%')
                    ->orWhere('tanggal_ambil', 'like', '%' . $keyword . '%')
                   
                
                 
                    // ->orWhere('tanggal_kembali', 'like', '%' . $keyword . '%')
                    ->orWhereHas('pelanggan', function ($query) use ($keyword) {
                        $query->where('name', 'like', '%' . $keyword . '%');
                    });
            })
            ->paginate($limit);
        return view('transaksi.pesanan_selesai', compact('title', 'data', 'keyword'));
    }
    public function detail_pesanan($id) {
        $title="Detail Pesanan";
        $data=Detailtransaksi::with('barang')->where('id_transaksi', $id)->get();
        return view('transaksi.detail_pembayaran',compact('data','title'));
        
    }
    public function detail_pesananselesai($id) {
        $title="Detail Pesanan";
        $data=Detailtransaksi::with('barang')->where('id_transaksi', $id)->get();
        return view('transaksi.detail_pesananselesai',compact('data','title'));
        
    }

    public function pembayaran_proses(Request $request)
    {
        $data = Transaksi::find($request->id);
        $bayar=str_replace('.', '', $request->bayar);

        if ($bayar < $data->total_harga) {
            Alert::error('Error', 'Pembayaran Kurang')->flash();
            return back();
            # code...
        }

        // $validator = Validator::make($request->all(), [
        //     'id' => 'required|integer',
        //     // 'status_ambil' => 'required|string|max:255',
        // ]);
        // if ($validator->fails()) {
        //     $messages = $validator->errors()->all();
        //     Alert::error($messages)->flash();
        //     return back()->withErrors($validator)->withInput();
        // }
      
     
        $data->status_ambil = "sudah";
        $data->total_bayar = $bayar;
        $data->status_pesanan = "selesai";
        $data->save();
        Alert::success('Success', 'Pembayaran Berhasil')->flash();
        return redirect()->route('pembayaran_danpengambilan');
    }
}
