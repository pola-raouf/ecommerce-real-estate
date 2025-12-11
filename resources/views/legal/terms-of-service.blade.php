<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms of Service - EL Kayan Real Estate</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('css/legal.css') }}">
    <link rel="stylesheet" href="{{ asset('css/footer.css') }}">
</head>

<body>

    <!-- Navbar -->
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
                        <a class="nav-link fw-semibold" href="{{ url('/') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-semibold" href="{{ route('about-us') }}">About Us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-semibold" href="{{ route('properties.index') }}">Properties</a>
                    </li>
                    @auth
                        @if(in_array(auth()->user()->role, ['admin', 'seller']))
                            <li class="nav-item">
                                <a class="nav-link fw-semibold" href="{{ route('dashboard') }}">Dashboard</a>
                            </li>
                        @endif
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

    <!-- Legal Page Content -->
    <main class="legal-page">
        <div class="legal-header">
            <h1><i class="bi bi-file-text-fill me-2"></i>Terms of Service</h1>
            <p class="last-updated">Last Updated: December 10, 2025</p>
        </div>

        <div class="legal-content">
            <div class="table-of-contents">
                <h3>Table of Contents</h3>
                <ul>
                    <li><a href="#acceptance">Acceptance of Terms</a></li>
                    <li><a href="#accounts">User Accounts</a></li>
                    <li><a href="#services">Our Services</a></li>
                    <li><a href="#property-listings">Property Listings</a></li>
                    <li><a href="#intellectual-property">Intellectual Property</a></li>
                    <li><a href="#user-conduct">User Conduct</a></li>
                    <li><a href="#limitation">Limitation of Liability</a></li>
                    <li><a href="#termination">Termination</a></li>
                    <li><a href="#changes">Changes to Terms</a></li>
                    <li><a href="#contact">Contact Information</a></li>
                </ul>
            </div>

            <section id="acceptance">
                <h2>1. Acceptance of Terms</h2>
                <p>
                    Welcome to EL Kayan Real Estate. By accessing or using our website and services, you agree to be
                    bound by these Terms of Service ("Terms"). If you do not agree to these Terms, please do not use our
                    services.
                </p>
                <p>
                    These Terms constitute a legally binding agreement between you and EL Kayan Real Estate. We reserve
                    the right to modify these Terms at any time, and such modifications will be effective immediately
                    upon posting.
                </p>
            </section>

            <section id="accounts">
                <h2>2. User Accounts</h2>

                <h3>2.1 Account Creation</h3>
                <p>To access certain features of our services, you may be required to create an account. When creating
                    an account, you agree to:</p>
                <ul>
                    <li>Provide accurate, current, and complete information</li>
                    <li>Maintain and promptly update your account information</li>
                    <li>Maintain the security of your password and account</li>
                    <li>Accept responsibility for all activities that occur under your account</li>
                    <li>Notify us immediately of any unauthorized use of your account</li>
                </ul>

                <h3>2.2 Account Types</h3>
                <p>We offer different types of accounts:</p>
                <ul>
                    <li><strong>Buyer Accounts:</strong> For users searching for properties</li>
                    <li><strong>Seller Accounts:</strong> For property owners and real estate agents listing properties
                    </li>
                    <li><strong>Admin Accounts:</strong> For authorized EL Kayan staff members</li>
                </ul>

                <h3>2.3 Account Termination</h3>
                <p>
                    We reserve the right to suspend or terminate your account at any time for violations of these Terms,
                    fraudulent activity, or any other reason we deem appropriate. You may also terminate your account at
                    any time by contacting us.
                </p>
            </section>

            <section id="services">
                <h2>3. Our Services</h2>
                <p>EL Kayan Real Estate provides an online platform for:</p>
                <ul>
                    <li>Browsing and searching property listings</li>
                    <li>Listing properties for sale or rent</li>
                    <li>Connecting buyers with sellers and real estate professionals</li>
                    <li>Managing property reservations and inquiries</li>
                    <li>Accessing real estate market information and resources</li>
                </ul>

                <div class="highlight-box">
                    <p><strong>Important:</strong> EL Kayan acts as a platform connecting buyers and sellers. We are not
                        a party to any transactions between users and do not guarantee the accuracy, quality, safety, or
                        legality of any listings or transactions.</p>
                </div>
            </section>

            <section id="property-listings">
                <h2>4. Property Listings</h2>

                <h3>4.1 Listing Requirements</h3>
                <p>If you list a property on our platform, you represent and warrant that:</p>
                <ul>
                    <li>You have the legal right to list the property</li>
                    <li>All information provided is accurate and complete</li>
                    <li>All photos and descriptions are truthful and not misleading</li>
                    <li>The property complies with all applicable laws and regulations</li>
                    <li>You will honor all reservations and inquiries made through our platform</li>
                </ul>

                <h3>4.2 Prohibited Listings</h3>
                <p>You may not list properties that:</p>
                <ul>
                    <li>Violate any local, state, or federal laws</li>
                    <li>Infringe on intellectual property rights</li>
                    <li>Contain false, misleading, or deceptive information</li>
                    <li>Discriminate based on race, religion, gender, or other protected characteristics</li>
                    <li>Are not actually available for sale or rent</li>
                </ul>

                <h3>4.3 Listing Fees</h3>
                <p>
                    Certain listing features may require payment of fees. All fees are non-refundable unless otherwise
                    stated. We reserve the right to change our fee structure at any time with reasonable notice.
                </p>
            </section>

            <section id="intellectual-property">
                <h2>5. Intellectual Property</h2>

                <h3>5.1 Our Content</h3>
                <p>
                    All content on our website, including text, graphics, logos, images, software, and design, is the
                    property of EL Kayan Real Estate or our licensors and is protected by copyright, trademark, and
                    other intellectual property laws.
                </p>

                <h3>5.2 User Content</h3>
                <p>
                    By submitting content to our platform (including property listings, photos, and reviews), you grant
                    us a worldwide, non-exclusive, royalty-free license to use, reproduce, modify, and display such
                    content for the purpose of operating and promoting our services.
                </p>

                <h3>5.3 Restrictions</h3>
                <p>You may not:</p>
                <ul>
                    <li>Copy, modify, or distribute our content without permission</li>
                    <li>Use our trademarks or branding without authorization</li>
                    <li>Reverse engineer or attempt to extract source code from our platform</li>
                    <li>Create derivative works based on our services</li>
                </ul>
            </section>

            <section id="user-conduct">
                <h2>6. User Conduct</h2>
                <p>You agree not to:</p>
                <ul>
                    <li>Use our services for any illegal or unauthorized purpose</li>
                    <li>Harass, abuse, or harm other users</li>
                    <li>Post spam, advertisements, or unsolicited communications</li>
                    <li>Attempt to gain unauthorized access to our systems</li>
                    <li>Interfere with or disrupt our services or servers</li>
                    <li>Use automated systems (bots, scrapers) without permission</li>
                    <li>Impersonate any person or entity</li>
                    <li>Collect or store personal data about other users</li>
                </ul>
            </section>

            <section id="limitation">
                <h2>7. Limitation of Liability</h2>
                <p>
                    TO THE MAXIMUM EXTENT PERMITTED BY LAW, EL KAYAN REAL ESTATE SHALL NOT BE LIABLE FOR ANY INDIRECT,
                    INCIDENTAL, SPECIAL, CONSEQUENTIAL, OR PUNITIVE DAMAGES, OR ANY LOSS OF PROFITS OR REVENUES, WHETHER
                    INCURRED DIRECTLY OR INDIRECTLY, OR ANY LOSS OF DATA, USE, GOODWILL, OR OTHER INTANGIBLE LOSSES.
                </p>
                <p>
                    We do not guarantee the accuracy, completeness, or reliability of any property listings or
                    user-generated content. All transactions are conducted at your own risk.
                </p>
                <p>
                    Our total liability to you for any claims arising from your use of our services shall not exceed the
                    amount you paid to us in the twelve (12) months prior to the claim.
                </p>
            </section>

            <section id="termination">
                <h2>8. Termination</h2>
                <p>
                    We may terminate or suspend your access to our services immediately, without prior notice or
                    liability, for any reason, including breach of these Terms. Upon termination, your right to use our
                    services will immediately cease.
                </p>
                <p>
                    All provisions of these Terms which by their nature should survive termination shall survive,
                    including ownership provisions, warranty disclaimers, indemnity, and limitations of liability.
                </p>
            </section>

            <section id="changes">
                <h2>9. Changes to Terms</h2>
                <p>
                    We reserve the right to modify or replace these Terms at any time. If a revision is material, we
                    will provide at least 30 days' notice prior to any new terms taking effect. What constitutes a
                    material change will be determined at our sole discretion.
                </p>
                <p>
                    By continuing to access or use our services after revisions become effective, you agree to be bound
                    by the revised terms.
                </p>
            </section>

            <section id="contact">
                <div class="contact-section">
                    <h3>10. Contact Information</h3>
                    <p>If you have any questions about these Terms of Service, please contact us:</p>
                    <p><strong>EL Kayan Real Estate</strong></p>
                    <p><i class="bi bi-envelope-fill me-2"></i>Email: <a
                            href="mailto:legal@elkayan.com">legal@elkayan.com</a></p>
                    <p><i class="bi bi-telephone-fill me-2"></i>Phone: <a href="tel:+15551234567">+1 (555) 123-4567</a>
                    </p>
                    <p><i class="bi bi-geo-alt-fill me-2"></i>Address: 123 Skyline Avenue, Downtown, City 12345</p>
                </div>
            </section>
        </div>
    </main>

    <!-- Footer -->
    @include('includes.footer')

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>