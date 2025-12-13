<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Properties - EL Kayan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/properties-index.css') }}">
    <link rel="stylesheet" href="{{ asset('css/footer.css') }}">
    <link rel="stylesheet" href="{{ asset('css/notifications.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>

    <!-- ================= NAVBAR ================= -->
    @include('includes.navbar', ['showNotifications' => true, 'showSettings' => false, 'showDashboard' => true])

    <!-- ================= PAGE CONTENT ================= -->
    <div class="container-fluid mt-5 pt-4">
        <!-- Page Header -->
        <div class="page-header-section mb-4">
            <div class="row align-items-center mb-3">
                <div class="col-md-8">
                    <h1 class="page-main-title">
                        <i class="bi bi-building me-2"></i>Browse Properties
                    </h1>
                    <p class="page-subtitle-text">Discover your perfect property from our curated collection</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="properties-count-badge">
                        <span class="count-number">{{ $properties->count() }}</span>
                        <span class="count-text">Properties</span>
                    </div>
                </div>
            </div>
            <!-- Status Counts -->
            <div class="row g-3">
                <div class="col-6 col-md-3">
                    <div class="status-count-card status-available">
                        <div class="status-icon">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                        <div class="status-info">
                            <span class="status-number">{{ $statusCounts['available'] ?? 0 }}</span>
                            <span class="status-label">Available</span>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="status-count-card status-sold">
                        <div class="status-icon">
                            <i class="bi bi-x-circle-fill"></i>
                        </div>
                        <div class="status-info">
                            <span class="status-number">{{ $statusCounts['sold'] ?? 0 }}</span>
                            <span class="status-label">Sold</span>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="status-count-card status-pending">
                        <div class="status-icon">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                        <div class="status-info">
                            <span class="status-number">{{ $statusCounts['pending'] ?? 0 }}</span>
                            <span class="status-label">Pending</span>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="status-count-card status-reserved">
                        <div class="status-icon">
                            <i class="bi bi-bookmark-fill"></i>
                        </div>
                        <div class="status-info">
                            <span class="status-number">{{ $statusCounts['reserved'] ?? 0 }}</span>
                            <span class="status-label">Reserved</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

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
                            <div class="property-card-modern shadow-sm h-100">
                                <div class="property-image-container">
                                    <img src="{{ $imagePath }}" class="property-main-image"
                                        alt="{{ $property->category ?? 'Property' }}">
                                    <div class="property-status-badge-always status-{{ strtolower($property->status) }}">
                                        <i class="bi bi-{{ $property->status === 'available' ? 'check-circle' : ($property->status === 'sold' ? 'x-circle' : ($property->status === 'pending' ? 'hourglass-split' : 'bookmark')) }}"></i>
                                        <span>{{ ucfirst($property->status) }}</span>
                                    </div>
                                    <div class="property-type-overlay">
                                        <i class="bi bi-{{ $property->transaction_type === 'rent' ? 'house-door' : 'house-fill' }}"></i>
                                        <span>{{ ucfirst($property->transaction_type ?? 'Sale') }}</span>
                                    </div>
                                </div>
                                <div class="property-card-content">
                                    <div class="property-title-section">
                                        <h5 class="property-name">{{ $property->category ?? 'Property' }}</h5>
                                    </div>
                                    
                                    <div class="property-details-list">
                                        <div class="detail-item">
                                            <i class="bi bi-geo-alt-fill detail-icon"></i>
                                            <span class="detail-text">{{ $property->location ?? 'Location not specified' }}</span>
                                        </div>
                                        <div class="detail-item">
                                            <i class="bi bi-tag-fill detail-icon"></i>
                                            <span class="detail-text">{{ ucfirst($property->category ?? 'N/A') }}</span>
                                        </div>
                                    </div>

                                    <div class="property-price-section">
                                        <span class="price-label-text">Price</span>
                                        <span class="price-main-value">{{ number_format($property->price ?? 0) }} EGP</span>
                                    </div>

                                    <div class="property-action-section">
                                        <a href="{{ route('properties.show', ['property' => $property->id]) }}"
                                            class="btn-view-property">
                                            <span>View Details</span>
                                            <i class="bi bi-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="empty-state-modern">
                                <div class="empty-icon-wrapper">
                                    <i class="bi bi-search"></i>
                                </div>
                                <h3 class="empty-title">No Properties Found</h3>
                                <p class="empty-message">We couldn't find any properties matching your criteria. Try adjusting your filters or check back later.</p>
                                <a href="{{ route('properties.index') }}" class="btn btn-primary btn-reset-filters">
                                    <i class="bi bi-arrow-counterclockwise me-2"></i>Reset Filters
                                </a>
                            </div>
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

    <!-- Professional Footer -->
    @include('includes.footer')

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
