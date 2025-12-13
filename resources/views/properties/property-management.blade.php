<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Management - EL Kayan</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/property-management.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
<!-- ================= NAVBAR ================= -->
@include('includes.navbar', ['showNotifications' => false, 'showSettings' => true, 'showDashboard' => true])
@php $canAssignOwner = $canAssignOwner ?? false; @endphp
<div id="toast-container"></div>
<div class="users-management-container">

    <!-- ===================== LEFT PANEL: Add Property Form ===================== -->
    <div class="user-info-panel">
        <div class="panel-header">
            <div class="panel-icon-wrapper">
                <i class="bi bi-house-add-fill"></i>
            </div>
            <div>
                <div class="panel-title">Add New Property</div>
                <p class="panel-subtitle">Create a new property listing</p>
            </div>
        </div>
        <form id="add-property-form" action="{{ route('properties.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-section">
                <div class="section-header">
                    <i class="bi bi-info-circle me-2"></i>
                    <span>Basic Information</span>
                </div>
                <div class="field-list">
                    <div class="field-group">
                        <label class="field-label">
                            <i class="bi bi-tag me-1"></i>Category <span class="required">*</span>
                        </label>
                        <div class="field-control">
                            <input type="text" name="category" placeholder="e.g., Apartment" required>
                        </div>
                    </div>

                    <div class="field-group">
                        <label class="field-label">
                            <i class="bi bi-geo-alt me-1"></i>Location <span class="required">*</span>
                        </label>
                        <div class="field-control">
                            <input type="text" name="location" placeholder="e.g., Maadi" required>
                        </div>
                    </div>
                    
                    <div class="field-group">
                        <label class="field-label">
                            <i class="bi bi-currency-dollar me-1"></i>Price <span class="required">*</span>
                        </label>
                        <div class="field-control">
                            <input type="number" name="price" placeholder="1000000" min="0" required>
                        </div>
                    </div>

                    <div class="field-group">
                        <label class="field-label">
                            <i class="bi bi-arrow-left-right me-1"></i>Transaction Type <span class="required">*</span>
                        </label>
                        <div class="field-control">
                            <select name="transaction_type" id="transaction_type" class="form-select" required>
                                <option value="" disabled selected>Select Transaction Type</option>
                                <option value="sale">For Sale</option>
                                <option value="rent">For Rent</option>
                            </select>
                        </div>
                    </div>

                    <div class="field-group">
                        <label class="field-label">
                            <i class="bi bi-tag-fill me-1"></i>Status <span class="required">*</span>
                        </label>
                        <div class="field-control">
                            <select name="status" required>
                                <option value="" disabled selected>Select Status</option>
                                <option value="available">Available</option>
                                <option value="sold">Sold</option>
                                <option value="pending">Pending</option>
                            </select>
                        </div>
                    </div>

                    <div class="field-group">
                        <label class="field-label">
                            <i class="bi bi-file-text me-1"></i>Description <span class="required">*</span>
                        </label>
                        <div class="field-control">
                            <textarea name="description" rows="3" placeholder="Property description" required></textarea>
                        </div>
                    </div>

                    <div class="field-group">
                        <label class="field-label">
                            <i class="bi bi-calendar-range me-1"></i>Installment Years
                        </label>
                        <div class="field-control">
                            <input type="number" name="installment_years" min="0" placeholder="0 for cash only">
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="section-header">
                    <i class="bi bi-images me-2"></i>
                    <span>Property Images</span>
                </div>
                <div class="field-list">
                    <div class="field-group">
                        <label class="field-label">
                            <i class="bi bi-image me-1"></i>Primary Image
                        </label>
                        <div class="field-control">
                            <input type="file" id="property-image" name="image" accept="image/*">
                        </div>
                    </div>

                    <div class="field-group">
                        <label class="field-label d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-images me-1"></i>Additional Images</span>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="add-multi-image-btn">
                                <i class="bi bi-plus-circle me-1"></i>Add Image
                            </button>
                        </label>
                        <div class="field-control multi-images-wrapper" id="multi-images-wrapper">
                            <div class="multi-image-input-group mb-2 d-flex align-items-center gap-2">
                                <input type="file" class="form-control" name="multiple_images[]" accept="image/*">
                                <button type="button" class="btn btn-sm btn-danger remove-image-btn"><i class="bi bi-dash-circle"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($canAssignOwner)
            <div class="form-section">
                <div class="section-header">
                    <i class="bi bi-person me-2"></i>
                    <span>Owner Information</span>
                </div>
                <div class="field-list">
                    <div class="field-group">
                        <label class="field-label">
                            <i class="bi bi-person-badge me-1"></i>User ID
                        </label>
                        <div class="field-control">
                            <input type="number" name="user_id" placeholder="Owner User ID" min="1">
                        </div>
                    </div>
                </div>
            </div>
            @else
                <input type="hidden" name="user_id" value="{{ auth()->id() }}">
            @endif

            <div class="button-group">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Add Property
                </button>
            </div>
        </form>
    </div>

    <!-- ===================== RIGHT PANEL: Properties Table ===================== -->
    <div class="users-list-panel">
        <div class="users-list-header">
            <div class="header-content">
                <div class="header-icon-wrapper">
                    <i class="bi bi-building-fill"></i>
                </div>
                <div>
                    <h2 class="panel-title mb-0">Property Lists</h2>
                    <p class="panel-subtitle">Manage all property listings</p>
                </div>
            </div>
            <form class="search-bar">
                <i class="bi bi-search search-icon"></i>
                <input type="text" placeholder="Search properties by category, location, or ID...">
            </form>
        </div>
        <div class="users-table-container" id="properties-list">
            <table class="users-table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Category</th>
                    <th>Location</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>User ID</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($properties as $property)
                    <tr data-description="{{ $property->description }}" data-installment="{{ $property->installment_years }}" data-transaction="{{ $property->transaction_type }}">
                        <td>{{ $property->id }}</td>
                        <td>{{ $property->category }}</td>
                        <td>{{ $property->location }}</td>
                        <td>{{ number_format($property->price) }} EGP</td>
                        <td>{{ ucfirst($property->status) }}</td>
                        <td>{{ $property->user_id }}</td>
                        <td>
                            <button class="btn btn-secondary btn-sm edit-btn" data-id="{{ $property->id }}"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-danger btn-sm delete-btn" data-id="{{ $property->id }}"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- ===================== MODALS ===================== -->
@include('property-management.edit-modal')
@include('property-management.delete-modal')

<!-- ===================== SCRIPTS ===================== -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="{{ asset('js/property-management.js') }}"></script>
<script src="{{ asset('js/button-loader.js') }}"></script>
<script src="{{ asset('js/property-management/edit-modal.js') }}"></script>
<script src="{{ asset('js/property-management/delete-modal.js') }}"></script>

</body>
</html>
