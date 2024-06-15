<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Keranjang;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;

class BarangController extends Controller
{
    public function index(Request $request)
    {

        $title = "Data Barang";
        if (auth()->user()->role == 'pelanggan') {
            $userId = Auth::id();
            $pelanggan=Pelanggan::where('id_user', $userId)->first();
            $jumlah_pesanan = Keranjang::where('id_pelanggan', $pelanggan->id)->count();
            $data = Barang::where('nama_produk', 'like', '%' . $request->search . '%')->orWhere('deskripsi', 'like', '%' . $request->search . '%')->orWhere('harga', 'like', '%' . $request->search . '%')->paginate(10);
            $offset = ($data->currentPage() - 1) * $data->perPage();
            return view('barang.index', compact('title', 'data', 'offset', 'jumlah_pesanan'));
        } else {
            # code...
            $data = Barang::where('nama_produk', 'like', '%' . $request->search . '%')->orWhere('deskripsi', 'like', '%' . $request->search . '%')->orWhere('harga', 'like', '%' . $request->search . '%')->paginate(10);
            $offset = ($data->currentPage() - 1) * $data->perPage();
            return view('barang.index', compact('title', 'data', 'offset'));
        }
    }

    public function tambah_barang()
    {
        $title = "Tambah Data Barang";
        return view('barang.tambah', compact('title'));
    }

    public function tambah_barang_proses(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_produk' => 'required|string|max:255',
            'deskripsi' => 'required',
            'harga' => 'required',
            'gambar' => 'required',
        ]);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            Alert::error($messages)->flash();
            return back()->withErrors($validator)->withInput();
        }
        $harga = str_replace('.', '', $request->harga);
        $data = new Barang();
        $data->nama_produk = $request->nama_produk;
        $data->deskripsi = $request->deskripsi;
        $data->harga = $harga;

        $fileName = time() . '.' . $request->file('gambar')->getClientOriginalExtension();

        $request->file('gambar')->move(public_path() . '/produk', $fileName);
        $data->gambar = $fileName;
        $data->save();
        Alert::success('Success', 'Data Berhasil di tambah')->flash();
        return redirect()->route('barang');
    }

    public function hapus_barang($id)
    {
        $data = Barang::find($id);
        $file = (public_path('/produk/' . $data->gambar));
        if (file_exists($file)) {
            @unlink($file);
        }
        $data->delete();
        Alert::success('Success', 'Data Berhasil di hapus')->flash();
        return redirect()->route('barang');
    }
    public function edit_barang($id)
    {
        $data = Barang::find($id);
        $title = "Edit Data Barang";
        return view('barang.edit', compact('title', 'data'));
    }

    public function update_barang(Request $request, $id)
    {
        if ($request->hasFile('gambar')) {
            $validator = Validator::make($request->all(), [
                // 'id' => 'required|integer',
                'nama_produk' => 'required|string|max:255',
                'deskripsi' => 'required',
                'harga' => 'required',
                'gambar' => 'required|max:2048|mimes:jpeg,jpg,png',
            ]);
        } else {
            # code...
            $validator = Validator::make($request->all(), [
                // 'id' => 'required|integer',
                'nama_produk' => 'required|string|max:255',
                'deskripsi' => 'required',
                'harga' => 'required',
                // 'gambar' => 'required',
            ]);
        }
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            Alert::error($messages)->flash();
            return back()->withErrors($validator)->withInput();
        }
        $harga = str_replace('.', '', $request->harga);
        $data = Barang::find($id);
        $data->nama_produk = $request->nama_produk;
        $data->deskripsi = $request->deskripsi;
        $data->harga = $harga;
        if ($request->hasFile('gambar')) {
            # code...
            $file = (public_path('/produk/' . $data->gambar));
            @unlink($file);

            $fileName = time() . '.' . $request->file('gambar')->getClientOriginalExtension();

            $request->file('gambar')->move(public_path() . '/produk', $fileName);
            $data->gambar = $fileName;
        }
        $data->save();
        Alert::success('Success', 'Data Berhasil di update')->flash();
        return redirect()->route('barang');
    }
}
