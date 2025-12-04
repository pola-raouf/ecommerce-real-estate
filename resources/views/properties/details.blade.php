<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Details - EL Kayan</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <!-- cCustom CSS -->
    <link rel="stylesheet" href="{{ asset('css/properties-details.css') }}">
</head>

@php
    $reservation = $property->reservation;
    $isSold = $property->status === 'sold';
    $isReservedByOther = $reservation && $reservation->user_id !== Auth::id();
    $canReserve = Auth::check() && !$reservation && !$isSold;
    $canCancel = Auth::check() && $reservation && $reservation->user_id === Auth::id();
@endphp

<body>
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
                <li class="nav-item"><a class="nav-link fw-semibold {{ Request::is('/') ? 'active' : '' }}" href="{{ url('/') }}">Home</a></li>
                <li class="nav-item"><a class="nav-link fw-semibold {{ Request::is('about-us') ? 'active' : '' }}" href="{{ route('about-us') }}">About Us</a></li>
                <li class="nav-item"><a class="nav-link fw-semibold {{ Request::is('properties') ? 'active' : '' }}" href="{{ route('properties.index') }}">Properties</a></li>
                @auth
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                        <img src="{{ Auth::user()->profile_image_url }}" 
                             alt="{{ Auth::user()->name }}" 
                             class="rounded-circle profile-img me-2">
                        <span>{{ Auth::user()->name }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item d-flex align-items-center" href="{{ route('profile') }}"><i class="bi bi-person-circle me-2"></i>Profile</a></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item d-flex align-items-center"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
                            </form>
                        </li>
                    </ul>
                </li>
                @else
                <li class="nav-item"><a class="btn btn-custom btn-sm fw-bold ms-2" href="{{ route('login.form') }}"><i class="bi bi-box-arrow-in-right me-1"></i> Login</a></li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

<div class="container property-details-container mt-5">

    <a href="{{ route('properties.index') }}" class="back-button">
        <i class="bi bi-arrow-left"></i> Back to Listings
    </a>

    <h1 class="property-title">{{ $property->title ?? $property->category ?? 'Property Details' }}</h1>

    <div class="row g-4">
        {{-- Images --}}
        <div class="col-lg-7">
            <div class="property-images-card">
                <h2><i class="bi bi-images me-2"></i>Property Images</h2>
                <div class="property-image-grid">
                    @if($property->images && $property->images->count() > 0)
                        @foreach($property->images as $index => $image)
                            <div class="property-image-item">
                                <a href="#" data-bs-toggle="modal" data-bs-target="#imageModal"
                                   data-bs-image-url="{{ asset($image->image_path) }}">
                                    <img src="{{ asset($image->image_path) }}" alt="Property Image">
                                </a>
                            </div>
                        @endforeach
                    @elseif($property->image)
                        <div class="property-image-item">
                            <a href="#" data-bs-toggle="modal" data-bs-target="#imageModal"
                               data-bs-image-url="{{ asset($property->image) }}">
                                <img src="{{ asset($property->image) }}" alt="Property Image">
                            </a>
                        </div>
                    @else
                        <div class="no-images text-center">
                            <i class="bi bi-image fs-1 d-block mb-3"></i>
                            <p>No images available for this property.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Payment / Reservation --}}
        <div class="col-lg-5">
            <div class="payment-card">
                <div class="payment-card-header">
                    <h2><i class="bi bi-credit-card me-2"></i>Reservation</h2>
                </div>
                <div class="payment-card-body">
                    @auth
    @if($property->status === 'available' && !$reservation)
        <button type="button" class="reserve-btn" data-property-id="{{ $property->id }}"> 
            <i class="bi bi-check-circle me-2"></i>Reserve this Property
        </button>
    @elseif($property->status === 'pending')
        <button type="button" class="reserve-btn pending" disabled>
            <i class="bi bi-hourglass-split me-2"></i>Pending
        </button>
    @elseif($canCancel)
        <button type="button" class="reserve-btn cancel" data-property-id="{{ $property->id }}">
            <i class="bi bi-x-circle me-2"></i>Cancel Reservation
        </button>
    @elseif($property->status === 'sold')
        <button type="button" class="reserve-btn sold-out" disabled>
            <i class="bi bi-x-circle me-2"></i>Sold Out
        </button>
    @endif
