@extends('templating.main')
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="col-lg-6 mb-3">
                        <form action="" method="GET" class="form-inline ml-2" id="searchForm">
                            <div class="input-group">
                                <input type="text" class="form-control" name="search" id="searchInput"
                                    value="{{ Request()->search }}" placeholder="Cari Data Barang..."
                                    oninput="searchOnChange()">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="submit">Cari</button>
                                    <a href="{{ route('barang') }}" class="btn btn-success">refresh</a>
                                </div>
                            </div>
                        </form>
                    </div>


                    <a href="{{ route('tambah_barang') }}" class="btn btn-info">Tambah Barang</a>
                    <div class="row">
                        @foreach ($data as $item)
                            <div class="card mb-3" style="width: 18rem;">
                                <img src="{{ asset('produk/' . $item->gambar) }}" class="card-img-top" alt="...">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $item->nama_produk }} </h5>
                                    <h6>Harga : {{ number_format($item->harga) }}</h6>
                                    <p class="card-text">{{ $item->deskripsi }}</p>
                                    <a href="{{ route('edit_barang', ['id' => $item->id]) }}" class="btn btn-warning">Edit</a> <button type="button"
                                        class="btn btn-danger delete-btn" onclick="deleteData({{ $item->id }})">
                                        <i class="fa-solid fa-trash"></i>
                                        {{--  --}}
                                    </button>
                                </div>
                            </div>
                        @endforeach

                    </div>
                    {{ $data->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>

    <script>
         function deleteData(id) {
         
         // const itemId = document.getElementById("soal");
         Swal.fire({
             title: "Apakah Kamu Yakin?",
             text: "Apakah Kamu Yakin Ingin Menghapus Data?",
             icon: "warning",
             showCancelButton: true,
             confirmButtonColor: "#3085d6",
             cancelButtonColor: "#d33",
             cancelButtonText: "Batal",
             confirmButtonText: "Ya"
         }).then((result) => {
             if (result.isConfirmed) {
                 // console.log(id);
                 window.location.href = `/hapus_barang/${id}`;
                 // window.location.href = "/selesaikan/".itemId "";
                 // Swal.fire({
                 //     title: "Deleted!",
                 //     text: "Your file has been deleted.",
                 //     icon: "success"
                 // });
             }
         });
     }
    </script>
@endsection
