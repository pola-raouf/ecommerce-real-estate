<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Properties - EL Kayan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/properties-index.css') }}">
    <link rel="stylesheet" href="{{ asset('css/footer.css') }}">
    <link rel="stylesheet" href="{{ asset('css/notifications.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>

    <!-- ===========-====== NAVBAR ================= -->
    <nav id="mainNavbar" class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4" href="{{ url('/') }}">
                <i class="bi bi-building-fill me-1"></i> EL Kayan
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    <li class="nav-item">
                        <a class="nav-link fw-semibold {{ Request::is('/') ? 'active' : '' }}"
                            href="{{ url('/') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-semibold {{ Request::is('about-us') ? 'active' : '' }}"
                            href="{{ route('about-us') }}">About Us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-semibold {{ Request::is('properties') ? 'active' : '' }}"
                            href="{{ route('properties.index') }}">Properties</a>
                    </li>
                    @auth
                        @if(in_array(auth()->user()->role, ['admin', 'seller']))
                            <li class="nav-item"><a class="nav-link fw-semibold {{ Request::is('dashboard') ? 'active' : '' }}"
                                    href="{{ route('dashboard') }}">Dashboard</a></li>
                        @endif
                    @endauth
                    @auth
                        {{-- Notification Bell --}}
                        <li class="nav-item dropdown position-relative">
                            <a class="nav-link notification-bell" href="#" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false" id="notificationBell">
                                <i class="bi bi-bell-fill"></i>
                                <span class="notification-badge" id="notificationBadge" style="display: none;">0</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end notification-dropdown-menu" style="width: 350px;">
                                <li class="notification-dropdown-header">
                                    <h6>Notifications</h6>
                                    <small class="text-muted" id="notificationCount">0 new</small>
                                </li>
                                <div id="notificationList" style="max-height: 300px; overflow-y: auto;">
                                    <li class="text-center py-3 text-muted">
                                        <i class="bi bi-bell-slash"></i>
                                        <p class="mb-0 small">No new notifications</p>
                                    </li>
                                </div>
                                <li class="notification-dropdown-footer">
                                    <a href="{{ route('notifications.index') }}">View All Notifications</a>
                                </li>
                            </ul>
                        </li>
                        
                        {{-- Profile Dropdown --}}
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button"
                                data-bs-toggle="dropdown">
                                <img src="{{ Auth::user()->profile_image_url }}" alt="{{ Auth::user()->name }}"
                                    class="rounded-circle profile-img me-2">
                                <span>{{ Auth::user()->name }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item d-flex align-items-center" href="{{ route('profile') }}"><i
                                            class="bi bi-person-circle me-2"></i>Profile</a></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item d-flex align-items-center"><i
                                                class="bi bi-box-arrow-right me-2"></i>Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="btn btn-custom btn-sm fw-bold ms-2" href="{{ route('login.form') }}">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Login
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- ================= PAGE CONTENT ================= -->
    <div class="container-fluid mt-5 pt-4">

        <!-- Mobile Filter Toggle Button -->
        <div class="d-lg-none mb-3">
            <button class="btn btn-primary w-100 filter-toggle-btn" type="button" data-bs-toggle="collapse"
                data-bs-target="#filterSidebar" aria-expanded="false" aria-controls="filterSidebar">
                <i class="bi bi-funnel-fill me-2"></i>
                <span>Show Filters</span>
                <i class="bi bi-chevron-down ms-auto toggle-icon"></i>
            </button>
        </div>

        <div class="row g-4">

            <!-- Main Content Area - Properties -->
            <div class="col-lg-9 order-2 order-lg-1">
                {{-- ================= PROPERTY CARDS ================= --}}
                <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4" id="properties-list">
                    @forelse($properties as $property)
                        @php
                            $imagePath = $property->image && file_exists(public_path($property->image))
                                ? asset($property->image)
                                : asset('images/properties/placeholder.jpg');

                        @endphp
                        <div class="col" data-id="{{ $property->id }}">
                            <div class="card shadow-sm h-100">
                                <img src="{{ $imagePath }}" class="card-img-top object-fit-cover"
                                    alt="{{ $property->category ?? 'Property' }}">
                                <div class="card-body d-flex flex-column">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h5 class="card-title mb-0">{{ $property->category }}</h5>
                                        <span class="badge bg-secondary">ID: {{ $property->id }}</span>
                                    </div>
                                    <p class="card-text mb-1"><span class="text-info fw-bold">Status:</span>
                                        {{ $property->status }}</p>
                                    <p class="card-text mb-1"><span class="text-info fw-bold">Location:</span>
                                        {{ $property->location }}</p>
                                    <p class="card-text mb-1"><span class="text-info fw-bold">Type:</span>
                                        {{ ucfirst($property->transaction_type ?? 'N/A') }}</p>
                                    <p class="card-text mb-3"><span class="text-success fw-bold">Price:</span>
                                        {{ number_format($property->price) }} EGP</p>
                                    <div class="mt-auto">
                                        <a href="{{ route('properties.show', ['property' => $property->id]) }}"
                                            class="btn btn-primary w-100">
                                            <i class="fas fa-info-circle me-1"></i> View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <p class="text-center text-muted fs-5">No properties found matching your criteria.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Sidebar - Filter Container -->
            <div class="col-lg-3 order-1 order-lg-2">
                <div class="filter-sidebar collapse d-lg-block" id="filterSidebar">
                    <div class="filter-header">
                        <h5 class="mb-0">
                            <i class="bi bi-funnel-fill me-2"></i>Filter Properties
                        </h5>
                    </div>

                    <form method="GET" action="{{ route('properties.index') }}" data-bs-theme="light">
                        <div class="filter-body">

                            <!-- Transaction Type -->
                            <div class="filter-group">
                                <label for="transaction_type" class="form-label">Transaction Type</label>
                                <select id="transaction_type" name="transaction_type" class="form-select">
                                    <option value="">All</option>
                                    <option value="sale" @selected(request('transaction_type') === 'sale')>For Sale
                                    </option>
                                    <option value="rent" @selected(request('transaction_type') === 'rent')>For Rent
                                    </option>
                                </select>
                            </div>

                            <!-- Search Term -->
                            <div class="filter-group">
                                <label for="search_term" class="form-label">Search</label>
                                <input type="text" id="search_term" name="search_term" class="form-control"
                                    value="{{ request('search_term') }}" placeholder="Category or Location">
                            </div>

                            <!-- Category -->
                            <div class="filter-group">
                                <label for="category" class="form-label">Category</label>
                                <select id="category" name="category" class="form-select">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat }}" @selected(request('category') === $cat)>{{ $cat }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Location -->
                            <div class="filter-group">
                                <label for="location" class="form-label">Location</label>
                                <select id="location" name="location" class="form-select">
                                    <option value="">All Locations</option>
                                    @foreach($locations as $loc)
                                        <option value="{{ $loc }}" @selected(request('location') === $loc)>{{ $loc }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Price Range -->
                            <div class="filter-group">
                                <label class="form-label">Price Range (EGP)</label>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <input type="number" id="min_price" name="min_price" class="form-control"
                                            value="{{ request('min_price') }}" placeholder="Min" min="0">
                                    </div>
                                    <div class="col-6">
                                        <input type="number" id="max_price" name="max_price" class="form-control"
                                            value="{{ request('max_price') }}" placeholder="Max" min="0">
                                    </div>
                                </div>
                            </div>

                            <!-- Sort -->
                            <div class="filter-group">
                                <label for="sort_by" class="form-label">Sort By</label>
                                <select id="sort_by" name="sort_by" class="form-select">
                                    <option value="id DESC" @selected(request('sort_by') === 'id DESC')>Latest</option>
                                    <option value="price ASC" @selected(request('sort_by') === 'price ASC')>Price: Low to
                                        High</option>
                                    <option value="price DESC" @selected(request('sort_by') === 'price DESC')>Price: High
                                        to Low</option>
                                </select>
                            </div>

                        </div>

                        <!-- Filter Actions -->
                        <div class="filter-actions">
                            <button type="submit" class="btn btn-primary w-100 mb-2">
                                <i class="bi bi-search me-1"></i> Apply Filters
                            </button>
                            <a href="{{ route('properties.index') }}" class="btn btn-outline-secondary w-100">
                                <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script>
        // Filter Toggle Button Text Change
        document.addEventListener('DOMContentLoaded', function () {
            const filterSidebar = document.getElementById('filterSidebar');
            const filterToggleBtn = document.querySelector('.filter-toggle-btn');

            if (filterSidebar && filterToggleBtn) {
                filterSidebar.addEventListener('show.bs.collapse', function () {
                    filterToggleBtn.querySelector('span').textContent = 'Hide Filters';
                    filterToggleBtn.querySelector('.toggle-icon').classList.remove('bi-chevron-down');
                    filterToggleBtn.querySelector('.toggle-icon').classList.add('bi-chevron-up');
                });

                filterSidebar.addEventListener('hide.bs.collapse', function () {
                    filterToggleBtn.querySelector('span').textContent = 'Show Filters';
                    filterToggleBtn.querySelector('.toggle-icon').classList.remove('bi-chevron-up');
                    filterToggleBtn.querySelector('.toggle-icon').classList.add('bi-chevron-down');
                });
            }
        });
    </script>

</body>

</html>
