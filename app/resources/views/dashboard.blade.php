<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Analytics Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: Arial, sans-serif; background: #f3f4f6; margin: 0; padding: 24px; }
        .container { max-width: 1100px; margin: 0 auto; }
        .grid { display: grid; gap: 20px; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); }
        .card { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .muted { color: #6b7280; font-size: 12px; text-transform: uppercase; letter-spacing: 0.08em; }
        .value { font-size: 32px; font-weight: bold; margin-top: 8px; }
        .chart { height: 300px; }
        ul { margin: 0; padding-left: 18px; }
        li { margin: 8px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Restaurant Analytics Dashboard</h1>
        <div class="grid" style="margin-top: 20px;">
            <div class="card">
                <div class="muted">Page Views</div>
                <div id="pageViews" class="value">0</div>
            </div>
            <div class="card">
                <div class="muted">Unique Visitors</div>
                <div id="uniqueVisitors" class="value">0</div>
            </div>
            <div class="card">
                <div class="muted">Total Events</div>
                <div id="totalEvents" class="value">0</div>
            </div>
        </div>

        <div class="grid" style="margin-top: 20px;">
            <div class="card">
                <div class="muted">Popular Pages</div>
                <ul id="popularPages"></ul>
            </div>
            <div class="card" style="grid-column: span 2;">
                <div class="muted">Event Trends</div>
                <div class="chart"><canvas id="trendChart"></canvas></div>
            </div>
        </div>
    </div>

    <script>
        const formatNumber = (value) => Number(value || 0);
        const apiUrl = '/api/analytics/dashboard?start=2026-10-01&end=2026-10-31';

        fetch(apiUrl)
            .then(response => response.json())
            .then(data => {
                document.getElementById('pageViews').innerText = formatNumber(data.summary.page_views);
                document.getElementById('uniqueVisitors').innerText = formatNumber(data.summary.unique_visitors);
                document.getElementById('totalEvents').innerText = formatNumber(data.summary.total_events);

                const popularPages = document.getElementById('popularPages');
                popularPages.innerHTML = (data.popular_pages || []).map(item => {
                    return `<li>${item.page || 'Unknown'} — ${item.count}</li>`;
                }).join('');

                const trendChart = document.getElementById('trendChart');
                const datasets = {};
                const labels = [...new Set((data.event_trend || []).map(item => item.date))].sort();

                (data.event_trend || []).forEach(item => {
                    if (!datasets[item.event_name]) {
                        datasets[item.event_name] = new Array(labels.length).fill(0);
                    }
                    const index = labels.indexOf(item.date);
                    datasets[item.event_name][index] = Number(item.count);
                });

                new Chart(trendChart, {
                    type: 'line',
                    data: {
                        labels,
                        datasets: Object.entries(datasets).map(([key, values]) => ({
                            label: key,
                            data: values,
                            borderWidth: 2,
                            fill: false,
                            tension: 0.2
                        }))
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: { beginAtZero: true }
                        }
                    }
                });
            })
            .catch(err => {
                console.error('Failed to fetch analytics', err);
            });
    </script>
</body>
</html>
