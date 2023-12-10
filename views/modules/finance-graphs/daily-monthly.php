<?php
/*=============================================
DAILY PAYMENTS HANDLER
=============================================*/
$pdo = connection::connect();
$storeid = $_SESSION['storeid'];

// Get the current month and last month
$currentMonth = date('m');
$lastMonth = date('m', strtotime('-1 month'));

// Prepare the SQL query to fetch and group the data for the current month
$currentMonthStmt = $pdo->prepare("
    SELECT DATE(PaymentDate) AS paymentdate, paymentmethod, SUM(Amount) AS totalamount
    FROM payments
    WHERE MONTH(PaymentDate) = :currentMonth
    AND StoreID = :storeid
    GROUP BY paymentdate, paymentmethod
    ORDER BY paymentdate
");
$currentMonthStmt->bindParam(':currentMonth', $currentMonth);
$currentMonthStmt->bindParam(':storeid', $storeid);
$currentMonthStmt->execute();
$currentMonthPaymentsData = $currentMonthStmt->fetchAll(PDO::FETCH_ASSOC);

// Prepare the SQL query to fetch and group the data for the last month
$lastMonthStmt = $pdo->prepare("
    SELECT DATE(PaymentDate) AS paymentdate, paymentmethod, SUM(Amount) AS totalamount
    FROM payments
    WHERE MONTH(PaymentDate) = :lastMonth
    AND StoreID = :storeid
    GROUP BY paymentdate, paymentmethod
    ORDER BY paymentdate
");
$lastMonthStmt->bindParam(':lastMonth', $lastMonth);
$lastMonthStmt->bindParam(':storeid', $storeid);
$lastMonthStmt->execute();
$lastMonthPaymentsData = $lastMonthStmt->fetchAll(PDO::FETCH_ASSOC);

// Initialize arrays to store the aggregated data for each month
$currentMonthAggregatedData = [];
$lastMonthAggregatedData = [];

// Aggregate the totals for the current month
foreach ($currentMonthPaymentsData as $row) {
    $paymentDate = $row['paymentdate'];
    $paymentMethod = $row['paymentmethod'];
    $totalAmount = $row['totalamount'];

    // Add the total amount to the respective payment method on the given date
    if (!isset($currentMonthAggregatedData[$paymentDate][$paymentMethod])) {
        $currentMonthAggregatedData[$paymentDate][$paymentMethod] = 0;
    }
    $currentMonthAggregatedData[$paymentDate][$paymentMethod] += $totalAmount;
}

// Aggregate the totals for the last month
foreach ($lastMonthPaymentsData as $row) {
    $paymentDate = $row['paymentdate'];
    $paymentMethod = $row['paymentmethod'];
    $totalAmount = $row['totalamount'];

    // Add the total amount to the respective payment method on the given date
    if (!isset($lastMonthAggregatedData[$paymentDate][$paymentMethod])) {
        $lastMonthAggregatedData[$paymentDate][$paymentMethod] = 0;
    }
    $lastMonthAggregatedData[$paymentDate][$paymentMethod] += $totalAmount;
}

// Prepare the data for the chart
$chartData = [];
$paymentMethods = [];

// Process the current month's data
foreach ($currentMonthAggregatedData as $paymentDate => $paymentMethodsData) {
    $chartData['currentMonth'][$paymentDate] = [];
    foreach ($paymentMethodsData as $paymentMethod => $totalAmount) {
        if (!in_array($paymentMethod, $paymentMethods)) {
            $paymentMethods[] = $paymentMethod;
        }
        $chartData['currentMonth'][$paymentDate][$paymentMethod] = $totalAmount;
    }
}

// Process the last month's data
foreach ($lastMonthAggregatedData as $paymentDate => $paymentMethodsData) {
    $chartData['lastMonth'][$paymentDate] = [];
    foreach ($paymentMethodsData as $paymentMethod => $totalAmount) {
        if (!in_array($paymentMethod, $paymentMethods)) {
            $paymentMethods[] = $paymentMethod;
        }
        $chartData['lastMonth'][$paymentDate][$paymentMethod] = $totalAmount;
    }
}

// Generate the labels for the x-axis (all days of the current month)
$daysInCurrentMonth = cal_days_in_month(CAL_GREGORIAN, $currentMonth, date('Y'));
$xLabels = range(1, $daysInCurrentMonth);

// Generate random colors for each payment method
$colors = array(
    'rgba(54, 162, 235, 0.5)',
    'rgba(255, 99, 132, 0.5)',
    'rgba(75, 192, 192, 0.5)',
    'rgba(255, 165, 0, 0.5)',   // Orange
    'rgba(128, 0, 128, 0.5)'    // Purple
    // Add more colors as needed
);
$bcolors = array(
    'rgba(54, 162, 235, 1)',
    'rgba(255, 99, 132, 1)',
    'rgba(75, 192, 192, 1)',
    'rgba(255, 165, 0, 1)',   // Orange
    'rgba(128, 0, 128, 1)'    // Purple
    // Add more colors as needed
);

// Generate the datasets for the current month's chart
$currentMonthDatasets = [];
foreach ($paymentMethods as $index => $paymentMethod) {
    $dataset = [
        'label' => $paymentMethod,
        'backgroundColor' => $colors[$index % count($colors)], // Assign a color from the array based on the index
        'bordercolor' => $bcolors[$index % count($bcolors)],
        'data' => []
    ];
    foreach ($xLabels as $day) {
        $dateKey = date('Y-m-d', mktime(0, 0, 0, $currentMonth, $day));
        $dataset['data'][] = $chartData['currentMonth'][$dateKey][$paymentMethod] ?? 0;
    }
    $currentMonthDatasets[] = $dataset;
}

// Generate the datasets for the last month's chart
$lastMonthDatasets = [];
foreach ($paymentMethods as $index => $paymentMethod) {
    $dataset = [
        'label' => $paymentMethod,
        'backgroundColor' => $colors[$index % count($colors)], // Assign a color from the array based on the index
        'data' => []
    ];
    foreach ($xLabels as $day) {
        $dateKey = date('Y-m-d', mktime(0, 0, 0, $lastMonth, $day));
        $dataset['data'][] = $chartData['lastMonth'][$dateKey][$paymentMethod] ?? 0;
    }
    $lastMonthDatasets[] = $dataset;
}

// The datasets for the current month's chart: $currentMonthDatasets
// The datasets for the last month's chart: $lastMonthDatasets


?>


<div class="card card-outline">
    <div class="card-header">
        <h5 class="m-0 d-flex align-items-center justify-content-between">
            Daily Payments
            <div class="d-flex">
                <button onclick="previousMonth()" class="btn btn-outline-primary btn-sm mx-1">Last Month</button>
                <button onclick="thisMonth()" class="btn btn-outline-primary btn-sm mx-1">This Month</button>
            </div>
        </h5>
    </div>
    <div class="card-body">
        <canvas id="dailymonthlypayments"></canvas>
    </div>
</div>

<script>
    // Declare the chart variables globally
var dailychart;
function thisMonth() {
    if (dailychart) {
        dailychart.destroy(); // Destroy the previous month's chart object if it exists
    }
    
    // Create a new Chart instance for the current month's data
    var ctx = document.getElementById('dailymonthlypayments').getContext('2d');
    dailychart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($xLabels); ?>,
            datasets: <?php echo json_encode($currentMonthDatasets); ?>
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Days of the Month'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Amount'
                    },
                    ticks: {
                        beginAtZero: true
                    }
                }
            }
        }
    });
}

function previousMonth() {
    if (dailychart) {
        dailychart.destroy(); // Destroy the current month's chart object if it exists
    }
    
    // Create a new Chart instance for the previous month's data
    var ctx = document.getElementById('dailymonthlypayments').getContext('2d');
    dailychart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($xLabels); ?>,
            datasets: <?php echo json_encode($lastMonthDatasets); ?>

        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Days of the Month'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Amount'
                    },
                    ticks: {
                        beginAtZero: true
                    }
                }
            }
        }
    });
}
// Add event listener to window load event
window.addEventListener('load', function () {
    thisMonth(); // Call the function to generate the monthly chart on page load
});
</script>