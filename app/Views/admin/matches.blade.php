@extends('layouts.app')

@section('title', 'Eşleşme Yönetimi')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Eşleşmeler</h5>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Geri Dön
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Kaybeden</th>
                        <th>Bulunan</th>
                        <th>Durum</th>
                        <th>Tarih</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($matches as $match)
                    <tr>
                        <td>
                            <div>
                                <strong>{{ $match->user1_name }}</strong>
                                <br>
                                <small class="text-muted">{{ $match->i1_title }}</small>
                            </div>
                        </td>
                        <td>
                            <div>
                                <strong>{{ $match->user2_name }}</strong>
                                <br>
                                <small class="text-muted">{{ $match->i2_title }}</small>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-{{ $match->status === 'pending' ? 'warning' : ($match->status === 'confirmed' ? 'success' : 'danger') }}">
                                {{ $match->status === 'pending' ? 'Bekliyor' : ($match->status === 'confirmed' ? 'Onaylandı' : 'Reddedildi') }}
                            </span>
                        </td>
                        <td>{{ $match->created_at }}</td>
                        <td>
                            <div class="btn-group">
                                <form action="{{ route('admin.updateMatchStatus', $match->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <select name="status" onchange="this.form.submit()" class="form-select form-select-sm">
                                        <option value="pending" {{ $match->status === 'pending' ? 'selected' : '' }}>Bekliyor</option>
                                        <option value="confirmed" {{ $match->status === 'confirmed' ? 'selected' : '' }}>Onaylandı</option>
                                        <option value="rejected" {{ $match->status === 'rejected' ? 'selected' : '' }}>Reddedildi</option>
                                    </select>
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
