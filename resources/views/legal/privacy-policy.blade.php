<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - EL Kayan Real Estate</title>

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
            <h1><i class="bi bi-shield-lock-fill me-2"></i>Privacy Policy</h1>
            <p class="last-updated">Last Updated: December 10, 2025</p>
        </div>

        <div class="legal-content">
            <div class="table-of-contents">
                <h3>Table of Contents</h3>
                <ul>
                    <li><a href="#introduction">Introduction</a></li>
                    <li><a href="#information-collection">Information We Collect</a></li>
                    <li><a href="#information-use">How We Use Your Information</a></li>
                    <li><a href="#data-protection">Data Protection & Security</a></li>
                    <li><a href="#third-party">Third-Party Services</a></li>
                    <li><a href="#user-rights">Your Rights</a></li>
                    <li><a href="#contact">Contact Us</a></li>
                </ul>
            </div>

            <section id="introduction">
                <h2>1. Introduction</h2>
                <p>
                    Welcome to EL Kayan Real Estate ("we," "our," or "us"). We are committed to protecting your privacy
                    and ensuring the security of your personal information. This Privacy Policy explains how we collect,
                    use, disclose, and safeguard your information when you visit our website and use our services.
                </p>
                <p>
                    By accessing or using our website, you agree to the terms of this Privacy Policy. If you do not
                    agree with the terms, please do not access or use our services.
                </p>
            </section>

            <section id="information-collection">
                <h2>2. Information We Collect</h2>

                <h3>2.1 Personal Information</h3>
                <p>We may collect the following types of personal information:</p>
                <ul>
                    <li><strong>Contact Information:</strong> Name, email address, phone number, and mailing address
                    </li>
                    <li><strong>Account Information:</strong> Username, password, and profile details</li>
                    <li><strong>Property Preferences:</strong> Search criteria, saved properties, and viewing history
                    </li>
                    <li><strong>Transaction Information:</strong> Property inquiries, reservation details, and
                        communication history</li>
                    <li><strong>Payment Information:</strong> Billing address and payment method details (processed
                        securely through third-party payment processors)</li>
                </ul>

                <h3>2.2 Automatically Collected Information</h3>
                <p>When you visit our website, we automatically collect certain information:</p>
                <ul>
                    <li><strong>Device Information:</strong> IP address, browser type, operating system, and device
                        identifiers</li>
                    <li><strong>Usage Data:</strong> Pages viewed, time spent on pages, links clicked, and navigation
                        paths</li>
                    <li><strong>Location Data:</strong> Approximate geographic location based on IP address</li>
                    <li><strong>Cookies and Tracking Technologies:</strong> Information collected through cookies, web
                        beacons, and similar technologies</li>
                </ul>
            </section>

            <section id="information-use">
                <h2>3. How We Use Your Information</h2>
                <p>We use the collected information for the following purposes:</p>
                <ul>
                    <li><strong>Service Provision:</strong> To provide, maintain, and improve our real estate services
                    </li>
                    <li><strong>Property Matching:</strong> To match you with properties that meet your preferences</li>
                    <li><strong>Communication:</strong> To respond to inquiries, send updates, and provide customer
                        support</li>
                    <li><strong>Account Management:</strong> To create and manage your user account</li>
                    <li><strong>Marketing:</strong> To send promotional materials and newsletters (with your consent)
                    </li>
                    <li><strong>Analytics:</strong> To analyze website usage and improve user experience</li>
                    <li><strong>Security:</strong> To detect, prevent, and address fraud and security issues</li>
                    <li><strong>Legal Compliance:</strong> To comply with legal obligations and enforce our terms</li>
                </ul>

                <div class="highlight-box">
                    <p><strong>Note:</strong> We will never sell your personal information to third parties for their
                        marketing purposes without your explicit consent.</p>
                </div>
            </section>

            <section id="data-protection">
                <h2>4. Data Protection & Security</h2>
                <p>
                    We implement appropriate technical and organizational measures to protect your personal information
                    against unauthorized access, alteration, disclosure, or destruction. These measures include:
                </p>
                <ul>
                    <li>Encryption of data in transit using SSL/TLS protocols</li>
                    <li>Secure storage of sensitive information with encryption at rest</li>
                    <li>Regular security assessments and vulnerability testing</li>
                    <li>Access controls and authentication mechanisms</li>
                    <li>Employee training on data protection and privacy practices</li>
                    <li>Regular backups and disaster recovery procedures</li>
                </ul>
                <p>
                    While we strive to protect your personal information, no method of transmission over the internet or
                    electronic storage is 100% secure. We cannot guarantee absolute security but are committed to
                    maintaining industry-standard security practices.
                </p>
            </section>

            <section id="third-party">
                <h2>5. Third-Party Services</h2>
                <p>We may share your information with trusted third-party service providers who assist us in operating
                    our website and conducting our business:</p>
                <ul>
                    <li><strong>Payment Processors:</strong> To process transactions securely</li>
                    <li><strong>Email Service Providers:</strong> To send communications and newsletters</li>
                    <li><strong>Analytics Services:</strong> To analyze website traffic and user behavior (e.g., Google
                        Analytics)</li>
                    <li><strong>Cloud Hosting Providers:</strong> To store and manage data securely</li>
                    <li><strong>Customer Support Tools:</strong> To provide efficient customer service</li>
                </ul>
                <p>
                    These third parties are contractually obligated to protect your information and use it only for the
                    purposes we specify. We do not authorize them to use or disclose your personal information except as
                    necessary to perform services on our behalf or comply with legal requirements.
                </p>
            </section>

            <section id="user-rights">
                <h2>6. Your Rights</h2>
                <p>You have the following rights regarding your personal information:</p>
                <ul>
                    <li><strong>Access:</strong> Request access to the personal information we hold about you</li>
                    <li><strong>Correction:</strong> Request correction of inaccurate or incomplete information</li>
                    <li><strong>Deletion:</strong> Request deletion of your personal information (subject to legal
                        obligations)</li>
                    <li><strong>Portability:</strong> Request a copy of your data in a structured, machine-readable
                        format</li>
                    <li><strong>Objection:</strong> Object to processing of your personal information for certain
                        purposes</li>
                    <li><strong>Restriction:</strong> Request restriction of processing in certain circumstances</li>
                    <li><strong>Withdraw Consent:</strong> Withdraw consent for marketing communications at any time
                    </li>
                </ul>
                <p>
                    To exercise any of these rights, please contact us using the information provided in the Contact
                    section below. We will respond to your request within 30 days.
                </p>
            </section>

            <section id="contact">
                <div class="contact-section">
                    <h3>7. Contact Us</h3>
                    <p>If you have any questions, concerns, or requests regarding this Privacy Policy or our data
                        practices, please contact us:</p>
                    <p><strong>EL Kayan Real Estate</strong></p>
                    <p><i class="bi bi-envelope-fill me-2"></i>Email: <a
                            href="mailto:privacy@elkayan.com">privacy@elkayan.com</a></p>
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