@else
    <a href="{{ route('login.form') }}" class="reserve-btn login-link">
        <i class="bi bi-box-arrow-in-right me-2"></i>Login to reserve
    </a>
@endauth

                </div>
            </div>

            {{-- Property Info --}}
            <div class="payment-card mt-3">
                <div class="payment-card-header">
                    <h2><i class="bi bi-info-circle me-2"></i>Property Information</h2>
                </div>
                <div class="payment-card-body">
                    @if($property->location)
                        <p><strong><i class="bi bi-geo-alt me-2"></i>Location:</strong> {{ $property->location }}</p>
                    @endif
                    @if($property->price)
                        <p><strong><i class="bi bi-currency-dollar me-2"></i>Price:</strong> {{ number_format($property->price) }} EGP</p>
                    @endif
                    @if($property->status)
                        <p><strong><i class="bi bi-tag me-2"></i>Status:</strong> 
                            <span class="badge bg-{{ $property->status === 'available' ? 'success' : ($property->status === 'sold' ? 'danger' : 'warning') }}">
                                {{ ucfirst($property->status) }}
                            </span>
                        </p>
                    @endif
                    @if($property->transaction_type)
                        <p><strong><i class="bi bi-arrow-left-right me-2"></i>Type:</strong> {{ ucfirst($property->transaction_type) }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Description --}}
    @if($property->description)
        <div class="description-card mt-4">
            <h2><i class="bi bi-file-text me-2"></i>Description</h2>
            <p>{!! nl2br(e($property->description)) !!}</p>
        </div>
    @endif

</div>

{{-- IMAGE VIEWER MODAL --}}
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Property Image</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img src="" class="img-fluid" id="modalImage">
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

{{-- Robust AJAX Reservation --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = '{{ csrf_token() }}';

    function updateButtonAndBadge(button, newStatus) {
        const badge = document.querySelector('.payment-card-body .badge');
        if(badge) {
            badge.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
            badge.className = 'badge bg-' + (newStatus === 'available' ? 'success' : (newStatus === 'sold' ? 'danger' : 'warning'));
        }

        if(newStatus === 'reserved') {
            button.classList.add('cancel');
            button.textContent = 'Cancel Reservation';
        } else if(newStatus === 'available') {
            button.classList.remove('cancel');
            button.textContent = 'Reserve this Property';
        }
    }

    async function handleReservation(button, method) {
        const propertyId = button.dataset.propertyId;
        if(!propertyId) return;

        button.disabled = true;
        button.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Processing...';

        const url = method === 'POST' ? `/properties/${propertyId}/reserve` : `/properties/${propertyId}/reservation`;

        try {
            const response = await fetch(url, {
                method: method,
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();

            if(response.ok) {
                updateButtonAndBadge(button, method === 'POST' ? 'reserved' : 'available');
            } else {
                alert(data.message || 'Something went wrong!');
            }
        } catch(err) {
            console.error(err);
            alert('Something went wrong!');
        } finally {
            button.disabled = false;
        }
    }

    document.querySelectorAll('.reserve-btn').forEach(button => {
        button.addEventListener('click', function() {
            if(button.classList.contains('cancel')) {
                handleReservation(button, 'DELETE');
            } else {
                handleReservation(button, 'POST');
            }
        });
    });

    // Image modal
    const imageModal = document.getElementById('imageModal');
    imageModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const imageUrl = button.getAttribute('data-bs-image-url');
        const modalImage = imageModal.querySelector('#modalImage');
        modalImage.src = imageUrl;
    });
});
</script>

</body>
</html>
