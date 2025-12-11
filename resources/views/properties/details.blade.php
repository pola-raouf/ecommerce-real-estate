<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Details - EL Kayan</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/properties-details.css') }}">
    <link rel="stylesheet" href="{{ asset('css/footer.css') }}">
</head>
@php
    $reservation = $reservation ?? $property->reservation;
    $canReserve = $canReserve ?? false;
    $canCancel = $canCancel ?? false;
    $isPending = $isPending ?? false;
    $isSold = $isSold ?? ($property->status === 'sold');
@endphp

<body>

    <!-- ================= NAVBAR ================= -->
    <nav id="mainNavbar" class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4 text-black" href="{{ url('/') }}">
                <i class="bi bi-building-fill me-1"></i> EL Kayan
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    <li class="nav-item"><a class="nav-link fw-semibold {{ Request::is('/') ? 'active' : '' }}"
                            href="{{ url('/') }}">Home</a></li>
                    <li class="nav-item"><a class="nav-link fw-semibold {{ Request::is('about-us') ? 'active' : '' }}"
                            href="{{ route('about-us') }}">About Us</a></li>
                    <li class="nav-item"><a class="nav-link fw-semibold {{ Request::is('properties') ? 'active' : '' }}"
                            href="{{ route('properties.index') }}">Properties</a></li>
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
                        <li class="nav-item"><a class="btn btn-custom btn-sm fw-bold ms-2"
                                href="{{ route('login.form') }}"><i class="bi bi-box-arrow-in-right me-1"></i> Login</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- ================= PAGE CONTENT ================= -->
    <div class="container property-details-container">

        <a href="{{ route('properties.index') }}" class="back-button">
            <i class="bi bi-arrow-left"></i> Back to Listings
        </a>

        <h1 class="property-title">{{ $property->title ?? $property->category ?? 'Property Details' }}</h1>

        <div class="row g-4">
            {{-- Multiple Images --}}
            <div class="col-lg-7 order-1">
                <div class="property-images-card">
                    <h2><i class="bi bi-images me-2"></i>Property Images</h2>
                    <div class="property-image-grid">
                        @if($property->images && $property->images->count() > 0)
                            @foreach($property->images as $index => $image)
                                <div class="property-image-item" data-image-index="{{ $index }}">
                                    <img src="{{ asset($image->image_path) }}" alt="Property Image {{ $index + 1 }}"
                                        class="property-thumbnail">
                                    <div class="image-overlay">
                                        <button class="btn-view-image" data-image-url="{{ asset($image->image_path) }}"
                                            data-image-index="{{ $index }}">
                                            <i class="bi bi-zoom-in"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        @elseif($property->image)
                            <div class="property-image-item" data-image-index="0">
                                <img src="{{ asset($property->image) }}" alt="Property Image" class="property-thumbnail">
                                <div class="image-overlay">
                                    <button class="btn-view-image" data-image-url="{{ asset($property->image) }}"
                                        data-image-index="0">
                                        <i class="bi bi-zoom-in"></i>
                                    </button>
                                </div>
                            </div>
                        @else
                            <div class="no-images">
                                <i class="bi bi-image fs-1 d-block mb-3"></i>
                                <p>No images available for this property.</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Description - appears after images on mobile --}}
                @if($property->description)
                    <div class="description-card mt-4 order-2 d-lg-none">
                        <h2><i class="bi bi-file-text me-2"></i>Description</h2>
                        <p>{!! nl2br(e($property->description)) !!}</p>
                    </div>
                @endif
            </div>

            {{-- Payment / Info --}}
            <div class="col-lg-5 order-3">
                <div class="payment-card">
                    <div class="payment-card-header">
                        <h2><i class="bi bi-credit-card me-2"></i>Payment Options</h2>
                    </div>
                    <div class="payment-card-body">
                        <div class="payment-info">
                            @if($property->installment_years > 0)
                                <p
                                    class="{{ $canReserve ? 'text-success' : ($isPending ? 'text-warning' : ($isSold ? 'text-danger' : 'text-muted')) }}">
                                    <strong>Installment Allowed:</strong>
                                    @if($canReserve)
                                        Yes
                                    @elseif($isPending)
                                        Pending Reservation
                                    @elseif($canCancel)
                                        Reserved by you
                                    @else
                                        Unavailable
                                    @endif
                                </p>
                                <p><strong>Period:</strong> {{ $property->installment_years }} Years</p>
                            @else
                                <p class="text-danger"><strong>Payment Type:</strong> Cash payment only</p>
                            @endif
                        </div>

                        @auth
                            @if($canReserve)
                                <button type="button" class="reserve-btn" data-bs-toggle="modal" 
                                        data-bs-target="#reservationModal">
                                    <i class="bi bi-check-circle me-2"></i>Reserve this Property
                                </button>
                            @elseif($canCancel)
                                <form action="{{ route('properties.cancelReservation', $property->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="reserve-btn cancel">
                                        <i class="bi bi-x-circle me-2"></i>Cancel Reservation
                                    </button>
                                </form>
                            @elseif($isPending)
                                <button type="button" class="reserve-btn pending" disabled>
                                    <i class="bi bi-hourglass-split me-2"></i>Reserved
                                </button>
                            @else
                                <button type="button" class="reserve-btn sold-out" disabled>
                                    <i class="bi bi-x-circle me-2"></i>Sold Out
                                </button>
                            @endif
                        @else
                            <a href="{{ route('login.form') }}" class="reserve-btn login-link">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Login to reserve
                            </a>
                        @endauth

                        @if($isPending && !$canCancel && $reservation)
                            <p class="text-warning mt-3 mb-0 small">
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                This property is currently reserved by another user.
                            </p>
                        @endif
                    </div>
                </div>

                {{-- Property Info Card --}}
                <div class="payment-card">
                    <div class="payment-card-header"
                        style="background: linear-gradient(135deg, rgba(13, 110, 253, 0.2) 0%, rgba(11, 94, 215, 0.2) 100%);">
                        <h2><i class="bi bi-info-circle me-2"></i>Property Information</h2>
                    </div>
                    <div class="payment-card-body">
                        @if($property->location)
                            <div class="payment-info">
                                <p><strong><i class="bi bi-geo-alt me-2"></i>Location:</strong> {{ $property->location }}
                                </p>
                            </div>
                        @endif
                        @if($property->price)
                            <div class="payment-info">
                                <p><strong><i class="bi bi-currency-dollar me-2"></i>Price:</strong>
                                    {{ number_format($property->price) }} EGP</p>
                            </div>
                        @endif
                        @if($property->status)
                            <div class="payment-info">
                                <p><strong><i class="bi bi-tag me-2"></i>Status:</strong>
                                    <span
                                        class="badge bg-{{ $property->status === 'available' ? 'success' : ($property->status === 'sold' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($property->status) }}
                                    </span>
                                </p>
                            </div>
                        @endif
                        @if($property->transaction_type)
                            <div class="payment-info">
                                <p><strong><i class="bi bi-arrow-left-right me-2"></i>Type:</strong>
                                    {{ ucfirst($property->transaction_type) }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Description - hidden on mobile (shown above), visible on desktop --}}
        @if($property->description)
            <div class="description-card mt-4 order-4 d-none d-lg-block">
                <h2><i class="bi bi-file-text me-2"></i>Description</h2>
                <p>{!! nl2br(e($property->description)) !!}</p>
            </div>
        @endif

    </div>

    {{-- RESERVATION MODAL --}}
    @auth
    <div class="modal fade" id="reservationModal" tabindex="-1" aria-labelledby="reservationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <h5 class="modal-title" id="reservationModalLabel">
                        <i class="bi bi-calendar-check me-2"></i>Reserve {{ $property->category }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <form action="{{ route('properties.reserve', $property->id) }}" method="POST">
                    @csrf
                    <div class="modal-body" style="padding: 30px;">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        
                        <div class="mb-4">
                            <h6 style="color: #667eea; font-weight: 600;">Property Details</h6>
                            <p class="mb-1"><strong>Location:</strong> {{ $property->location }}</p>
                            <p class="mb-1"><strong>Price:</strong> ${{ number_format($property->price, 2) }}</p>
                            <p class="mb-0"><strong>Type:</strong> {{ ucfirst($property->transaction_type) }}</p>
                        </div>
                        
                        <hr>
                        
                        {{-- Meeting Date/Time (Required for both rent and sale) --}}
                        <div class="mb-3">
                            <label for="meeting_datetime" class="form-label">
                                <i class="bi bi-calendar-event me-1"></i>Viewing Appointment *
                            </label>
                            <input type="datetime-local" 
                                   class="form-control @error('meeting_datetime') is-invalid @enderror" 
                                   id="meeting_datetime" 
                                   name="meeting_datetime" 
                                   value="{{ old('meeting_datetime') }}"
                                   min="{{ now()->addDay()->format('Y-m-d\TH:i') }}"
                                   required>
                            <small class="text-muted">Select when you'd like to view the property</small>
                            @error('meeting_datetime')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        @if($property->transaction_type === 'rent')
                            {{-- Rental-specific fields --}}
                            <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
                                <h6 style="color: #667eea; font-weight: 600; margin-bottom: 15px;">
                                    <i class="bi bi-house-door me-1"></i>Rental Details
                                </h6>
                                
                                <div class="mb-3">
                                    <label for="start_date" class="form-label">
                                        <i class="bi bi-calendar-check me-1"></i>Rental Start Date *
                                    </label>
                                    <input type="date" 
                                           class="form-control @error('start_date') is-invalid @enderror" 
                                           id="start_date" 
                                           name="start_date" 
                                           value="{{ old('start_date') }}"
                                           required>
                                    <small class="text-muted">When do you want to start renting?</small>
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="duration_value" class="form-label">
                                            <i class="bi bi-hourglass-split me-1"></i>Duration *
                                        </label>
                                        <input type="number" 
                                               class="form-control @error('duration_value') is-invalid @enderror" 
                                               id="duration_value" 
                                               name="duration_value" 
                                               value="{{ old('duration_value', 6) }}"
                                               min="1" 
                                               max="100"
                                               required>
                                        @error('duration_value')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="duration_unit" class="form-label">Period</label>
                                        <select class="form-select @error('duration_unit') is-invalid @enderror" 
                                                id="duration_unit" 
                                                name="duration_unit"
                                                required>
                                            <option value="weeks" {{ old('duration_unit') === 'weeks' ? 'selected' : '' }}>Weeks</option>
                                            <option value="months" {{ old('duration_unit', 'months') === 'months' ? 'selected' : '' }}>Months</option>
                                            <option value="years" {{ old('duration_unit') === 'years' ? 'selected' : '' }}>Years</option>
                                        </select>
                                        @error('duration_unit')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        {{-- Notes (Optional for both) --}}
                        <div class="mb-3">
                            <label for="notes" class="form-label">
                                <i class="bi bi-chat-left-text me-1"></i>Additional Notes (Optional)
                            </label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" 
                                      name="notes" 
                                      rows="3" 
                                      maxlength="500"
                                      placeholder="Any special requests or questions?">{{ old('notes') }}</textarea>
                            <small class="text-muted">Maximum 500 characters</small>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Note:</strong> You'll receive a confirmation email with all the details after submitting this reservation.
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                            <i class="bi bi-check-circle me-2"></i>Confirm Reservation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endauth

    {{-- FULL SCREEN IMAGE VIEWER --}}
    <div id="fullscreen-image-viewer" class="fullscreen-image-viewer">
        <div class="viewer-header">
            <span class="image-counter" id="image-counter">1 / 1</span>
            <button class="btn-close-viewer" id="close-viewer">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <button class="viewer-nav-btn viewer-nav-prev" id="prev-image-btn">
            <i class="bi bi-chevron-left"></i>
        </button>
        <div class="viewer-content">
            <img id="viewer-image" src="" alt="Property Image">
        </div>
        <button class="viewer-nav-btn viewer-nav-next" id="next-image-btn">
            <i class="bi bi-chevron-right"></i>
        </button>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const viewer = document.getElementById('fullscreen-image-viewer');
            const viewerImage = document.getElementById('viewer-image');
            const closeBtn = document.getElementById('close-viewer');
            const prevBtn = document.getElementById('prev-image-btn');
            const nextBtn = document.getElementById('next-image-btn');
            const counter = document.getElementById('image-counter');

            let currentIndex = 0;
            let allImages = [];

            // Collect all images (primary first, then gallery)
            @if($property->image)
                allImages.push('{{ asset($property->image) }}');
            @endif
            @if($property->images && $property->images->count() > 0)
                @foreach($property->images as $index => $image)
                    allImages.push('{{ asset($image->image_path) }}');
                @endforeach
            @endif

            // Open viewer
            document.querySelectorAll('.btn-view-image').forEach((btn, index) => {
                btn.addEventListener('click', function () {
                    currentIndex = parseInt(this.getAttribute('data-image-index'));
                    openViewer();
                });
            });

            function openViewer() {
                viewer.classList.add('active');
                document.body.style.overflow = 'hidden';
                updateImage();
            }

            function closeViewer() {
                viewer.classList.remove('active');
                document.body.style.overflow = '';
            }

            function updateImage() {
                if (allImages.length === 0) return;

                viewerImage.src = allImages[currentIndex];
                counter.textContent = `${currentIndex + 1} / ${allImages.length}`;

                prevBtn.style.display = currentIndex === 0 ? 'none' : 'flex';
                nextBtn.style.display = currentIndex === allImages.length - 1 ? 'none' : 'flex';
            }

            function nextImage() {
                if (currentIndex < allImages.length - 1) {
                    currentIndex++;
                    updateImage();
                }
            }

            function prevImage() {
                if (currentIndex > 0) {
                    currentIndex--;
                    updateImage();
                }
            }

            // Event listeners
            closeBtn.addEventListener('click', closeViewer);
            prevBtn.addEventListener('click', prevImage);
            nextBtn.addEventListener('click', nextImage);

            // Keyboard navigation
            document.addEventListener('keydown', function (e) {
                if (!viewer.classList.contains('active')) return;

                if (e.key === 'Escape') closeViewer();
                if (e.key === 'ArrowLeft') prevImage();
                if (e.key === 'ArrowRight') nextImage();
            });

            // Close on background click
            viewer.addEventListener('click', function (e) {
                if (e.target === viewer) closeViewer();
            });
        });
        
        // Auto-open reservation modal if there are validation errors
        @if($errors->any())
            const reservationModal = new bootstrap.Modal(document.getElementById('reservationModal'));
            reservationModal.show();
        @endif
    </script>

</body>

</html>
