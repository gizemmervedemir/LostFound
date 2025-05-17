@extends('layouts.app')

@section('title', 'Ana Sayfa')

@section('content')
<div class="container mt-5">
    <!-- Hero Section -->
    <div class="text-center mb-5">
        <h1 class="display-4">Kaybedilen ve Bulunan Eşya Platformu</h1>
        <p class="lead">Eşyalarınızı kolayca bulmak için burada!</p>
        <div class="mt-4">
            <a href="/items/create" class="btn btn-primary btn-lg">Eşya Kaydet</a>
            <a href="/items" class="btn btn-secondary btn-lg">Eşyaları Göster</a>
        </div>
    </div>

    <!-- Search Section -->
    <div class="card mb-5">
        <div class="card-body">
            <h3 class="card-title mb-4">Eşya Ara</h3>
            <form class="needs-validation" novalidate>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="title">Başlık</label>
                            <input type="text" class="form-control" id="title" placeholder="Eşya başlığı...">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="location">Konum</label>
                            <input type="text" class="form-control" id="location" placeholder="Konum...">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="type">Tür</label>
                            <select class="form-control" id="type">
                                <option value="">Tümü</option>
                                <option value="lost">Kaybedilen</option>
                                <option value="found">Bulunan</option>
                            </select>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Ara</button>
            </form>
        </div>
    </div>

    <!-- Featured Items -->
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">Öne Çıkan Eşyalar</h2>
        </div>
    </div>

    <!-- Lost Items -->
    <div class="row mb-5">
        <div class="col-12">
            <h3 class="mb-4">Kaybedilen Eşyalar</h3>
        </div>
        @foreach($lostItems as $item)
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ $item->title }}</h5>
                    <p class="card-text">{{ Str::limit($item->description, 100) }}</p>
                    <p class="card-text"><small class="text-muted">{{ $item->location }}</small></p>
                    <a href="/items/{{ $item->id }}" class="btn btn-primary">Detaylar</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Found Items -->
    <div class="row">
        <div class="col-12">
            <h3 class="mb-4">Bulunan Eşyalar</h3>
        </div>
        @foreach($foundItems as $item)
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ $item->title }}</h5>
                    <p class="card-text">{{ Str::limit($item->description, 100) }}</p>
                    <p class="card-text"><small class="text-muted">{{ $item->location }}</small></p>
                    <a href="/items/{{ $item->id }}" class="btn btn-primary">Detaylar</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/main.js') }}"></script>
<script>
    // Initialize search functionality
    liveSearch();
</script>
@endsection
