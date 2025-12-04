$(document).ready(function() {
    // ======================= Config & Elements =======================
    const pieCtx = document.getElementById('pieChart');
    const $clientSelect = $('#clientSelect');
    const $salesTableBody = $('#salesTableBody');
    const $salesTableEmpty = $('#salesTableEmpty');
    const clientDataUrl = window.dashboardConfig?.clientDataUrl || $clientSelect.data('endpoint') || '/dashboard/client-data';

    // ======================= Helper Functions =======================
    
    // Normalize sales data for table rendering
    const normalizeSales = (data) => {
        if (Array.isArray(data)) return data;
        if (data && typeof data === 'object') return Object.values(data);
        return [];
    };

    // Render the sales table
    const renderSalesTable = (data) => {
        $salesTableBody.empty();

        if (!data.length) {
            $salesTableEmpty.removeClass('d-none');
            return;
        }

        $salesTableEmpty.addClass('d-none');

        data.forEach((point, idx) => {
            $salesTableBody.append(`
                <tr>
                    <td>${point?.label || `Month ${idx + 1}`}</td>
                    <td>${point?.listings || 0}</td>
                    <td>${point?.reservations || 0}</td>
                </tr>
            `);
        });
    };

    // Extract labels from pie data
    const getPieLabels = (pie) => Object.keys(pie || {});

    // ======================= Initialize Pie Chart =======================
    const defaultColors = ['#a3c3d6','#f4b787','#cfd6e3','#d6a3c3','#87f4b7'];
    let pieData = window.dashboardConfig?.initialPie || {};
    
    const pieChart = new Chart(pieCtx, {
        type: 'doughnut',
        data: {
            labels: getPieLabels(pieData),
            datasets: [{
                data: getPieLabels(pieData).map(l => pieData[l] || 0),
                backgroundColor: defaultColors,
                borderColor: '#fff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            cutout: '65%',
            plugins: { legend: { position: 'bottom' } },
            animation: { duration: 800 }
        }
    });

    // Render initial sales table
    const initialSales = normalizeSales(window.dashboardConfig?.salesData || []);
    renderSalesTable(initialSales);

    // ======================= Client Switch Handler =======================
    if ($clientSelect.length) {
        $clientSelect.change(function() {
            const clientId = $(this).val();
            if (!clientDataUrl) return;

            $.ajax({
                url: clientDataUrl,
                method: 'GET',
                data: { id: clientId },
                success: function(res) {
                    if (res.error) return alert(res.error);

                    // ----- Pie Chart Update -----
                    const piePayload = res.pie || res.pieData || {};
                    const labels = getPieLabels(piePayload);

                    pieChart.data.labels = labels.length ? labels : ['No Data'];
                    pieChart.data.datasets[0].data = labels.length ? labels.map(l => piePayload[l] || 0) : [1];
                    pieChart.update();

                    // ----- Sales Table Update -----
                    renderSalesTable(normalizeSales(res.sales));
                },
                error: function(err) {
                    console.error('AJAX error:', err);
                }
            });
        });
    }

    // ======================= Optional Randomize Button =======================
    $('#randomizeBtn').click(function() {
        const labels = pieChart.data.labels;
        pieChart.data.datasets[0].data = labels.map(() => Math.floor(Math.random() * 30) + 10);
        pieChart.update();
    });
});
