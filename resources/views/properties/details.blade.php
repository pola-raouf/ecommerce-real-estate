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
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
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
    @include('includes.navbar', ['showNotifications' => true, 'showSettings' => false, 'showDashboard' => true])

    <!-- ================= PAGE CONTENT ================= -->
    <div class="container property-details-container">
        <a href="{{ route('properties.index') }}" class="back-button">
            <i class="bi bi-arrow-left"></i> Back to Listings
        </a>

        <div class="property-hero">
            <div class="row align-items-center gy-3">
                <div class="col-lg-8">
                    <p class="eyebrow mb-2">{{ ucfirst($property->transaction_type ?? 'Property') }}</p>
                    <h1 class="property-title mb-3">{{ $property->title ?? $property->category ?? 'Property Details' }}</h1>
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        @if($property->location)
                            <span class="meta-chip"><i class="bi bi-geo-alt me-1"></i>{{ $property->location }}</span>
                        @endif
                        @php
                            $status = $property->status ?? 'available';
                            $statusClass = $status === 'available' ? 'success' : ($status === 'sold' ? 'danger' : 'warning');
                        @endphp
                        <span class="meta-chip badge-soft-{{ $statusClass }}"><i class="bi bi-tag me-1"></i>{{ ucfirst($status) }}</span>
                        <span class="meta-chip"><i class="bi bi-arrow-left-right me-1"></i>{{ ucfirst($property->transaction_type ?? 'listing') }}</span>
                        @if(($property->installment_years ?? 0) > 0)
                            <span class="meta-chip success"><i class="bi bi-credit-card-2-back me-1"></i>Installments: {{ $property->installment_years }} years</span>
                        @else
                            <span class="meta-chip neutral"><i class="bi bi-cash-coin me-1"></i>Cash only</span>
                        @endif
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="price-block">
                        <p class="price-label mb-1">Listed price</p>
                        <div class="price-value mb-2">{{ $property->price ? number_format($property->price) . ' EGP' : 'Contact for price' }}</div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="pill-soft"><i class="bi bi-shield-check me-1"></i>Verified listing</span>
                            @if($isSold)
                                <span class="pill-soft danger"><i class="bi bi-x-circle me-1"></i>Sold out</span>
                            @elseif($isPending)
                                <span class="pill-soft warning"><i class="bi bi-hourglass-split me-1"></i>Reserved</span>
                            @else
                                <span class="pill-soft success"><i class="bi bi-check2-circle me-1"></i>Available</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            {{-- Multiple Images --}}
            <div class="col-lg-7 order-1">
                <div class="property-images-card">
                    <div class="section-header">
                        <div class="section-icon-wrapper">
                            <i class="bi bi-images"></i>
                        </div>
                        <div>
                            <h2 class="section-title">Property Gallery</h2>
                            <p class="section-subtitle">{{ ($property->images && $property->images->count() > 0) ? $property->images->count() : ($property->image ? 1 : 0) }} {{ ($property->images && $property->images->count() > 0) ? 'photos' : 'photo' }} available</p>
                        </div>
                    </div>
                    <div class="property-image-grid">
                        @if($property->images && $property->images->count() > 0)
                            @foreach($property->images as $index => $image)
                                <div class="property-image-item" data-image-index="{{ $index }}">
                                    <img src="{{ asset($image->image_path) }}" alt="Property Image {{ $index + 1 }}"
                                        class="property-thumbnail">
                                    <div class="image-overlay">
                                        <div class="overlay-content">
                                            <button class="btn-view-image" data-image-url="{{ asset($image->image_path) }}"
                                                data-image-index="{{ $index }}">
                                                <i class="bi bi-zoom-in"></i>
                                                <span class="btn-label">View</span>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="image-number">{{ $index + 1 }}</div>
                                </div>
                            @endforeach
                        @elseif($property->image)
                            <div class="property-image-item" data-image-index="0">
                                <img src="{{ asset($property->image) }}" alt="Property Image" class="property-thumbnail">
                                <div class="image-overlay">
                                    <div class="overlay-content">
                                        <button class="btn-view-image" data-image-url="{{ asset($property->image) }}"
                                            data-image-index="0">
                                            <i class="bi bi-zoom-in"></i>
                                            <span class="btn-label">View</span>
                                        </button>
                                    </div>
                                </div>
                                <div class="image-number">1</div>
                            </div>
                        @else
                            <div class="no-images">
                                <div class="no-images-icon">
                                    <i class="bi bi-image"></i>
                                </div>
                                <h3>No Images Available</h3>
                                <p>Images for this property will be added soon.</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Description - appears after images on mobile --}}
                @if($property->description)
                    <div class="description-card mt-4 order-2 d-lg-none">
                        <div class="section-header">
                            <div class="section-icon-wrapper description-icon">
                                <i class="bi bi-file-text"></i>
                            </div>
                            <div>
                                <h2 class="section-title">Property Description</h2>
                                <p class="section-subtitle">Detailed information</p>
                            </div>
                        </div>
                        <div class="description-content">
                            <p>{!! nl2br(e($property->description)) !!}</p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Payment / Info --}}
            <div class="col-lg-5 order-3">
                <div class="payment-card">
                    <div class="section-header">
                        <div class="section-icon-wrapper payment-icon">
                            <i class="bi bi-credit-card-2-front"></i>
                        </div>
                        <div>
                            <h2 class="section-title">Payment & Reservation</h2>
                            <p class="section-subtitle">Secure booking options</p>
                        </div>
                    </div>
                    <div class="payment-card-body">
                        <div class="payment-details">
                            @if($property->installment_years > 0)
                                <div class="info-row">
                                    <div class="info-label">
                                        <i class="bi bi-check-circle-fill text-success"></i>
                                        <span>Installment Plan</span>
                                    </div>
                                    <div class="info-value">
                                        <span class="badge-status {{ $canReserve ? 'available' : ($isPending ? 'pending' : ($isSold ? 'sold' : 'unavailable')) }}">
                                            @if($canReserve)
                                                Available
                                            @elseif($isPending)
                                                Pending
                                            @elseif($canCancel)
                                                Reserved by you
                                            @else
                                                Unavailable
                                            @endif
                                        </span>
                                    </div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">
                                        <i class="bi bi-calendar-range"></i>
                                        <span>Payment Period</span>
                                    </div>
                                    <div class="info-value">
                                        <strong>{{ $property->installment_years }} Years</strong>
                                    </div>
                                </div>
                            @else
                                <div class="info-row">
                                    <div class="info-label">
                                        <i class="bi bi-cash-coin text-primary"></i>
                                        <span>Payment Method</span>
                                    </div>
                                    <div class="info-value">
                                        <span class="badge-status neutral">Cash Only</span>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="reservation-section">
                            @auth
                                @if($canReserve)
                                    <button type="button" class="reserve-btn" data-bs-toggle="modal" 
                                            data-bs-target="#reservationModal">
                                        <i class="bi bi-check-circle"></i>
                                        <span>Reserve this Property</span>
                                    </button>
                                @elseif($canCancel)
                                    <form action="{{ route('properties.cancelReservation', $property->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="reserve-btn cancel">
                                            <i class="bi bi-x-circle"></i>
                                            <span>Cancel Reservation</span>
                                        </button>
                                    </form>
                                @elseif($property->status === 'pending')
                                    <button type="button" class="reserve-btn pending" disabled>
                                        <i class="bi bi-hourglass-split"></i>
                                        <span>Pending Approval</span>
                                    </button>
                                @elseif($isPending)
                                    <button type="button" class="reserve-btn pending" disabled>
                                        <i class="bi bi-hourglass-split"></i>
                                        <span>Currently Reserved</span>
                                    </button>
                                @else
                                    <button type="button" class="reserve-btn sold-out" disabled>
                                        <i class="bi bi-x-circle"></i>
                                        <span>Sold Out</span>
                                    </button>
                                @endif
                            @else
                                <a href="{{ route('login.form') }}" class="reserve-btn login-link">
                                    <i class="bi bi-box-arrow-in-right"></i>
                                    <span>Login to Reserve</span>
                                </a>
                            @endauth

                            @if($isPending && !$canCancel && $reservation)
                                <div class="reservation-notice">
                                    <i class="bi bi-info-circle"></i>
                                    <span>This property is currently reserved by another user.</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Property Info Card --}}
                <div class="payment-card info-card">
                    <div class="section-header">
                        <div class="section-icon-wrapper info-icon">
                            <i class="bi bi-info-circle"></i>
                        </div>
                        <div>
                            <h2 class="section-title">Property Details</h2>
                            <p class="section-subtitle">Key information</p>
                        </div>
                    </div>
                    <div class="payment-card-body">
                        <div class="info-list">
                            @if($property->location)
                                <div class="info-item">
                                    <div class="info-icon-wrapper">
                                        <i class="bi bi-geo-alt-fill"></i>
                                    </div>
                                    <div class="info-content">
                                        <span class="info-label">Location</span>
                                        <span class="info-value">{{ $property->location }}</span>
                                    </div>
                                </div>
                            @endif
                            @if($property->price)
                                <div class="info-item">
                                    <div class="info-icon-wrapper">
                                        <i class="bi bi-currency-dollar"></i>
                                    </div>
                                    <div class="info-content">
                                        <span class="info-label">Price</span>
                                        <span class="info-value price-value">{{ number_format($property->price) }} EGP</span>
                                    </div>
                                </div>
                            @endif
                            @if($property->status)
                                <div class="info-item">
                                    <div class="info-icon-wrapper">
                                        <i class="bi bi-tag-fill"></i>
                                    </div>
                                    <div class="info-content">
                                        <span class="info-label">Status</span>
                                        <span class="info-value">
                                            <span class="status-badge status-{{ $property->status }}">
                                                {{ ucfirst($property->status) }}
                                            </span>
                                        </span>
                                    </div>
                                </div>
                            @endif
                            @if($property->transaction_type)
                                <div class="info-item">
                                    <div class="info-icon-wrapper">
                                        <i class="bi bi-arrow-left-right"></i>
                                    </div>
                                    <div class="info-content">
                                        <span class="info-label">Transaction Type</span>
                                        <span class="info-value">{{ ucfirst($property->transaction_type) }}</span>
                                    </div>
                                </div>
                            @endif
                            @if($property->category)
                                <div class="info-item">
                                    <div class="info-icon-wrapper">
                                        <i class="bi bi-building"></i>
                                    </div>
                                    <div class="info-content">
                                        <span class="info-label">Category</span>
                                        <span class="info-value">{{ ucfirst($property->category) }}</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Description - hidden on mobile (shown above), visible on desktop --}}
        @if($property->description)
            <div class="description-card mt-4 order-4 d-none d-lg-block">
                <div class="section-header">
                    <div class="section-icon-wrapper description-icon">
                        <i class="bi bi-file-text"></i>
                    </div>
                    <div>
                        <h2 class="section-title">Property Description</h2>
                        <p class="section-subtitle">Detailed information about this property</p>
                    </div>
                </div>
                <div class="description-content">
                    <p>{!! nl2br(e($property->description)) !!}</p>
                </div>
            </div>
        @endif

    </div>

    {{-- RESERVATION MODAL --}}
    @auth
    <div class="modal fade" id="reservationModal" tabindex="-1" aria-labelledby="reservationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content reservation-modal-content">
                <div class="reservation-modal-header">
                    <div class="modal-header-content">
                        <div class="modal-icon-wrapper">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                        <div>
                            <h5 class="modal-title" id="reservationModalLabel">Reserve Property</h5>
                            <p class="modal-subtitle">{{ $property->category ?? 'Property' }}</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <form action="{{ route('properties.reserve', $property->id) }}" method="POST">
                    @csrf
                    <div class="reservation-modal-body">
                        @if($errors->any())
                            <div class="alert alert-danger reservation-alert">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                <div>
                                    <strong>Please fix the following errors:</strong>
                                    <ul class="mb-0 mt-2">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif
                        
                        <div class="property-summary-card">
                            <div class="summary-header">
                                <i class="bi bi-info-circle me-2"></i>
                                <span>Property Summary</span>
                            </div>
                            <div class="summary-content">
                                <div class="summary-item">
                                    <i class="bi bi-geo-alt-fill"></i>
                                    <div>
                                        <span class="summary-label">Location</span>
                                        <span class="summary-value">{{ $property->location }}</span>
                                    </div>
                                </div>
                                <div class="summary-item">
                                    <i class="bi bi-currency-dollar"></i>
                                    <div>
                                        <span class="summary-label">Price</span>
                                        <span class="summary-value">{{ number_format($property->price) }} EGP</span>
                                    </div>
                                </div>
                                <div class="summary-item">
                                    <i class="bi bi-arrow-left-right"></i>
                                    <div>
                                        <span class="summary-label">Type</span>
                                        <span class="summary-value">{{ ucfirst($property->transaction_type) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-section">
                            <div class="section-header">
                                <i class="bi bi-calendar-event me-2"></i>
                                <span>Appointment Details</span>
                            </div>
                            <div class="form-group-modern">
                                <label for="meeting_datetime" class="form-label-modern">
                                    <i class="bi bi-calendar-check me-1"></i>Viewing Appointment Date <span class="required">*</span>
                                </label>
                                <input type="date" 
                                       class="form-control-modern @error('meeting_datetime') is-invalid @enderror" 
                                       id="meeting_datetime" 
                                       name="meeting_datetime" 
                                       value="{{ old('meeting_datetime') }}"
                                       min="{{ now()->addDay()->format('Y-m-d') }}"
                                       required>
                                <small class="form-help-text">Select the date you'd like to view the property</small>
                                @error('meeting_datetime')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        @if($property->transaction_type === 'rent')
                            <div class="form-section rental-section">
                                <div class="section-header">
                                    <i class="bi bi-house-door me-2"></i>
                                    <span>Rental Details</span>
                                </div>
                                
                                <div class="form-group-modern">
                                    <label for="start_date" class="form-label-modern">
                                        <i class="bi bi-calendar-check me-1"></i>Rental Start Date <span class="required">*</span>
                                    </label>
                                    <input type="date" 
                                           class="form-control-modern @error('start_date') is-invalid @enderror" 
                                           id="start_date" 
                                           name="start_date" 
                                           value="{{ old('start_date') }}"
                                           required>
                                    <small class="form-help-text">When do you want to start renting?</small>
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <label for="duration_value" class="form-label-modern">
                                                <i class="bi bi-hourglass-split me-1"></i>Duration <span class="required">*</span>
                                            </label>
                                            <input type="number" 
                                                   class="form-control-modern @error('duration_value') is-invalid @enderror" 
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
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <label for="duration_unit" class="form-label-modern">Period <span class="required">*</span></label>
                                            <select class="form-control-modern form-select-modern @error('duration_unit') is-invalid @enderror" 
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
                            </div>
                        @endif
                        
                        <div class="form-section">
                            <div class="form-group-modern">
                                <label for="notes" class="form-label-modern">
                                    <i class="bi bi-chat-left-text me-1"></i>Additional Notes <span class="optional">(Optional)</span>
                                </label>
                                <textarea class="form-control-modern @error('notes') is-invalid @enderror" 
                                          id="notes" 
                                          name="notes" 
                                          rows="4" 
                                          maxlength="500"
                                          placeholder="Any special requests or questions?">{{ old('notes') }}</textarea>
                                <small class="form-help-text">Maximum 500 characters</small>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="info-notice">
                            <i class="bi bi-info-circle-fill"></i>
                            <div>
                                <strong>Confirmation Email</strong>
                                <p class="mb-0">You'll receive a confirmation email with all the details after submitting this reservation.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="reservation-modal-footer">
                        <button type="button" class="btn btn-cancel-modal" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-2"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-confirm-reservation">
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
