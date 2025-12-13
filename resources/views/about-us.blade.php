<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - EL Kayan</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/about.css') }}">
    <link rel="stylesheet" href="{{ asset('css/footer.css') }}">
</head>

<body>

    <!-- ================= NAVBAR ================= -->
    @include('includes.navbar', ['showNotifications' => false, 'showSettings' => false, 'showDashboard' => true])

    <!-- ================= PAGE CONTENT ================= -->
    <div class="page-wrapper">
        <div class="container">
            <div class="page-hero">
                <div class="row align-items-center gy-4">
                    <div class="col-lg-6 fade-up">
                        <p class="eyebrow mb-2">About EL Kayan</p>
                        <h1 class="display-4 fw-bold text-heading">Designing real estate journeys that feel effortless</h1>
                        <p class="lead text-body-secondary mb-4">We combine a curated portfolio, transparent processes, and
                            a people-first team so every move—buying, selling, or renting—stays calm, confident, and on-brand.</p>

                        <div class="hero-pills mb-4">
                            <span class="pill"><i class="bi bi-stars me-2"></i>Human-centered support</span>
                            <span class="pill"><i class="bi bi-lightning-charge-fill me-2"></i>Fast, clear steps</span>
                            <span class="pill"><i class="bi bi-shield-check me-2"></i>Verified listings only</span>
                        </div>

                        <div class="row g-3">
                            <div class="col-6 col-sm-4">
                                <div class="stat-card">
                                    <div class="stat-value">4.9/5</div>
                                    <div class="stat-label">Client satisfaction</div>
                                </div>
                            </div>
                            <div class="col-6 col-sm-4">
                                <div class="stat-card">
                                    <div class="stat-value">120+</div>
                                    <div class="stat-label">Premium listings</div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-4">
                                <div class="stat-card">
                                    <div class="stat-value">24/7</div>
                                    <div class="stat-label">Dedicated support</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 fade-up">
                        <div class="hero-visual">
                            <img src="{{ url('images/main1.jpg') }}" class="img-fluid" alt="EL Kayan">
                            <div class="hero-floating-card shadow-sm">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="floating-icon">
                                        <i class="bi bi-people-fill"></i>
                                    </div>
                                    <div>
                                        <p class="small text-uppercase text-muted mb-1">Trusted by families</p>
                                        <strong class="h5 mb-0 d-block">Turning searches into homes</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content-section fade-up">
                <div class="row align-items-center gy-4">
                    <div class="col-md-6">
                        <img src="{{ url('images/background1.jpg') }}" class="img-fluid rounded-4 shadow-lg about-image"
                            alt="Who we are">
                    </div>
                    <div class="col-md-6">
                        <div class="section-heading">
                            <p class="eyebrow mb-2">Who we are</p>
                            <h2 class="h1 text-heading mb-3">A modern, responsive real estate partner</h2>
                            <p class="text-body-secondary mb-3">EL Kayan blends technology with attentive experts to connect
                                buyers, sellers, and renters to places that match their goals—and feel good to live in.</p>
                            <div class="value-chips">
                                <span class="chip"><i class="bi bi-check2-circle me-2"></i>Transparent process</span>
                                <span class="chip"><i class="bi bi-geo-alt me-2"></i>Prime neighborhoods</span>
                                <span class="chip"><i class="bi bi-brush me-2"></i>Carefully curated visuals</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mission & Vision -->
            <div class="row g-4 mb-5 fade-up">
                <div class="col-md-6">
                    <div class="section-card h-100">
                        <div class="card-icon gradient-primary"><i class="bi bi-rocket-takeoff-fill"></i></div>
                        <h3 class="fw-bold text-heading mb-2">Our Mission</h3>
                        <p class="text-body-secondary mb-0">Simplify every transaction with honest guidance, curated listings,
                            and a calm, confident experience from the first viewing to the final signature.</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="section-card h-100">
                        <div class="card-icon gradient-secondary"><i class="bi bi-binoculars-fill"></i></div>
                        <h3 class="fw-bold text-heading mb-2">Our Vision</h3>
                        <p class="text-body-secondary mb-0">Become the region’s most trusted destination for real estate journeys
                            that feel premium, personal, and beautifully clear.</p>
                    </div>
                </div>
            </div>

            <!-- Team Section -->
            <div class="team-section fade-up">
                <div class="text-center mb-4">
                    <p class="eyebrow mb-2">Team</p>
                    <h2 class="fw-bold text-heading mb-2">People who craft the experience</h2>
                    <p class="text-body-secondary">A multidisciplinary team making sure every interaction stays polished.</p>
                </div>
                <div class="row justify-content-center">
                    @php
                        $team = [
                            ['img' => 'Eyad1.jpg', 'name' => 'Eyad Ashraf', 'role' => 'Back-end Developer'],
                            ['img' => 'pola.jpg', 'name' => 'Pola Raouf', 'role' => 'Back-end Developer'],
                            ['img' => 'Ahmed.jpg', 'name' => 'Ahmed Tamer', 'role' => 'Front-end Developer'],
                            ['img' => 'bashmo.jpg', 'name' => 'Abdelrahman', 'role' => 'Front-end Developer'],
                            ['img' => 'nour.jpg', 'name' => 'Nour Mohey', 'role' => 'Front-end Developer'] // new member
                        ];
                    @endphp

                    @foreach($team as $member)
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                            <div class="card shadow-sm hover-card glow-hover h-100">
                                <img src="{{ url('images/about-us/' . $member['img']) }}"
                                    class="card-img-top team-img glow-hover" alt="{{ $member['name'] }}">
                                <div class="card-body text-center rounded-name">
                                    <h5 class="card-title fw-bold text-black mb-1">{{ $member['name'] }}</h5>
                                    <p class="card-text text-secondary-light mb-0">{{ $member['role'] }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Why Choose Us Section -->
            <div class="why-choose-section fade-up">
                <div class="text-center mb-4">
                    <p class="eyebrow mb-2">Why choose us</p>
                    <h2 class="fw-bold text-heading mb-2">A flow that feels premium end-to-end</h2>
                    <p class="text-body-secondary">Every detail—from colors to copy—is designed to be reassuring and easy on the eyes.</p>
                </div>
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="section-card text-center h-100">
                            <div class="card-icon gradient-primary"><i class="bi bi-shield-check-fill"></i></div>
                            <h4 class="fw-bold text-heading mb-2">Trusted service</h4>
                            <p class="text-body-secondary mb-0">Verified listings and guided steps so you can decide with confidence.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="section-card text-center h-100">
                            <div class="card-icon gradient-secondary"><i class="bi bi-house-heart-fill"></i></div>
                            <h4 class="fw-bold text-heading mb-2">Curated portfolio</h4>
                            <p class="text-body-secondary mb-0">Homes chosen for location, light, and lifestyle fit—not just price.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="section-card text-center h-100">
                            <div class="card-icon gradient-primary"><i class="bi bi-people-fill"></i></div>
                            <h4 class="fw-bold text-heading mb-2">Expert partners</h4>
                            <p class="text-body-secondary mb-0">A responsive team that keeps updates clear and timelines on track.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Professional Footer -->
    @include('includes.footer')

    <!-- ========================= SCRIPTS ========================= -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const faders = document.querySelectorAll('.fade-up');
            const appearOptions = { threshold: 0.2, rootMargin: "0px 0px -50px 0px" };
            const appearOnScroll = new IntersectionObserver(function (entries, observer) {
                entries.forEach(entry => {
                    if (!entry.isIntersecting) return;
                    entry.target.classList.add('appear');
                    observer.unobserve(entry.target);
                });
            }, appearOptions);

            faders.forEach(fader => appearOnScroll.observe(fader));
        });
    </script>

</body>

</html>
