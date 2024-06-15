<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Detailtransaksi;
use App\Models\Keranjang;
use App\Models\Pelanggan;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
    public function detail_pesanan($id)
    {
        $title = "Detail Pesanan";
        $data = Detailtransaksi::with('barang')->where('id_transaksi', $id)->get();
        return view('transaksi.detail_pembayaran', compact('data', 'title'));
    }
    public function detail_pesananselesai($id)
    {
        $title = "Detail Pesanan";
        $data = Detailtransaksi::with('barang')->where('id_transaksi', $id)->get();
        return view('transaksi.detail_pesananselesai', compact('data', 'title'));
    }

    public function pembayaran_proses(Request $request)
    {
        $data = Transaksi::find($request->id);
        $bayar = str_replace('.', '', $request->bayar);

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

    public function halaman_keranjang()
    {
        $title = "Halaman Keranjang";
        $id_user = Auth::id(); // Asumsikan pelanggan adalah user yang sedang login
        $Pelanggan = Pelanggan::where('id_user', $id_user)->first();
        $id_pelanggan = $Pelanggan->id;
        $jumlah_pesanan = Keranjang::where('id_pelanggan',$id_pelanggan)->count();
        $data=Keranjang::with('pelanggan','barang')->where('id_pelanggan', $id_pelanggan)->get();
        return view('transaksi.halaman_keranjang',compact('title','jumlah_pesanan','data'));
    }
    public function tambah_qty($id)
    {
        $data = Keranjang::find($id);
        $barang=Barang::find($data->id_barang);

        $data->sub_total = $data->sub_total + $barang->harga;
        $data->qty = $data->qty + 1;
        $data->save();
        return redirect()->route('halaman_keranjang');
      
    }

    public function kurang_qty($id)
    {
        $data = Keranjang::find($id);
        $barang=Barang::find($data->id_barang);
        $data->sub_total = $data->sub_total - $barang->harga;
        $data->qty = $data->qty - 1;
        $data->save();
        return redirect()->route('halaman_keranjang');
    }
    public function reset_qty($id)
    {
        $data = Keranjang::find($id);
        $data->delete();
        return redirect()->route('halaman_keranjang');
    }

    public function pemesanan(Request $request){

        $id_user = Auth::id(); // Asumsikan pelanggan adalah user yang sedang login

        $Pelanggan = Pelanggan::where('id_user', $id_user)->first();

        $id_pelanggan = $Pelanggan->id;
        $validator = Validator::make($request->all(), [
            // 'id' => 'required|integer',
            'tanggal_ambil' => 'required|date', // Memastikan tanggal ambil ada dan berformat tanggal
            'total_harga' => 'required|numeric|min:0', // Memastikan total harga ada dan merupakan angka positif
            'id_keranjang.*' => 'required|exists:keranjangs,id',
        ]);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            Alert::error($messages)->flash();
            return back()->withErrors($validator)->withInput();
        }
        $tanggal_ambil = Carbon::parse($request->tanggal_ambil)->format('Y-m-d');
        $idKeranjang = $request->id_keranjang;
        $total_harga=str_replace('.', '', $request->total_harga);
        $data = new Transaksi();
        $data->id_pelanggan = $id_pelanggan;
        $data->total_harga = $total_harga;
        $data->status_ambil = "belum";
        $data->status_pesanan = "belum";
        $data->tanggal_ambil = $tanggal_ambil;
        $data->tanggal_pesan = Carbon::now()->format('Y-m-d');
        $data->save();
        // $id = $data->id;
        foreach ($idKeranjang as $id) {
            $keranjang = Keranjang::find($id);
            $detail = new DetailTransaksi();
            $detail->id_transaksi = $data->id;
            $detail->id_barang = $keranjang->id_barang;
            $detail->qty = $keranjang->qty;
            $detail->sub_total = $keranjang->sub_total;
            $detail->save();
            $keranjang->delete();
        }
        // $keranjang = $request->id_keranjang;
        // $data->save();
        Alert::success('Success', 'Pemesanan Berhasil')->flash();
        return redirect()->route('pesanan_saya');

    }


    public function pesanan_saya()
    {
        $title = "Pesanan Saya";

        $id_user = Auth::id(); // Asumsikan pelanggan adalah user yang sedang login
        $Pelanggan = Pelanggan::where('id_user', $id_user)->first();

        $id_pelanggan = $Pelanggan->id;
        $jumlah_pesanan=Keranjang::where('id_pelanggan', $id_pelanggan)->count();
        $data = Transaksi::with('pelanggan')->where('id_pelanggan', $id_pelanggan)  ->paginate(20);;
// dd($data);
        return view('transaksi.pesanan_saya', compact('title', 'data','jumlah_pesanan'));
        
    }

    public function pesanan_saya_detail($id) {

        $title = "Detail Pesanan Saya";

        $id_user = Auth::id(); // Asumsikan pelanggan adalah user yang sedang login
        $Pelanggan = Pelanggan::where('id_user', $id_user)->first();

        $id_pelanggan = $Pelanggan->id;
        $jumlah_pesanan=Keranjang::where('id_pelanggan', $id_pelanggan)->count();
        $data=Detailtransaksi::with('barang')->where('id_transaksi', $id)->get();
        return view('transaksi.pesanan_saya_detail', compact('title', 'data','jumlah_pesanan'));
        
    }
}
