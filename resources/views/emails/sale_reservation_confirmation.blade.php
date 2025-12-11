<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sale Reservation Confirmed</title>
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
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
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
        .greeting {
            font-size: 18px;
            color: #333;
            margin-bottom: 20px;
        }
        .property-card {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
        }
        .property-image {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        .property-details {
            margin: 15px 0;
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
        .meeting-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .meeting-box h3 {
            margin: 0 0 10px;
            color: #856404;
            font-size: 18px;
        }
        .meeting-box p {
            margin: 5px 0;
            color: #856404;
            font-size: 16px;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
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
        .footer p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>🏡 Purchase Reservation Confirmed!</h1>
            <p>Your property viewing has been scheduled</p>
        </div>
        
        <div class="content">
            <p class="greeting">Dear {{ $user->name }},</p>
            
            <p>Congratulations! Your purchase reservation has been confirmed. We're excited to help you find your dream property.</p>
            
            <div class="property-card">
                @if($property->image)
                    <img src="{{ asset($property->image) }}" alt="{{ $property->category }}" class="property-image">
                @endif
                
                <div class="property-details">
                    <div class="detail-row">
                        <span class="detail-label">Property Type:</span>
                        <span class="detail-value">{{ $property->category }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Location:</span>
                        <span class="detail-value">{{ $property->location }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Sale Price:</span>
                        <span class="detail-value">${{ number_format($property->price, 2) }}</span>
                    </div>
                    @if($property->installment_years > 0)
                        <div class="detail-row">
                            <span class="detail-label">Installment Option:</span>
                            <span class="detail-value">Available ({{ $property->installment_years }} years)</span>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="meeting-box">
                <h3>📍 Viewing Appointment</h3>
                <p><strong>Date & Time:</strong> {{ $reservation->getMeetingDateFormatted() }}</p>
                <p>Please arrive on time. Our agent will be waiting to show you the property and discuss purchase options.</p>
            </div>
            
            @if($reservation->notes)
                <div style="margin: 20px 0; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                    <strong>Your Notes:</strong>
                    <p style="margin: 10px 0 0; color: #666;">{{ $reservation->notes }}</p>
                </div>
            @endif
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ route('properties.show', $property->id) }}" class="cta-button">View Property Details</a>
            </div>
            
            <div style="background: #e8f5e9; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <h4 style="margin: 0 0 10px; color: #2e7d32;">What to Bring:</h4>
                <ul style="margin: 0; padding-left: 20px; color: #2e7d32;">
                    <li>Valid identification documents</li>
                    <li>Proof of income or financial capability</li>
                    <li>List of questions about the property</li>
                    <li>Your preferred payment method details</li>
                </ul>
            </div>
            
            <div style="background: #e3f2fd; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <h4 style="margin: 0 0 10px; color: #1565c0;">Next Steps:</h4>
                <ol style="margin: 0; padding-left: 20px; color: #1565c0;">
                    <li>Attend the scheduled viewing</li>
                    <li>Review property documents and legal papers</li>
                    <li>Discuss payment terms and financing options</li>
                    <li>Make your purchase decision</li>
                </ol>
            </div>
            
            <p style="margin-top: 30px; color: #666;">If you need to reschedule or have any questions, please contact us immediately.</p>
        </div>
        
        <div class="footer">
            <p><strong>EL Kayan Real Estate</strong></p>
            <p>Your trusted partner in property investment</p>
            <p style="margin-top: 15px;">© {{ date('Y') }} EL Kayan. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
