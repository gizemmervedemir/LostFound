@extends('layouts.app')

@section('title', 'İlan Yönetimi')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">İlanlar</h5>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Geri Dön
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Başlık</th>
                        <th>Tür</th>
                        <th>Kullanıcı</th>
                        <th>Durum</th>
                        <th>Tarih</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                    <tr>
                        <td>{{ $item->title }}</td>
                        <td>
                            <span class="badge bg-{{ $item->type === 'lost' ? 'danger' : 'success' }}">
                                {{ $item->type === 'lost' ? 'Kaybedilen' : 'Bulunan' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('profile', $item->user_id) }}" class="text-decoration-none">
                                {{ $item->user->name }}
                            </a>
                        </td>
                        <td>
                            <span class="badge bg-{{ $item->status === 'pending' ? 'warning' : ($item->status === 'matched' ? 'success' : 'info') }}">
                                {{ $item->status === 'pending' ? 'Bekliyor' : ($item->status === 'matched' ? 'Eşleşti' : 'Talep Edildi') }}
                            </span>
                        </td>
                        <td>{{ $item->created_at }}</td>
                        <td>
                            <div class="btn-group">
                                <form action="{{ route('admin.updateItemStatus', $item->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <select name="status" onchange="this.form.submit()" class="form-select form-select-sm">
                                        <option value="pending" {{ $item->status === 'pending' ? 'selected' : '' }}>Bekliyor</option>
                                        <option value="matched" {{ $item->status === 'matched' ? 'selected' : '' }}>Eşleşti</option>
                                        <option value="claimed" {{ $item->status === 'claimed' ? 'selected' : '' }}>Talep Edildi</option>
                                    </select>
                                </form>
                                <form action="{{ route('admin.deleteItem', $item->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bu ilanı silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
