<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cookie Policy - EL Kayan Real Estate</title>

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
            <h1><i class="bi bi-cookie me-2"></i>Cookie Policy</h1>
            <p class="last-updated">Last Updated: December 10, 2025</p>
        </div>

        <div class="legal-content">
            <div class="table-of-contents">
                <h3>Table of Contents</h3>
                <ul>
                    <li><a href="#what-are-cookies">What Are Cookies</a></li>
                    <li><a href="#how-we-use">How We Use Cookies</a></li>
                    <li><a href="#types-of-cookies">Types of Cookies We Use</a></li>
                    <li><a href="#third-party">Third-Party Cookies</a></li>
                    <li><a href="#managing-cookies">Managing Cookies</a></li>
                    <li><a href="#updates">Updates to This Policy</a></li>
                    <li><a href="#contact">Contact Us</a></li>
                </ul>
            </div>

            <section id="what-are-cookies">
                <h2>1. What Are Cookies</h2>
                <p>
                    Cookies are small text files that are placed on your computer or mobile device when you visit a
                    website. They are widely used to make websites work more efficiently and provide information to
                    website owners.
                </p>
                <p>
                    Cookies allow websites to remember your actions and preferences (such as login, language, font size,
                    and other display preferences) over a period of time, so you don't have to keep re-entering them
                    whenever you come back to the site or browse from one page to another.
                </p>
            </section>

            <section id="how-we-use">
                <h2>2. How We Use Cookies</h2>
                <p>EL Kayan Real Estate uses cookies for the following purposes:</p>
                <ul>
                    <li><strong>Essential Functionality:</strong> To enable core website features such as user
                        authentication and account management</li>
                    <li><strong>Performance & Analytics:</strong> To understand how visitors use our website and improve
                        user experience</li>
                    <li><strong>Personalization:</strong> To remember your preferences and provide customized content
                    </li>
                    <li><strong>Security:</strong> To detect and prevent fraudulent activity and enhance website
                        security</li>
                    <li><strong>Advertising:</strong> To deliver relevant advertisements and measure their effectiveness
                    </li>
                </ul>

                <div class="highlight-box">
                    <p><strong>Note:</strong> By using our website, you consent to the use of cookies in accordance with
                        this Cookie Policy. If you do not agree to our use of cookies, you should disable them by
                        following the instructions in the "Managing Cookies" section below.</p>
                </div>
            </section>

            <section id="types-of-cookies">
                <h2>3. Types of Cookies We Use</h2>

                <h3>3.1 Strictly Necessary Cookies</h3>
                <p>
                    These cookies are essential for the website to function properly. They enable core functionality
                    such as security, network management, and accessibility. You cannot opt-out of these cookies.
                </p>
                <p><strong>Examples:</strong></p>
                <ul>
                    <li>Session cookies for user authentication</li>
                    <li>Security cookies to prevent fraudulent use</li>
                    <li>Load balancing cookies for website performance</li>
                </ul>

                <h3>3.2 Performance Cookies</h3>
                <p>
                    These cookies collect information about how visitors use our website, such as which pages are
                    visited most often. This data helps us improve how our website works.
                </p>
                <p><strong>Examples:</strong></p>
                <ul>
                    <li>Google Analytics cookies to track page views and user behavior</li>
                    <li>Heat mapping cookies to understand user interaction patterns</li>
                    <li>Error tracking cookies to identify and fix technical issues</li>
                </ul>

                <h3>3.3 Functional Cookies</h3>
                <p>
                    These cookies allow the website to remember choices you make (such as your username, language, or
                    region) and provide enhanced, personalized features.
                </p>
                <p><strong>Examples:</strong></p>
                <ul>
                    <li>Cookies that remember your login details</li>
                    <li>Cookies that store your property search preferences</li>
                    <li>Cookies that remember your display settings (e.g., dark mode)</li>
                </ul>

                <h3>3.4 Targeting/Advertising Cookies</h3>
                <p>
                    These cookies are used to deliver advertisements that are relevant to you and your interests. They
                    also help measure the effectiveness of advertising campaigns.
                </p>
                <p><strong>Examples:</strong></p>
                <ul>
                    <li>Cookies from advertising networks (e.g., Google Ads, Facebook Pixel)</li>
                    <li>Retargeting cookies to show you relevant ads on other websites</li>
                    <li>Conversion tracking cookies to measure ad performance</li>
                </ul>
            </section>

            <section id="third-party">
                <h2>4. Third-Party Cookies</h2>
                <p>
                    In addition to our own cookies, we may use various third-party cookies to report usage statistics,
                    deliver advertisements, and provide enhanced functionality.
                </p>

                <h3>4.1 Analytics Services</h3>
                <ul>
                    <li><strong>Google Analytics:</strong> Tracks website usage and provides insights into user behavior
                    </li>
                    <li><strong>Hotjar:</strong> Records user sessions to understand how visitors interact with our site
                    </li>
                </ul>

                <h3>4.2 Advertising Partners</h3>
                <ul>
                    <li><strong>Google Ads:</strong> Delivers targeted advertisements based on your interests</li>
                    <li><strong>Facebook Pixel:</strong> Tracks conversions and enables retargeting campaigns</li>
                </ul>

                <h3>4.3 Social Media</h3>
                <ul>
                    <li><strong>Facebook, Twitter, LinkedIn:</strong> Enable social sharing and track engagement</li>
                </ul>

                <p>
                    These third-party services have their own privacy policies and cookie policies. We recommend
                    reviewing their policies to understand how they use your data.
                </p>
            </section>

            <section id="managing-cookies">
                <h2>5. Managing Cookies</h2>
                <p>
                    You have the right to decide whether to accept or reject cookies. You can exercise your cookie
                    preferences by adjusting your browser settings.
                </p>

                <h3>5.1 Browser Settings</h3>
                <p>Most web browsers allow you to control cookies through their settings. Here's how to manage cookies
                    in popular browsers:</p>
                <ul>
                    <li><strong>Google Chrome:</strong> Settings → Privacy and security → Cookies and other site data
                    </li>
                    <li><strong>Mozilla Firefox:</strong> Options → Privacy & Security → Cookies and Site Data</li>
                    <li><strong>Safari:</strong> Preferences → Privacy → Manage Website Data</li>
                    <li><strong>Microsoft Edge:</strong> Settings → Cookies and site permissions → Manage and delete
                        cookies</li>
                </ul>

                <h3>5.2 Opt-Out Tools</h3>
                <p>You can also use the following tools to opt out of certain types of cookies:</p>
                <ul>
                    <li><strong>Google Analytics Opt-out:</strong> <a href="https://tools.google.com/dlpage/gaoptout"
                            target="_blank" rel="noopener noreferrer">Browser Add-on</a></li>
                    <li><strong>Network Advertising Initiative:</strong> <a
                            href="http://www.networkadvertising.org/choices/" target="_blank"
                            rel="noopener noreferrer">Opt-out Tool</a></li>
                    <li><strong>Digital Advertising Alliance:</strong> <a href="http://www.aboutads.info/choices/"
                            target="_blank" rel="noopener noreferrer">Consumer Choice Page</a></li>
                </ul>

                <h3>5.3 Impact of Disabling Cookies</h3>
                <p>
                    Please note that if you disable cookies, some features of our website may not function properly. For
                    example, you may not be able to stay logged in, save preferences, or use certain interactive
                    features.
                </p>
            </section>

            <section id="updates">
                <h2>6. Updates to This Policy</h2>
                <p>
                    We may update this Cookie Policy from time to time to reflect changes in our practices or for other
                    operational, legal, or regulatory reasons. We will notify you of any material changes by posting the
                    new Cookie Policy on this page and updating the "Last Updated" date.
                </p>
                <p>
                    We encourage you to review this Cookie Policy periodically to stay informed about how we use
                    cookies.
                </p>
            </section>

            <section id="contact">
                <div class="contact-section">
                    <h3>7. Contact Us</h3>
                    <p>If you have any questions about our use of cookies or this Cookie Policy, please contact us:</p>
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