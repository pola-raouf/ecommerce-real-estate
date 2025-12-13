<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard - EL Kayan</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
  <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
  <link rel="stylesheet" href="{{ asset('css/footer.css') }}">
  <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="d-flex flex-column min-vh-100">

  <!-- ================= NAVBAR ================= -->
  @include('includes.navbar', ['showNotifications' => false, 'showSettings' => true, 'showDashboard' => true, 'dashboardLabel' => 'Analytics'])

  <!-- ================= MAIN CONTENT ================= -->
  <div class="dashboard-container" style="margin-top: 90px;">
    <!-- Dashboard Header -->
    <div class="dashboard-header">
      <div class="header-content">
        <div class="header-icon-wrapper">
          <i class="bi bi-speedometer2"></i>
        </div>
        <div>
          <h1 class="dashboard-title">
            @if(auth()->user()->role === 'admin')
              Analytics Dashboard
            @else
              Your Dashboard
            @endif
          </h1>
          <p class="dashboard-subtitle">Monitor your property performance and insights</p>
        </div>
      </div>
    </div>

    <!-- Metrics Section -->
    @if(auth()->user()->role === 'admin')
      <section class="metrics-section">
        <div class="metrics-grid">
          <div class="metric-card metric-primary">
            <div class="metric-icon">
              <i class="bi bi-building"></i>
            </div>
            <div class="metric-content">
              <div class="metric-label">Total Listings</div>
              <div class="metric-value" id="clientListings">{{ $totalListings }}</div>
              <div class="metric-trend">
                <i class="bi bi-arrow-up"></i>
                <span>All properties</span>
              </div>
            </div>
          </div>
          <div class="metric-card metric-secondary">
            <div class="metric-icon">
              <i class="bi bi-calendar-check"></i>
            </div>
            <div class="metric-content">
              <div class="metric-label">Total Reservations</div>
              <div class="metric-value" id="clientReservations">{{ $totalReservations }}</div>
              <div class="metric-trend">
                <i class="bi bi-arrow-up"></i>
                <span>Active bookings</span>
              </div>
            </div>
          </div>
          <div class="metric-card metric-accent">
            <div class="metric-icon">
              <i class="bi bi-people"></i>
            </div>
            <div class="metric-content">
              <div class="metric-label">Website Visitors</div>
              <div class="metric-value" id="clientVisitors">{{ $totalVisitors }}</div>
              <div class="metric-trend">
                <i class="bi bi-eye"></i>
                <span>Total views</span>
              </div>
            </div>
          </div>
        </div>
      </section>
    @else
      <section class="metrics-section">
        <div class="metrics-grid">
          <div class="metric-card metric-primary">
            <div class="metric-icon">
              <i class="bi bi-building"></i>
            </div>
            <div class="metric-content">
              <div class="metric-label">Your Listings</div>
              <div class="metric-value" id="clientListings">{{ $listings }}</div>
              <div class="metric-trend">
                <i class="bi bi-arrow-up"></i>
                <span>Properties listed</span>
              </div>
            </div>
          </div>
          <div class="metric-card metric-secondary">
            <div class="metric-icon">
              <i class="bi bi-calendar-check"></i>
            </div>
            <div class="metric-content">
              <div class="metric-label">Your Reservations</div>
              <div class="metric-value" id="clientReservations">{{ $reservations }}</div>
              <div class="metric-trend">
                <i class="bi bi-arrow-up"></i>
                <span>Active bookings</span>
              </div>
            </div>
          </div>
          <div class="metric-card metric-accent">
            <div class="metric-icon">
              <i class="bi bi-eye"></i>
            </div>
            <div class="metric-content">
              <div class="metric-label">Website Visitors</div>
              <div class="metric-value" id="clientVisitors">{{ $visitors ?? 0 }}</div>
              <div class="metric-trend">
                <i class="bi bi-graph-up"></i>
                <span>Total views</span>
              </div>
            </div>
          </div>
        </div>
      </section>
    @endif

    @if(auth()->user()->role === 'admin')
      <div class="filter-section">
        <div class="filter-card">
          <div class="filter-header">
            <i class="bi bi-funnel me-2"></i>
            <span>Filter by Client</span>
          </div>
          <select id="clientSelect" class="form-select-modern" data-endpoint="{{ route('dashboard.clientData') }}">
            <option value="">All Clients</option>
            @foreach($clients as $client)
              <option value="{{ $client->id }}">{{ $client->name }}</option>
            @endforeach
          </select>
        </div>
      </div>
    @endif

    <!-- Charts Section -->
    <section class="charts-section">
      <div class="charts-grid">
        <div class="chart-card">
          <div class="chart-header">
            <div class="chart-header-content">
              <div class="chart-icon">
                <i class="bi bi-bar-chart"></i>
              </div>
              <div>
                <h3 class="chart-title">Sales Summary</h3>
                <p class="chart-subtitle">Monthly overview of listings and reservations</p>
              </div>
            </div>
          </div>
          <div class="chart-body">
            <div class="table-wrapper">
              <table class="table-modern" id="salesTable">
                <thead>
                  <tr>
                    <th><i class="bi bi-calendar3 me-1"></i>Month</th>
                    <th><i class="bi bi-building me-1"></i>Listings</th>
                    <th><i class="bi bi-calendar-check me-1"></i>Reservations</th>
                  </tr>
                </thead>
                <tbody id="salesTableBody">
                  @foreach($salesData as $point)
                    <tr>
                      <td><strong>{{ $point['label'] ?? '—' }}</strong></td>
                      <td><span class="badge-count">{{ $point['listings'] ?? 0 }}</span></td>
                      <td><span class="badge-count">{{ $point['reservations'] ?? 0 }}</span></td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            <p id="salesTableEmpty" class="empty-message {{ count($salesData) ? 'd-none' : '' }}">
              <i class="bi bi-info-circle me-2"></i>No sales data available.
            </p>
          </div>
        </div>
        
        <div class="chart-card">
          <div class="chart-header">
            <div class="chart-header-content">
              <div class="chart-icon">
                <i class="bi bi-pie-chart"></i>
              </div>
              <div>
                <h3 class="chart-title">Property Types</h3>
                <p class="chart-subtitle">Distribution by category</p>
              </div>
            </div>
          </div>
          <div class="chart-body">
            <canvas class="canvas" id="pieChart"></canvas>
          </div>
        </div>
      </div>
    </section>
  </div>

  <!-- Professional Footer -->
  @include('includes.footer')

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
  <script>
    window.dashboardConfig = {
      initialPie: @json($pieData),
      salesData: @json($salesData),
      clientDataUrl: "{{ route('dashboard.clientData') }}"
    };
  </script>
  <script src="{{ asset('js/dashboard.js') }}"></script>

</body>

</html>
