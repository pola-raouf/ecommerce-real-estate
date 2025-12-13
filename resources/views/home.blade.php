<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EL Kayan - Home</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('css/footer.css') }}">
    <link rel="stylesheet" href="{{ asset('css/notifications.css') }}">
</head>

<body>

    <!-- ================= NAVBAR ================= -->
    @include('includes.navbar', ['showNotifications' => true, 'showSettings' => false, 'showDashboard' => true])

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

    </script>

    <!-- Professional Footer -->
    @include('includes.footer')
</body>

</html>
