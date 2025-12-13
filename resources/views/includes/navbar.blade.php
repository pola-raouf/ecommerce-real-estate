@php
    // Default values - can be overridden when including
    $showNotifications = $showNotifications ?? true;
    $showSettings = $showSettings ?? false;
    $showDashboard = $showDashboard ?? true;
    $dashboardLabel = $dashboardLabel ?? 'Dashboard';
@endphp

<!-- ================= NAVBAR ================= -->
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
                <!-- Home -->
                <li class="nav-item">
                    <a class="nav-link fw-semibold {{ Request::is('/') ? 'active' : '' }}" href="{{ url('/') }}">
                        <i class="bi bi-house-door me-1"></i>Home
                    </a>
                </li>

                <!-- About Us -->
                <li class="nav-item">
                    <a class="nav-link fw-semibold {{ Request::is('about-us') ? 'active' : '' }}" href="{{ route('about-us') }}">
                        <i class="bi bi-info-circle me-1"></i>About Us
                    </a>
                </li>

                <!-- Properties -->
                <li class="nav-item">
                    <a class="nav-link fw-semibold {{ Request::is('properties') || Request::is('properties/*') ? 'active' : '' }}" href="{{ route('properties.index') }}">
                        <i class="bi bi-building me-1"></i>Properties
                    </a>
                </li>

                @auth
                    @if($showDashboard && in_array(auth()->user()->role, ['admin', 'seller']))
                        <li class="nav-item">
                            <a class="nav-link fw-semibold {{ Request::is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                <i class="bi bi-speedometer2 me-1"></i>{{ $dashboardLabel }}
                            </a>
                        </li>
                    @endif

                    @if($showSettings)
                        <!-- Settings Dropdown -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle fw-semibold d-flex align-items-center {{ Request::is('users-management') || Request::is('property-management') ? 'active' : '' }}" 
                               href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-gear me-1"></i>Settings
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                @if(auth()->user()->role === 'admin')
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center" href="{{ route('users-management') }}">
                                            <i class="bi bi-people-fill me-2"></i>Users Management
                                        </a>
                                    </li>
                                @endif
                                <li>
                                    <a class="dropdown-item d-flex align-items-center" href="{{ route('property-management') }}">
                                        <i class="bi bi-building-fill me-2"></i>Property Management
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endif

                    @if($showNotifications)
                        <!-- Notification Bell -->
                        <li class="nav-item dropdown position-relative">
                            <a class="nav-link notification-bell" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" id="notificationBell">
                                <i class="bi bi-bell-fill"></i>
                                <span class="notification-badge" id="notificationBadge" style="display: none;">0</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end notification-dropdown-menu" style="width: 350px;">
                                <li class="notification-dropdown-header">
                                    <h6><i class="bi bi-bell-fill me-2"></i>Notifications</h6>
                                    <small class="text-muted" id="notificationCount">0 new</small>
                                </li>
                                <div id="notificationList" style="max-height: 300px; overflow-y: auto;">
                                    <li class="text-center py-3 text-muted">
                                        <i class="bi bi-bell-slash"></i>
                                        <p class="mb-0 small">No new notifications</p>
                                    </li>
                                </div>
                                <li class="notification-dropdown-footer">
                                    <a href="{{ route('notifications.index') }}">
                                        <i class="bi bi-arrow-right me-1"></i>View All Notifications
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endif

                    <!-- User Profile Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="{{ Auth::user()->profile_image_url }}" alt="{{ Auth::user()->name }}" class="rounded-circle profile-img me-2">
                            <span>{{ Auth::user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item d-flex align-items-center" href="{{ route('profile') }}">
                                    <i class="bi bi-person-circle me-2"></i>Profile
                                </a>
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item d-flex align-items-center">
                                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                                    </button>
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

@if($showNotifications)
@auth
<script>
    // Notification system
    function fetchNotifications() {
        fetch('{{ route("notifications.recent") }}')
            .then(response => response.json())
            .then(data => {
                const badge = document.getElementById('notificationBadge');
                const count = document.getElementById('notificationCount');
                const list = document.getElementById('notificationList');
                
                // Update badge
                if (badge && count) {
                    if (data.unread_count > 0) {
                        badge.textContent = data.unread_count;
                        badge.style.display = 'block';
                        count.textContent = data.unread_count + ' new';
                    } else {
                        badge.style.display = 'none';
                        count.textContent = '0 new';
                    }
                }
                
                // Update notification list
                if (list) {
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
                }
            })
            .catch(error => console.error('Error fetching notifications:', error));
    }
    
    // Fetch notifications on page load
    if (document.getElementById('notificationBell')) {
        fetchNotifications();
        // Refresh notifications every 30 seconds
        setInterval(fetchNotifications, 30000);
    }
</script>
@endauth
@endif

<script>
    // Navbar scroll effect
    const navbar = document.getElementById('mainNavbar');
    if (navbar) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                navbar.classList.add('navbar-scrolled');
            } else {
                navbar.classList.remove('navbar-scrolled');
            }
        });
    }
</script>

