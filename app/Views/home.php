<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kaybedilen & Bulunan Eşya Platformu</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="/">Kaybedilen & Bulunan</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/items">Eşyalar</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/items/create">Eşya Kaydet</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    @if(session('user_id'))
                        <li class="nav-item">
                            <a class="nav-link" href="/profile">Profil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/logout">Çıkış</a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="/login">Giriş</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/register">Kayıt</a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-5">
        <div class="row">
            <!-- Search Section -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Eşya Ara</h4>
                        <form id="searchForm" class="needs-validation" novalidate>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <input type="text" class="form-control" placeholder="Başlık">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <input type="text" class="form-control" placeholder="Konum">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <select class="form-select">
                                        <option value="">Tümü</option>
                                        <option value="lost">Kaybedilen</option>
                                        <option value="found">Bulunan</option>
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Ara</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Featured Items -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Öne Çıkan Eşyalar</h4>
                        <div id="featuredItems" class="list-group"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Items -->
        <div class="row mt-4" id="itemsList">
            <!-- Items will be loaded here via AJAX -->
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-light mt-5">
        <div class="container py-4">
            <div class="row">
                <div class="col-md-6">
                    <h5>Kaybedilen & Bulunan</h5>
                    <p>İTÜ Campus'ta kaybedilen ve bulunan eşyaları kolayca bulabilirsiniz.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p>&copy; 2025 Kaybedilen & Bulunan. Tüm hakları saklıdır.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="{{ asset('js/main.js') }}"></script>
</body>
</html>
