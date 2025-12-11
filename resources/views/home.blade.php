<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EL Kayan - Home</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Custom Home CSS -->
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('css/footer.css') }}">
    <link rel="stylesheet" href="{{ asset('css/notifications.css') }}">
</head>

<body>

    <!-- ================= NAVBAR (EXACT COPY) ================= -->
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
                        @auth
                            @if(in_array(auth()->user()->role, ['admin', 'seller']))
                                <li class="nav-item">
                                    <a class="nav-link fw-semibold {{ Request::is('dashboard') ? 'active' : '' }}"
                                        href="{{ route('dashboard') }}">Dashboard</a>
                                </li>
                            @endif
                        @endauth

                        <!-- Notification Bell -->
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

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
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

    {{-- ================= LOGIN POPUP ================= --}}
    @if (session('login_popup'))
        <div id="login-popup" class="popup-container">
            <div class="popup-box">
                {{ session('login_popup') }}
            </div>
        </div>
    @endif
    <!-- ================= HERO SECTION ================= -->
    <section class="hero-section">
        <div class="floating-shape shape-1"></div>
        <div class="floating-shape shape-2"></div>
        <div class="floating-shape shape-3"></div>
        <div class="overlay"></div>

        <div class="hero-content text-center">
            <h1 class="hero-title">Your Dream Property Awaits</h1>
            <p class="hero-subtitle">Discover the best homes, apartments, and offices for sale or rent in prime
                locations.</p>
            <a href="{{ route('properties.index') }}" class="btn btn-hero">Explore Properties</a>
        </div>
    </section>

    <!-- ================= FEATURES SECTION ================= -->
    <section class="features-section py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-house-heart"></i>
                        </div>
                        <h3 class="feature-title">Premium Properties</h3>
                        <p class="feature-text">Handpicked selection of luxury homes and apartments in the most
                            desirable neighborhoods.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <h3 class="feature-title">Trusted Service</h3>
                        <p class="feature-text">Verified listings with professional support throughout your property
                            journey.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-geo-alt"></i>
                        </div>
                        <h3 class="feature-title">Prime Locations</h3>
                        <p class="feature-text">Properties in the best areas with excellent connectivity and amenities.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ================= PROPERTY CATEGORIES ================= -->
    <section class="categories-section py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">Property Categories</h2>
                <p class="section-subtitle">Find your perfect property type</p>
            </div>
            <div class="row g-4">
                <div class="col-md-3 col-sm-6">
                    <div class="category-card">
                        <div class="category-icon">
                            <i class="bi bi-house-door"></i>
                        </div>
                        <h4>Houses</h4>
                        <p>Spacious family homes</p>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="category-card">
                        <div class="category-icon">
                            <i class="bi bi-building"></i>
                        </div>
                        <h4>Apartments</h4>
                        <p>Modern living spaces</p>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="category-card">
                        <div class="category-icon">
                            <i class="bi bi-briefcase"></i>
                        </div>
                        <h4>Offices</h4>
                        <p>Commercial spaces</p>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="category-card">
                        <div class="category-icon">
                            <i class="bi bi-shop"></i>
                        </div>
                        <h4>Retail</h4>
                        <p>Shop & storefronts</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ================= STATS SECTION ================= -->
    <section class="stats-section py-5">
        <div class="container">
            <div class="row g-4 text-center">
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card">
                        <div class="stat-number" data-target="1000">0</div>
                        <div class="stat-label">Properties</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card">
                        <div class="stat-number" data-target="500">0</div>
                        <div class="stat-label">Happy Clients</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card">
                        <div class="stat-number" data-target="50">0</div>
                        <div class="stat-label">Locations</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card">
                        <div class="stat-number" data-target="15">0</div>
                        <div class="stat-label">Years Experience</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Navbar scroll effect
        const navbar = document.getElementById('mainNavbar');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                navbar.classList.add('navbar-scrolled');
            } else {
                navbar.classList.remove('navbar-scrolled');
            }
        });

        // Stats counter animation
        const observerOptions = {
            threshold: 0.5
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const statNumber = entry.target;
                    const target = parseInt(statNumber.getAttribute('data-target'));
                    const duration = 2000;
                    const increment = target / (duration / 16);
                    let current = 0;

                    const updateCounter = () => {
                        current += increment;
                        if (current < target) {
                            statNumber.textContent = Math.floor(current);
                            requestAnimationFrame(updateCounter);
                        } else {
                            statNumber.textContent = target + '+';
                        }
                    };

                    updateCounter();
                    observer.unobserve(statNumber);
                }
            });
        }, observerOptions);

        document.querySelectorAll('.stat-number').forEach(stat => {
            observer.observe(stat);
        });
    </script>
    <script>
        const popup = document.getElementById('login-popup');
        if (popup) {
            setTimeout(() => {
                popup.classList.add('fade-out');
                setTimeout(() => popup.remove(), 400); // Remove after fade out
            }, 5000); // 5 seconds
        }

        // Notification system
        @auth
        function fetchNotifications() {
            fetch('{{ route("notifications.recent") }}')
                .then(response => response.json())
                .then(data => {
                    const badge = document.getElementById('notificationBadge');
                    const count = document.getElementById('notificationCount');
                    const list = document.getElementById('notificationList');
                    
                    // Update badge
                    if (data.unread_count > 0) {
                        badge.textContent = data.unread_count;
                        badge.style.display = 'block';
                        count.textContent = data.unread_count + ' new';
                    } else {
                        badge.style.display = 'none';
                        count.textContent = '0 new';
                    }
                    
                    // Update notification list
                    if (data.notifications.length > 0) {
                        list.innerHTML = '';
                        data.notifications.forEach(notification => {
                            const item = document.createElement('li');
                            item.className = 'dropdown-item notification-dropdown-item' + (notification.read_at ? ' read' : ' unread');
                            item.innerHTML = `
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-house-fill text-primary me-2 mt-1"></i>
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold small">${notification.message}</div>
                                        <small class="text-muted">${notification.created_at}</small>
                                    </div>
                                </div>
                            `;
                            if (notification.property_id) {
                                item.style.cursor = 'pointer';
                                item.onclick = () => {
                                    window.location.href = '/properties/' + notification.property_id;
                                };
                            }
                            list.appendChild(item);
                        });
                    } else {
                        list.innerHTML = `
                            <li class="text-center py-3 text-muted">
                                <i class="bi bi-bell-slash"></i>
                                <p class="mb-0 small">No new notifications</p>
                            </li>
                        `;
                    }
                })
                .catch(error => console.error('Error fetching notifications:', error));
        }
        
        // Fetch notifications on page load
        fetchNotifications();
        
        // Refresh notifications every 30 seconds
        setInterval(fetchNotifications, 30000);
        @endauth
    
    </script>

    <!-- Professional Footer -->
    @include('includes.footer')
</body>

</html>
