@extends('layouts.app')

@section('title', $item->title)

@section('content')
<div class="container mt-5">
    <div class="row">
        <!-- Item Details -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="card-title">{{ $item->title }}</h2>
                        @if($item->type === 'lost')
                            <a href="{{ route('qr.form', $item->id) }}" class="btn btn-primary">
                                <i class="fas fa-qrcode"></i> QR Kodu Oluştur
                            </a>
                        @endif
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tür</label>
                                <p class="form-control-plaintext">
                                    {{ $item->type === 'lost' ? 'Kaybedilen' : 'Bulunan' }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Konum</label>
                                <p class="form-control-plaintext">{{ $item->location }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Açıklama</label>
                        <p class="form-control-plaintext">{{ $item->description }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Eklenme Tarihi</label>
                        <p class="form-control-plaintext">{{ $item->created_at->format('d.m.Y H:i') }}</p>
                    </div>

                    @if($item->images)
                    <div class="mb-3">
                        <label class="form-label">Resimler</label>
                        <div class="row">
                            @foreach($item->images as $image)
                            <div class="col-md-4 mb-3">
                                <img src="{{ asset('uploads/items/' . $image) }}" 
                                     class="img-thumbnail" 
                                     alt="{{ $item->title }}">
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if($item->status === 'matched')
                    <div class="alert alert-success">
                        <h4 class="alert-heading">Eşya Eşleşti!</h4>
                        <p>Bu eşya başka bir kullanıcı ile eşleşti.</p>
                        <hr>
                        <p class="mb-0">Lütfen eşleşen kullanıcı ile iletişime geçin.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Chat Section -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Sohbet</h5>
                </div>
                <div class="card-body" id="chat-container">
                    <div id="chat-messages" class="mb-3" style="height: 300px; overflow-y: auto;">
                        <!-- Messages will be added here -->
                    </div>
                    <div class="input-group">
                        <input type="text" 
                               id="message-input" 
                               class="form-control" 
                               placeholder="Mesaj yazın...">
                        <button class="btn btn-primary" id="send-message">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Matches Section -->
    @if($matches)
    <div class="card mt-5">
        <div class="card-header">
            <h5 class="card-title mb-0">Eşleşen Eşyalar</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Başlık</th>
                            <th>Tür</th>
                            <th>Konum</th>
                            <th>Durum</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($matches as $match)
                        <tr>
                            <td>{{ $match->title }}</td>
                            <td>{{ $match->type === 'lost' ? 'Kaybedilen' : 'Bulunan' }}</td>
                            <td>{{ $match->location }}</td>
                            <td>
                                <span class="badge bg-{{ $match->status === 'pending' ? 'warning' : 'success' }}">
                                    {{ $match->status === 'pending' ? 'Beklemede' : 'Eşleşti' }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/main.js') }}"></script>
<script>
    // Initialize chat
    initializeChat();
</script>
@endsection
