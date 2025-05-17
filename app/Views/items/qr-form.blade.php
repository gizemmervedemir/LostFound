@extends('layouts.app')

@section('title', 'QR Kodu Oluştur')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">QR Kodu Oluştur</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('qr.generate', $item->id) }}">
                        @csrf
                        
                        <div class="form-group">
                            <label for="title">Başlık</label>
                            <input type="text" class="form-control" id="title" name="title" value="{{ $item->title }}" readonly>
                        </div>

                        <div class="form-group">
                            <label for="type">Tür</label>
                            <input type="text" class="form-control" id="type" name="type" value="{{ $item->type === 'lost' ? 'Kaybedilen' : 'Bulunan' }}" readonly>
                        </div>

                        <div class="form-group">
                            <label for="description">Açıklama</label>
                            <textarea class="form-control" id="description" name="description" rows="3" readonly>{{ $item->description }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="location">Konum</label>
                            <input type="text" class="form-control" id="location" name="location" value="{{ $item->location }}" readonly>
                        </div>

                        <button type="submit" class="btn btn-primary">QR Kodu Oluştur</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
