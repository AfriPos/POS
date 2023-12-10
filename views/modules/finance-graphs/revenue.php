<?php

$pdo = Connection::connect();
$storeid = $_SESSION['storeid'];

// Prepare arrays to hold the data for the graph
$paymentMonths = [];
$totalAmounts = [];

// Generate all months of a year
$year = date('Y'); // Current year
for ($month = 1; $month <= 12; $month++) {
    $paymentMonths[] = date('F Y', mktime(0, 0, 0, $month, 1, $year));
    $totalAmounts[] = 0;
}

// Fetch monthly/yearly revenue data from the database
$sql = "SELECT DATE_FORMAT(PaymentDate, '%Y-%m') AS payment_month, SUM(Amount) AS total_amount FROM payments WHERE StoreID = :storeid GROUP BY DATE_FORMAT(PaymentDate, '%Y-%m')";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':storeid', $storeid);
$stmt->execute();

// Process the query results and populate the data arrays
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $monthIndex = date('n', strtotime($row['payment_month'])) - 1;
    $totalAmounts[$monthIndex] = $row['total_amount'];
}
?>

<div class="card card-outline">
    <div class="card-header">
        <h5 class="m-0 d-flex align-items-center justify-content-between">
            Monthly Recurring Revenue
            <div class="d-flex">
                <button onclick="monthlyRevenue()" class="btn btn-outline-primary btn-sm mx-1">Monthly</button>
                <button onclick="yearlyRevenue()" class="btn btn-outline-primary btn-sm mx-1">Yearly</button>
            </div>
        </h5>
    </div>
    <div class="card-body">
        <canvas id="revenueChart"></canvas>
    </div>
</div>

<script>
var revenueChart; // Global variable for the chart

function monthlyRevenue() {
    // Update chart data for monthly revenue
    var months = <?php echo json_encode($paymentMonths); ?>;
    var revenueData = <?php echo json_encode($totalAmounts); ?>;
    updateChart(months, revenueData, 'Monthly Revenue', 'line');
}

function yearlyRevenue() {
    // Fetch yearly revenue data from the database
    var years = [];
    var yearlyRevenueData = [];
    <?php
    $sql = "SELECT DATE_FORMAT(PaymentDate, '%Y') AS payment_year, SUM(Amount) AS total_amount FROM payments WHERE StoreID = :storeid GROUP BY DATE_FORMAT(PaymentDate, '%Y')";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':storeid', $storeid);
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "years.push('" . $row['payment_year'] . "');";
        echo "yearlyRevenueData.push(" . $row['total_amount'] . ");";
    }
    ?>
    if (years.length === 1) {
        // Only one year, use bar graph
        updateChart(years, yearlyRevenueData, 'Yearly Revenue', 'bar');
    } else {
        // Multiple years, use line graph
        updateChart(years, yearlyRevenueData, 'Yearly Revenue', 'line');
    }
}

function updateChart(labels, data, label, type) {
    var ctx = document.getElementById('revenueChart').getContext('2d');

    // Clear previous chart if it exists
    if (revenueChart) {
        revenueChart.destroy();
    }

    // Configure the chart
    var chartConfig = {
        type: type,
        data: {
            labels: labels,
            datasets: [{
                label: label,
                data: data,
                backgroundColor: 'rgba(0, 123, 255, 0.2)',
                borderColor: 'rgba(0, 123, 255, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                }
            }
        }
    };

    // Create the chart
    revenueChart = new Chart(ctx, chartConfig);
}

// Initial chart display (monthly revenue)
monthlyRevenue();
</script>
