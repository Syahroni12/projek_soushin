@extends('templating.main')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <a href="{{ route('ipen') }}" class="btn btn-danger mb-4">Kembali</a>
                <h5>Detail</h1>

                    <img src="{{ asset('event/' . $data->image_event) }}" class="img-fluid text-center" alt="...">
                    <h1 class="text-center">  {{ $data->title }}</h1>
                <p>{{ $data->description }}</p>
            </div>
        </div>
    </div>
</div>
@endsection