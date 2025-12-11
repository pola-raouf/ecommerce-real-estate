<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Property Available</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .email-header .icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
        .email-body {
            padding: 30px 20px;
        }
        .property-card {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #667eea;
        }
        .property-image {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border-radius: 6px;
            margin-bottom: 15px;
        }
        .property-details {
            margin: 15px 0;
        }
        .property-details h2 {
            color: #667eea;
            margin: 0 0 10px 0;
            font-size: 24px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
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
        .price {
            font-size: 28px;
            font-weight: bold;
            color: #667eea;
            margin: 15px 0;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 14px 30px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 20px 0;
            text-align: center;
            transition: transform 0.2s;
        }
        .cta-button:hover {
            transform: translateY(-2px);
        }
        .email-footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
        .badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            margin: 5px 0;
        }
        .badge-sale {
            background-color: #28a745;
            color: white;
        }
        .badge-rent {
            background-color: #17a2b8;
            color: white;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <div class="icon">🏠</div>
            <h1>New Property Available!</h1>
            <p style="margin: 10px 0 0 0; font-size: 16px;">A new property has been added to our listings</p>
        </div>

        <!-- Body -->
        <div class="email-body">
            <p style="font-size: 16px; color: #555;">Hello,</p>
            <p style="font-size: 16px; color: #555;">
                We're excited to inform you that a new property has just been added to our platform! 
                This could be the perfect opportunity you've been waiting for.
            </p>

            <div class="property-card">
                @if($property->image)
                    <img src="{{ asset($property->image) }}" alt="{{ $property->category }}" class="property-image">
                @endif

                <div class="property-details">
                    <h2>{{ $property->category }}</h2>
                    
                    <span class="badge {{ $property->transaction_type === 'sale' ? 'badge-sale' : 'badge-rent' }}">
                        For {{ ucfirst($property->transaction_type) }}
                    </span>

                    <div class="price">
                        ${{ number_format($property->price, 2) }}
                        @if($property->transaction_type === 'rent')
                            <span style="font-size: 16px; color: #666;">/month</span>
                        @endif
                    </div>

                    <div class="detail-row">
                        <span class="detail-label">📍 Location:</span>
                        <span class="detail-value">{{ $property->location }}</span>
                    </div>

                    @if($property->description)
                    <div class="detail-row">
                        <span class="detail-label">📝 Description:</span>
                        <span class="detail-value">{{ Str::limit($property->description, 100) }}</span>
                    </div>
                    @endif

                    @if($property->installment_years && $property->transaction_type === 'sale')
                    <div class="detail-row">
                        <span class="detail-label">💳 Installment:</span>
                        <span class="detail-value">{{ $property->installment_years }} years available</span>
                    </div>
                    @endif

                    <div class="detail-row">
                        <span class="detail-label">✅ Status:</span>
                        <span class="detail-value">{{ ucfirst($property->status) }}</span>
                    </div>
                </div>

                <div style="text-align: center;">
                    <a href="{{ route('properties.show', $property->id) }}" class="cta-button">
                        View Property Details
                    </a>
                </div>
            </div>

            <p style="font-size: 14px; color: #666; margin-top: 20px;">
                Don't miss out on this opportunity! Properties are being reserved quickly.
            </p>
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <p style="margin: 0 0 10px 0; font-weight: 600;">EL Kayan Real Estate</p>
            <p style="margin: 0;">Your trusted partner in finding the perfect property</p>
            <p style="margin: 10px 0 0 0; font-size: 12px; color: #999;">
                © {{ date('Y') }} EL Kayan. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
