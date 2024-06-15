<?php

namespace App\Http\Controllers;

use App\Models\Keranjang;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;

class KeranjangController extends Controller
{

    public function tambah_keranjang(Request $request)
    {
        auth()->user()->id;
        $pelanggan = Pelanggan::where('id_user', auth()->user()->id)->first();
        // dd($pelanggan->id);
        $validator = Validator::make($request->all(), [
            // 'id_user' => 'required',
            'id_barang' => 'required',
            'qty' => 'required',
            'subtotal_harga' => 'required',
        ]);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            Alert::error($messages)->flash();
            return back()->withErrors($validator)->withInput();
        }
        $subtotal = str_replace('.', '', $request->subtotal_harga);
        $cek = Keranjang::where('id_pelanggan', $pelanggan->id)->where('id_barang', $request->id_barang)->first();
        if ($cek) {
            $cek->update([
                'qty' => $cek->qty + $request->qty,
                'sub_total' => $cek->sub_total + $subtotal
                   
            ]);
        } else {
            $data = new Keranjang();
            $data->id_pelanggan = $pelanggan->id;
            $data->id_barang = $request->id_barang;
            $data->qty = $request->qty;
            $data->sub_total = $subtotal;
            $data->save();
        }
        Alert::success('Success', 'Data Berhasil di tambah')->flash();
        return redirect()->route('barang');
    }
}
