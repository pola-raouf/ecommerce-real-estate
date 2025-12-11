<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - EL Kayan</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('css/notifications.css') }}">
    <link rel="stylesheet" href="{{ asset('css/footer.css') }}">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
    </style>
</head>
<body>
    
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
                        @if(in_array(auth()->user()->role, ['admin', 'seller']))
                            <li class="nav-item">
                                <a class="nav-link fw-semibold {{ Request::is('dashboard') ? 'active' : '' }}"
                                    href="{{ route('dashboard') }}">Dashboard</a>
                            </li>
                        @endif

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

    <!-- Hero Section -->
    <div class="notifications-hero">
        <div class="container">
            <div class="hero-content">
                <div class="hero-icon">
                    <i class="bi bi-bell-fill"></i>
                </div>
                <h1 class="hero-title">Notifications Center</h1>
                <p class="hero-subtitle">Stay updated with the latest property announcements</p>
                
                <div class="hero-stats">
                    <div class="stat-item">
                        <div class="stat-number">{{ $notifications->total() }}</div>
                        <div class="stat-label">Total</div>
                    </div>
                    <div class="stat-divider"></div>
                    <div class="stat-item">
                        <div class="stat-number text-primary">{{ $unreadCount }}</div>
                        <div class="stat-label">Unread</div>
                    </div>
                    <div class="stat-divider"></div>
                    <div class="stat-item">
                        <div class="stat-number text-success">{{ $notifications->total() - $unreadCount }}</div>
                        <div class="stat-label">Read</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <main class="container my-5">
        <div class="notifications-container">
            <!-- Action Bar -->
            <div class="action-bar">
                <div class="filter-tabs">
                    <button class="filter-tab active" data-filter="all">
                        <i class="bi bi-list-ul me-2"></i>All Notifications
                    </button>
                    <button class="filter-tab" data-filter="unread">
                        <i class="bi bi-envelope me-2"></i>Unread
                        @if($unreadCount > 0)
                            <span class="badge bg-primary ms-2">{{ $unreadCount }}</span>
                        @endif
                    </button>
                    <button class="filter-tab" data-filter="read">
                        <i class="bi bi-envelope-open me-2"></i>Read
                    </button>
                </div>
                
                @if($unreadCount > 0)
                    <form action="{{ route('notifications.markAllAsRead') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-gradient">
                            <i class="bi bi-check-all me-2"></i>Mark All as Read
                        </button>
                    </form>
                @endif
            </div>
            
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show custom-alert" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if($notifications->count() > 0)
                <div class="notifications-grid">
                    @foreach($notifications as $notification)
                        <div class="notification-card {{ $notification->read_at ? 'read' : 'unread' }}" 
                             data-status="{{ $notification->read_at ? 'read' : 'unread' }}">
                            <!-- Card Header -->
                            <div class="card-header-custom">
                                <div class="notification-icon-wrapper">
                                    <div class="notification-icon-bg">
                                        <i class="bi bi-house-heart-fill"></i>
                                    </div>
                                </div>
                                <div class="notification-meta">
                                    <span class="notification-type">
                                        <i class="bi bi-megaphone-fill me-1"></i>Property Alert
                                    </span>
                                    <span class="notification-time">
                                        <i class="bi bi-clock-fill me-1"></i>
                                        {{ $notification->created_at->diffForHumans() }}
                                    </span>
                                </div>
                                @if(!$notification->read_at)
                                    <div class="unread-indicator">
                                        <span class="pulse"></span>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Card Body -->
                            <div class="card-body-custom">
                                <h3 class="notification-title">
                                    {{ $notification->data['message'] ?? 'New notification' }}
                                </h3>
                                
                                <!-- Property Details -->
                                @if(isset($notification->data['property_id']))
                                    <div class="property-preview">
                                        @if(isset($notification->data['image']) && $notification->data['image'])
                                            <div class="property-image">
                                                <img src="{{ asset($notification->data['image']) }}" 
                                                     alt="Property" 
                                                     onerror="this.src='{{ asset('images/default-property.jpg') }}'">
                                                @if(isset($notification->data['transaction_type']))
                                                    <span class="property-badge {{ $notification->data['transaction_type'] === 'sale' ? 'badge-sale' : 'badge-rent' }}">
                                                        For {{ ucfirst($notification->data['transaction_type']) }}
                                                    </span>
                                                @endif
                                            </div>
                                        @endif
                                        
                                        <div class="property-details">
                                            @if(isset($notification->data['category']))
                                                <div class="detail-item">
                                                    <i class="bi bi-building"></i>
                                                    <span>{{ $notification->data['category'] }}</span>
                                                </div>
                                            @endif
                                            
                                            @if(isset($notification->data['location']))
                                                <div class="detail-item">
                                                    <i class="bi bi-geo-alt-fill"></i>
                                                    <span>{{ $notification->data['location'] }}</span>
                                                </div>
                                            @endif
                                            
                                            @if(isset($notification->data['price']))
                                                <div class="detail-item price-item">
                                                    <i class="bi bi-cash-stack"></i>
                                                    <span class="price-value">${{ number_format($notification->data['price'], 2) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    {{-- Reservation-Specific Details --}}
                                    @if(isset($notification->data['reservation_id']))
                                        <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-top: 15px;">
                                            <h6 style="color: #667eea; font-weight: 600; margin-bottom: 10px;">
                                                <i class="bi bi-calendar-check me-1"></i>Reservation Details
                                            </h6>
                                            
                                            {{-- Customer Info (for admins/owners) --}}
                                            @if(isset($notification->data['user_name']) && in_array(auth()->user()->role, ['admin', 'seller']))
                                                <div class="detail-item" style="margin-bottom: 8px;">
                                                    <i class="bi bi-person-fill"></i>
                                                    <span><strong>Customer:</strong> {{ $notification->data['user_name'] }}</span>
                                                </div>
                                                @if(isset($notification->data['user_email']))
                                                    <div class="detail-item" style="margin-bottom: 8px;">
                                                        <i class="bi bi-envelope-fill"></i>
                                                        <span><strong>Email:</strong> {{ $notification->data['user_email'] }}</span>
                                                    </div>
                                                @endif
                                                @if(isset($notification->data['user_phone']))
                                                    <div class="detail-item" style="margin-bottom: 8px;">
                                                        <i class="bi bi-telephone-fill"></i>
                                                        <span><strong>Phone:</strong> {{ $notification->data['user_phone'] }}</span>
                                                    </div>
                                                @endif
                                            @endif
                                            
                                            {{-- Meeting Date/Time --}}
                                            @if(isset($notification->data['meeting_datetime_formatted']))
                                                <div class="detail-item" style="margin-bottom: 8px; background: #fff3cd; padding: 10px; border-radius: 5px;">
                                                    <i class="bi bi-calendar-event"></i>
                                                    <span><strong>Viewing:</strong> {{ $notification->data['meeting_datetime_formatted'] }}</span>
                                                </div>
                                            @endif
                                            
                                            {{-- Rental Details --}}
                                            @if(isset($notification->data['duration']))
                                                <div class="detail-item" style="margin-bottom: 8px;">
                                                    <i class="bi bi-hourglass-split"></i>
                                                    <span><strong>Duration:</strong> {{ $notification->data['duration'] }}</span>
                                                </div>
                                            @endif
                                            
                                            @if(isset($notification->data['start_date']))
                                                <div class="detail-item" style="margin-bottom: 8px;">
                                                    <i class="bi bi-calendar-check"></i>
                                                    <span><strong>Start Date:</strong> {{ $notification->data['start_date'] }}</span>
                                                </div>
                                            @endif
                                            
                                            {{-- Customer Notes --}}
                                            @if(isset($notification->data['notes']))
                                                <div style="margin-top: 10px; padding: 10px; background: white; border-radius: 5px; border-left: 3px solid #667eea;">
                                                    <strong style="color: #667eea;"><i class="bi bi-chat-left-text me-1"></i>Notes:</strong>
                                                    <p style="margin: 5px 0 0; color: #666;">{{ $notification->data['notes'] }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                @endif
                            </div>
                            
                            <!-- Card Footer -->
                            <div class="card-footer-custom">
                                @if(isset($notification->data['property_id']))
                                    <a href="{{ route('properties.show', $notification->data['property_id']) }}" 
                                       class="btn btn-view-property">
                                        <i class="bi bi-eye-fill me-2"></i>View Property
                                    </a>
                                @endif
                                
                                @if(!$notification->read_at)
                                    <form action="{{ route('notifications.markAsRead', $notification->id) }}" 
                                          method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-mark-read" title="Mark as read">
                                            <i class="bi bi-check2-circle"></i>
                                        </button>
                                    </form>
                                @else
                                    <span class="read-badge">
                                        <i class="bi bi-check2-all"></i> Read
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="pagination-wrapper">
                    {{ $notifications->links() }}
                </div>
            @else
                <div class="empty-state-modern">
                    <div class="empty-icon">
                        <i class="bi bi-bell-slash"></i>
                    </div>
                    <h3>No Notifications Yet</h3>
                    <p>When you receive property alerts and updates, they'll appear here.</p>
                    <a href="{{ route('properties.index') }}" class="btn btn-explore">
                        <i class="bi bi-compass me-2"></i>Explore Properties
                    </a>
                </div>
            @endif
        </div>
    </main>
    
    <!-- Footer -->
    @include('includes.footer')
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Filter Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filterTabs = document.querySelectorAll('.filter-tab');
            const notificationCards = document.querySelectorAll('.notification-card');
            
            filterTabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    // Update active tab
                    filterTabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    
                    const filter = this.dataset.filter;
                    
                    // Filter notifications
                    notificationCards.forEach(card => {
                        if (filter === 'all') {
                            card.style.display = 'block';
                        } else {
                            if (card.dataset.status === filter) {
                                card.style.display = 'block';
                            } else {
                                card.style.display = 'none';
                            }
                        }
                    });
                });
            });
            
            // Navbar scroll effect
            const navbar = document.getElementById('mainNavbar');
            window.addEventListener('scroll', () => {
                if (window.scrollY > 50) {
                    navbar.classList.add('navbar-scrolled');
                } else {
                    navbar.classList.remove('navbar-scrolled');
                }
            });
            
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
        });
    </script>
</body>
</html>
