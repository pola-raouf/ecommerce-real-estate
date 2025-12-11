<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Reservation Alert</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            padding: 40px 20px;
            text-align: center;
            color: white;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }
        .header p {
            margin: 10px 0 0;
            font-size: 16px;
            opacity: 0.9;
        }
        .content {
            padding: 40px 30px;
        }
        .alert-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .user-info {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
        }
        .property-card {
            background: #e8f5e9;
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
        }
        .property-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: 600;
            color: #555;
        }
        .detail-value {
            color: #333;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            padding: 15px 40px;
            text-decoration: none;
            border-radius: 30px;
            font-weight: 600;
            margin: 20px 0;
            text-align: center;
        }
        .footer {
            background: #f8f9fa;
            padding: 30px;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>🔔 New Reservation Alert</h1>
            <p>A customer has reserved a property</p>
        </div>
        
        <div class="content">
            <div class="alert-box">
                <h3 style="margin: 0 0 10px; color: #856404;">⚡ Action Required</h3>
                <p style="margin: 0; color: #856404;">A new {{ $property->transaction_type === 'rent' ? 'rental' : 'sale' }} reservation has been made. Please prepare for the scheduled viewing.</p>
            </div>
            
            <h3 style="color: #333;">Customer Information</h3>
            <div class="user-info">
                <div class="detail-row">
                    <span class="detail-label">Name:</span>
                    <span class="detail-value">{{ $user->name }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Email:</span>
                    <span class="detail-value">{{ $user->email }}</span>
                </div>
                @if($user->phone)
                    <div class="detail-row">
                        <span class="detail-label">Phone:</span>
                        <span class="detail-value">{{ $user->phone }}</span>
                    </div>
                @endif
            </div>
            
            <h3 style="color: #333;">Property Details</h3>
            <div class="property-card">
                @if($property->image)
                    <img src="{{ asset($property->image) }}" alt="{{ $property->category }}" class="property-image">
                @endif
                
                <div class="detail-row">
                    <span class="detail-label">Type:</span>
                    <span class="detail-value">{{ $property->category }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Location:</span>
                    <span class="detail-value">{{ $property->location }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Price:</span>
                    <span class="detail-value">${{ number_format($property->price, 2) }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Transaction:</span>
                    <span class="detail-value">{{ ucfirst($property->transaction_type) }}</span>
                </div>
            </div>
            
            @if($property->transaction_type === 'rent' && $reservation->duration_value)
                <div style="background: #e3f2fd; padding: 20px; border-radius: 8px; margin: 20px 0;">
                    <h4 style="margin: 0 0 10px; color: #1565c0;">Rental Details</h4>
                    <div class="detail-row">
                        <span class="detail-label">Start Date:</span>
                        <span class="detail-value">{{ $reservation->start_date ? $reservation->start_date->format('F j, Y') : 'Not specified' }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Duration:</span>
                        <span class="detail-value">{{ $reservation->getDurationText() }}</span>
                    </div>
                </div>
            @endif
            
            <div style="background: #fff3cd; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <h4 style="margin: 0 0 10px; color: #856404;">📅 Scheduled Viewing</h4>
                <p style="margin: 0; font-size: 18px; font-weight: 600; color: #856404;">
                    {{ $reservation->getMeetingDateFormatted() }}
                </p>
            </div>
            
            @if($reservation->notes)
                <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
                    <h4 style="margin: 0 0 10px; color: #555;">Customer Notes:</h4>
                    <p style="margin: 0; color: #666;">{{ $reservation->notes }}</p>
                </div>
            @endif
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ route('properties.show', $property->id) }}" class="cta-button">View Property</a>
            </div>
            
            <div style="background: #f8d7da; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #dc3545;">
                <h4 style="margin: 0 0 10px; color: #721c24;">⏰ Reminder</h4>
                <ul style="margin: 0; padding-left: 20px; color: #721c24;">
                    <li>Confirm the appointment with the customer</li>
                    <li>Prepare all necessary property documents</li>
                    <li>Ensure the property is ready for viewing</li>
                    <li>Be punctual for the scheduled time</li>
                </ul>
            </div>
        </div>
        
        <div class="footer">
            <p><strong>EL Kayan Real Estate - Admin Panel</strong></p>
            <p>© {{ date('Y') }} EL Kayan. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
