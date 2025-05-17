@extends('layouts.app')

@section('title', 'Kullanıcı Yönetimi')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Kullanıcılar</h5>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Geri Dön
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Ad</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>İlan Sayısı</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span class="badge bg-{{ $user->role === 'admin' ? 'warning' : 'primary' }}">
                                {{ $user->role === 'admin' ? 'Yönetici' : 'Kullanıcı' }}
                            </span>
                        </td>
                        <td>{{ $user->items_count }}</td>
                        <td>
                            <div class="btn-group">
                                @if($user->role === 'user')
                                <form action="{{ route('admin.updateRole', $user->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="role" value="admin">
                                    <button type="submit" class="btn btn-sm btn-warning" onclick="return confirm('Bu kullanıcının rolünü yönetici yapmak istediğinizden emin misiniz?')">
                                        <i class="fas fa-crown"></i>
                                    </button>
                                </form>
                                @else
                                <form action="{{ route('admin.updateRole', $user->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="role" value="user">
                                    <button type="submit" class="btn btn-sm btn-primary" onclick="return confirm('Bu kullanıcının rolünü normal kullanıcı yapmak istediğinizden emin misiniz?')">
                                        <i class="fas fa-user"></i>
                                    </button>
                                </form>
                                @endif
                                <form action="{{ route('admin.deleteUser', $user->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bu kullanıcıyı silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.')">
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
