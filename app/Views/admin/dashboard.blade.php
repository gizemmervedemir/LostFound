@extends('layouts.app')

@section('title', 'Yönetim Paneli')

@section('content')
<div class="row">
    <!-- Statistics Cards -->
    <div class="col-md-3 mb-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h5 class="card-title">Toplam İlan</h5>
                <p class="card-text display-4">{{ $stats['total_items'] }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5 class="card-title">Toplam Kullanıcı</h5>
                <p class="card-text display-4">{{ $stats['total_users'] }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card bg-warning text-dark">
            <div class="card-body">
                <h5 class="card-title">Bekleyen İlanlar</h5>
                <p class="card-text display-4">{{ $stats['pending_items'] }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h5 class="card-title">Eşleşen İlanlar</h5>
                <p class="card-text display-4">{{ $stats['matched_items'] }}</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Items -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Son İlanlar</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Başlık</th>
                                <th>Tür</th>
                                <th>Durum</th>
                                <th>Tarih</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stats['recent_items'] as $item)
                            <tr>
                                <td>{{ $item->title }}</td>
                                <td>
                                    <span class="badge bg-{{ $item->type === 'lost' ? 'danger' : 'success' }}">
                                        {{ $item->type === 'lost' ? 'Kaybedilen' : 'Bulunan' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $item->status === 'pending' ? 'warning' : ($item->status === 'matched' ? 'success' : 'info') }}">
                                        {{ $item->status === 'pending' ? 'Bekliyor' : ($item->status === 'matched' ? 'Eşleşti' : 'Talep Edildi') }}
                                    </span>
                                </td>
                                <td>{{ $item->created_at }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Hızlı Eylemler</h5>
            </div>
            <div class="card-body">
                <div class="list-group">
                    <a href="{{ route('admin.users') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-users"></i> Kullanıcı Yönetimi
                    </a>
                    <a href="{{ route('admin.items') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-box"></i> İlan Yönetimi
                    </a>
                    <a href="{{ route('admin.matches') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-exchange-alt"></i> Eşleşme Yönetimi
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
