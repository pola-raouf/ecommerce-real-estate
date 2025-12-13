<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Management - EL Kayan</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/users-management.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
@php($today = now()->toDateString())
@php($today = now()->toDateString())
<body>

<!-- ================= NAVBAR ================= -->
@include('includes.navbar', ['showNotifications' => false, 'showSettings' => true, 'showDashboard' => true])

<div id="toast-container"></div>

<!-- ================= USERS MANAGEMENT CONTAINER ================= -->
<div class="users-management-container">

    <!-- LEFT PANEL: User Form -->
    <div class="user-info-panel">
        <div class="panel-header">
            <div class="panel-icon-wrapper">
                <i class="bi bi-person-plus-fill"></i>
            </div>
            <div>
                <div class="panel-title">Add New User</div>
                <p class="panel-subtitle">Create a new user account</p>
            </div>
        </div>
        <form id="add-user-form" action="{{ route('users.store') }}" method="POST">
            @csrf
            <div class="form-section">
                <div class="section-header">
                    <i class="bi bi-info-circle me-2"></i>
                    <span>Basic Information</span>
                </div>
                <div class="field-list">
                    <div class="field-group">
                        <label class="field-label">
                            <i class="bi bi-person me-1"></i>Full Name <span class="required">*</span>
                        </label>
                        <div class="field-control">
                            <input type="text" name="name" class="input-edit" placeholder="Enter full name" required>
                        </div>
                    </div>

                    <div class="field-group">
                        <label class="field-label">
                            <i class="bi bi-envelope me-1"></i>Email <span class="required">*</span>
                        </label>
                        <div class="field-control">
                            <input type="email" name="email" class="input-edit" placeholder="example@gmail.com" required>
                        </div>
                    </div>

                    <div class="field-group">
                        <label class="field-label">
                            <i class="bi bi-lock me-1"></i>Password <span class="required">*</span>
                        </label>
                        <div class="field-control">
                            <input type="password" name="password" class="input-edit" placeholder="Enter password" required>
                        </div>
                    </div>

                    <div class="field-group">
                        <label class="field-label">
                            <i class="bi bi-telephone me-1"></i>Phone <span class="required">*</span>
                        </label>
                        <div class="field-control">
                            <input type="tel" name="phone" class="input-edit" placeholder="+201234567890" required>
                        </div>
                    </div>

                    <div class="field-group">
                        <label class="field-label">
                            <i class="bi bi-shield-check me-1"></i>Role <span class="required">*</span>
                        </label>
                        <div class="field-control">
                            <select name="role" class="input-edit" required>
                                <option value="" disabled selected>Select Role</option>
                                <option value="admin">Admin</option>
                                <option value="seller">Seller</option>
                                <option value="buyer">Buyer</option>
                            </select>
                        </div>
                    </div>

                    <div class="field-group">
                        <label class="field-label">
                            <i class="bi bi-calendar me-1"></i>Birth Date <span class="required">*</span>
                        </label>
                        <div class="field-control">
                            <input type="date" name="birth_date" class="input-edit" required max="{{ $today }}">
                        </div>
                    </div>

                    <div class="field-group">
                        <label class="field-label">
                            <i class="bi bi-gender-ambiguous me-1"></i>Gender <span class="required">*</span>
                        </label>
                        <div class="field-control">
                            <select name="gender" class="input-edit" required>
                                <option value="" disabled selected>Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                    </div>

                    <div class="field-group">
                        <label class="field-label">
                            <i class="bi bi-geo-alt me-1"></i>Location <span class="required">*</span>
                        </label>
                        <div class="field-control">
                            <input type="text" name="location" class="input-edit" placeholder="Enter location" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="button-group">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Add User
                </button>
            </div>
        </form>
    </div>

    <!-- RIGHT PANEL: Users List -->
    <div class="users-list-panel">
        <!-- Fixed Title + Search -->
        <div class="users-list-header">
            <div class="header-content">
                <div class="header-icon-wrapper">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <h2 class="panel-title">Users List</h2>
                    <p class="panel-subtitle">Manage all registered users</p>
                </div>
            </div>
            <div class="search-bar" data-route="{{ route('users.search') }}">
                <i class="bi bi-search search-icon"></i>
                <input type="text" placeholder="Search users by name, email, or phone...">
            </div>
        </div>

        <!-- Scrollable Table -->
        <div class="users-table-container">
            <table class="users-table table table-hover">
                <thead>
                    <tr>
                        <th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Role</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr
                        data-id="{{ $user->id }}"
                        data-birth_date="{{ $user->birth_date }}"
                        data-gender="{{ $user->gender }}"
                        data-location="{{ $user->location }}"
                        data-role="{{ $user->role }}"
                    >
                        <td>{{ $user->id }}</td>
                        <td class="user-name">{{ $user->name }}</td>
                        <td class="user-email">{{ $user->email }}</td>
                        <td class="user-phone">{{ $user->phone }}</td>
                        <td class="user-role">{{ ucfirst($user->role) }}</td>
                        <td>
                            <button class="btn btn-secondary btn-sm edit-btn"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-danger btn-sm delete-btn" data-id="{{ $user->id }}"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- DELETE USER MODAL -->
