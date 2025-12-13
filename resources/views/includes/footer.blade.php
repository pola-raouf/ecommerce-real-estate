<?php
// Professional footer component for EL Kayan Real Estate
?>
</main>

<!-- Professional Footer -->
<footer class="site-footer">
    <div class="footer-content">
        <div class="container">
            <div class="footer-grid">
                <!-- Company Info Column -->
                <div class="footer-column">
                    <h3>
                        <i class="bi bi-building-fill me-2"></i>EL Kayan Real Estate
                    </h3>
                    <p class="footer-description">
                        Your trusted partner in finding the perfect property. We specialize in premium real estate
                        solutions across prime locations, offering verified listings and expert guidance throughout your property journey.
                    </p>
                    <div class="social-icons">
                        <a href="#" class="social-icon facebook" aria-label="Facebook" title="Follow us on Facebook">
                            <i class="bi bi-facebook"></i>
                        </a>
                        <a href="#" class="social-icon twitter" aria-label="Twitter" title="Follow us on Twitter">
                            <i class="bi bi-twitter"></i>
                        </a>
                        <a href="#" class="social-icon instagram" aria-label="Instagram" title="Follow us on Instagram">
                            <i class="bi bi-instagram"></i>
                        </a>
                        <a href="#" class="social-icon linkedin" aria-label="LinkedIn" title="Connect on LinkedIn">
                            <i class="bi bi-linkedin"></i>
                        </a>
                    </div>
                </div>

                <!-- Quick Links Column -->
                <div class="footer-column">
                    <h3>Quick Links</h3>
                    <ul class="footer-links">
                        <li><a href="{{ url('/') }}">Home</a></li>
                        <li><a href="{{ route('about-us') }}">About Us</a></li>
                        <li><a href="{{ route('properties.index') }}">Properties</a></li>
                        @auth
                            @if(in_array(auth()->user()->role, ['admin', 'seller']))
                                <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            @endif
                            <li><a href="{{ route('profile') }}">My Profile</a></li>
                        @else
                            <li><a href="{{ route('login.form') }}">Login</a></li>
                            <li><a href="{{ route('register.form') }}">Register</a></li>
                        @endauth
                    </ul>
                </div>

                <!-- Contact Info Column -->
                <div class="footer-column">
                    <h3>Get In Touch</h3>
                    <div class="contact-item">
                        <i class="bi bi-telephone-fill"></i>
                        <div>
                            <span class="contact-label">Phone</span>
                            <a href="tel:+15551234567">+1 (555) 123-4567</a>
                        </div>
                    </div>
                    <div class="contact-item">
                        <i class="bi bi-envelope-fill"></i>
                        <div>
                            <span class="contact-label">Email</span>
                            <a href="mailto:info@elkayan.com">info@elkayan.com</a>
                        </div>
                    </div>
                    <div class="contact-item">
                        <i class="bi bi-geo-alt-fill"></i>
                        <div>
                            <span class="contact-label">Address</span>
                            <span>123 Skyline Avenue<br>Downtown, City 12345</span>
                        </div>
                    </div>
                    <div class="contact-item">
                        <i class="bi bi-clock-fill"></i>
                        <div>
                            <span class="contact-label">Business Hours</span>
                            <span>Mon - Fri: 9:00 AM - 6:00 PM<br>Sat: 10:00 AM - 4:00 PM</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Bottom -->
    <div class="footer-bottom">
        <div class="container">
            <div class="footer-bottom-content">
                <p class="copyright">
                    &copy; {{ date('Y') }} <strong>EL Kayan Real Estate</strong>. All rights reserved.
                </p>
                <ul class="footer-bottom-links">
                    <li><a href="{{ route('privacy-policy') }}">Privacy Policy</a></li>
                    <li><span class="separator">•</span></li>
                    <li><a href="{{ route('terms-of-service') }}">Terms of Service</a></li>
                    <li><span class="separator">•</span></li>
                    <li><a href="{{ route('cookie-policy') }}">Cookie Policy</a></li>
                </ul>
            </div>
        </div>
    </div>
</footer>

<!-- Back to Top Button -->
<a href="#" class="back-to-top" id="backToTop" aria-label="Back to top">
    <i class="bi bi-arrow-up"></i>
</a>

<!-- Footer JavaScript -->
<script>
    // Back to top button functionality
    const backToTop = document.getElementById('backToTop');

    window.addEventListener('scroll', () => {
        if (window.scrollY > 300) {
            backToTop.classList.add('visible');
        } else {
            backToTop.classList.remove('visible');
        }
    });

    backToTop.addEventListener('click', (e) => {
        e.preventDefault();
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
</script>

</body>

</html>