<div id="delete-modal" class="modal-overlay" style="display:none;">
    <div class="modal delete-modal-modern">
        <div class="modal-header delete-modal-header">
            <h5 class="modal-title">Confirm Deletion</h5>
            <button type="button" class="modal-close" id="delete-close"><i class="bi bi-x-lg"></i></button>
        </div>
        <div class="modal-body delete-modal-body">
            <div class="delete-warning-icon">
                <i class="bi bi-exclamation-triangle-fill"></i>
            </div>
            <p class="delete-message">Are you sure you want to delete this user?</p>
            <p class="delete-submessage">This action cannot be undone.</p>
        </div>
        <div class="modal-actions delete-modal-actions">
            <button class="btn btn-secondary" id="delete-cancel">Cancel</button>
            <form id="delete-form" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="bi bi-trash me-2"></i>Delete
                </button>
            </form>
        </div>
    </div>
</div>

<!-- EDIT USER MODAL -->
<div id="edit-modal" class="modal-overlay" style="display:none;">
    <div class="modal edit-modal-modern">
        <div class="modal-header edit-modal-header">
            <div class="modal-header-content">
                <div class="modal-icon-wrapper edit-icon">
                    <i class="bi bi-pencil-square"></i>
                </div>
                <div>
                    <h5 class="modal-title">Edit User</h5>
                    <p class="modal-subtitle">Update user information</p>
                </div>
            </div>
            <button type="button" class="modal-close" id="edit-close"><i class="bi bi-x-lg"></i></button>
        </div>
        <div class="edit-modal-body">
            <form id="edit-user-form">
                @csrf
                <input type="hidden" id="edit-user-id">
                
                <div class="form-section">
                    <div class="section-header">
                        <i class="bi bi-person me-2"></i>
                        <span>Personal Information</span>
                    </div>
                    <div class="form-grid">
                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                <i class="bi bi-person-fill me-1"></i>Full Name <span class="required">*</span>
                            </label>
                            <input type="text" id="edit-name" class="form-control-modern" required>
                        </div>
                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                <i class="bi bi-envelope me-1"></i>Email <span class="required">*</span>
                            </label>
                            <input type="email" id="edit-email" class="form-control-modern" required>
                        </div>
                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                <i class="bi bi-lock me-1"></i>Password <span class="optional">(leave blank to keep)</span>
                            </label>
                            <input type="password" id="edit-password" class="form-control-modern" placeholder="Enter new password">
                        </div>
                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                <i class="bi bi-telephone me-1"></i>Phone <span class="required">*</span>
                            </label>
                            <input type="tel" id="edit-phone" class="form-control-modern" required>
                        </div>
                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                <i class="bi bi-shield-check me-1"></i>Role <span class="required">*</span>
                            </label>
                            <select id="edit-role" class="form-control-modern form-select-modern" required>
                                <option value="admin">Admin</option>
                                <option value="seller">Seller</option>
                                <option value="buyer">Buyer</option>
                            </select>
                        </div>
                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                <i class="bi bi-calendar me-1"></i>Birth Date <span class="required">*</span>
                            </label>
                            <input type="date" id="edit-birth_date" class="form-control-modern" required max="{{ $today }}">
                        </div>
                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                <i class="bi bi-gender-ambiguous me-1"></i>Gender <span class="required">*</span>
                            </label>
                            <select id="edit-gender" class="form-control-modern form-select-modern" required>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                <i class="bi bi-geo-alt me-1"></i>Location <span class="required">*</span>
                            </label>
                            <input type="text" id="edit-location" class="form-control-modern" required>
                        </div>
                    </div>
                </div>
                
                <div class="edit-modal-footer">
                    <button type="button" class="btn btn-cancel-modal" id="edit-cancel">
                        <i class="bi bi-x-circle me-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-save-modal">
                        <i class="bi bi-check-circle me-2"></i>Update User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap & Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/users-management.js') }}"></script>
<script src="{{ asset('js/button-loader.js') }}"></script>
</body>
</html>
