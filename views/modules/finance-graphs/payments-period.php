
<?php
/*=============================================
PAYMENTS HANDLER
=============================================*/

$storeid = $_SESSION['storeid'];

// Prepare the query to fetch payments
$paymentsQuery = $pdo->prepare('SELECT paymentmethod, SUM(Amount) AS total_amount, MONTH(PaymentDate) AS month, YEAR(PaymentDate) AS year FROM payments WHERE StoreID = :storeid GROUP BY PaymentMethod, MONTH(PaymentDate)');
$paymentsQuery->bindParam(':storeid', $storeid);
$paymentsQuery->execute();
$paymentData = $paymentsQuery->fetchAll(PDO::FETCH_ASSOC);
// var_dump($paymentData);

// Create arrays to store the payment methods, months, and amounts
$paymentMethods = array();
$months = array();
$amounts = array();

foreach ($paymentData as $payment) {
    $paymentMethod = $payment['paymentmethod'];
    $month = $payment['month'];
    $amount = $payment['total_amount'];

    // Add the payment method to the array if it doesn't exist
    if (!in_array($paymentMethod, $paymentMethods)) {
        $paymentMethods[] = $paymentMethod;
    }

    // Add the month to the array if it doesn't exist
    if (!in_array($month, $months)) {
        $months[] = $month;
    }

    // Add the amount to the amounts array for the corresponding payment method and month
    $amounts[$paymentMethod][$month] = $amount;
}

// Define an array of colors for the datasets
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


$datasets = array();

// Iterate over the payment methods
foreach ($paymentMethods as $index => $paymentMethod) {
    $data = array();

    // Sort the months in ascending order
    sort($months);

    // Iterate over the months
    foreach ($months as $month) {
        // If the payment method and month combination has a corresponding amount, use it; otherwise, use 0
        $amount = isset($amounts[$paymentMethod][$month]) ? $amounts[$paymentMethod][$month] : 0;

        // Add the amount to the data array
        $data[] = $amount;
    }

    // Create a dataset object for Chart.js with the corresponding color
    $dataset = array(
        'label' => $paymentMethod,
        'data' => $data,
        'backgroundColor' => $colors[$index % count($colors)], // Assign a color from the array based on the index
        'bordercolor' => $bcolors[$index % count($bcolors)]
    );

    // Add the dataset to the datasets array
    $datasets[] = $dataset;
}

// Prepare the labels for Chart.js
$labels = array_map(function ($month) {
    // Convert the month number to the corresponding month name
    return date('F', mktime(0, 0, 0, $month, 1));
}, $months);

// Create the graph using Chart.js
$chartData = array(
    'type' => 'bar', // You can choose the chart type here (e.g., 'bar', 'line', 'pie', etc.)
    'data' => array(
        'labels' => $labels,
        'datasets' => $datasets
    ),
    'options' => array(
        'responsive' => true, // Make the graph responsive
        'scales' => array(
            'y' => array(
                'beginAtZero' => true // Start the y-axis from zero
            )
        )
    )
);

// Convert the data to JSON format
$chartDataJson = json_encode($chartData);
// Prepare the query to fetch payments

$paymentMethods = array_unique(array_column($paymentData, 'paymentmethod'));
$years = array_unique(array_column($paymentData, 'year'));

$paymentTotals = array();

// Initialize the payment totals for each payment method and year to zero
foreach ($paymentMethods as $paymentMethod) {
    foreach ($years as $year) {
        $paymentTotals[$paymentMethod][$year] = 0;
    }
}

// Calculate the payment totals for each payment method and year
foreach ($paymentData as $payment) {
    $paymentMethod = $payment['paymentmethod'];
    $year = $payment['year'];
    $amount = $payment['total_amount'];

    $paymentTotals[$paymentMethod][$year] += $amount;
}

// Prepare the data for the graph
$dataset = array();

foreach ($paymentMethods as $paymentMethod) {
    $data = array();
    foreach ($years as $year) {
        $data[] = $paymentTotals[$paymentMethod][$year];
    }
    $dataset[] = array(
        'label' => $paymentMethod,
        'data' => $data,
    );
}
?>


<div class="card card-outline">
    <div class="card-header">
        <h5 class="m-0 d-flex align-items-center justify-content-between">
            Payments for period (VAT included)
            <div class="d-flex">
                <button onclick="Monthlypayment()" class="btn btn-outline-primary btn-sm mx-1">Monthly</button>
                <button onclick="Yearlypayment()" class="btn btn-outline-primary btn-sm mx-1">Yearly</button>
            </div>
        </h5>
    </div>
    <div class="card-body">
        <canvas id='paymentChart'></canvas>
    </div>
</div>



<script>
/*=============================================
PAYMENTS HANDLER JS
=============================================*/
// Declare a variable to store the chart instance
var chart = null; 

function Monthlypayment() {

    // Destroy the existing chart if it exists
    if (chart) {
        chart.destroy();
    }

    var chartData = <?php echo $chartDataJson; ?>;
    var ctx = document.getElementById('paymentChart').getContext('2d');
    chart = new Chart(ctx, chartData);

}
function Yearlypayment() {

    // Destroy the existing chart if it exists
    if (chart) {
        chart.destroy();
    }

    // Get the data from PHP
    var labels = <?php echo json_encode($years); ?>;
    var chartData = <?php echo json_encode($dataset); ?>;

    // Define an array of RGBA colors
    var colors = [
    'rgba(54, 162, 235, 0.5)',
    'rgba(255, 99, 132, 0.5)',
    'rgba(75, 192, 192, 0.5)',
    'rgba(75, 192, 192, 0.7)',
    'rgba(153, 102, 255, 0.7)',
    'rgba(255, 159, 64, 0.7)',
    'rgba(255, 99, 132, 0.7)'
    // Add more colors as needed
    ];

    // Create the chart
    var ctx = document.getElementById('paymentChart').getContext('2d');
    chart = new Chart(ctx,  {
        type: 'bar',
        data: {
            labels: labels,
            datasets: chartData.map(function(dataset, index) {
                return {
                    label: dataset.label,
                    data: dataset.data,
                    backgroundColor: colors[index % colors.length],
                    borderColor: colors[index % colors.length],
                    borderWidth: 1
                };
            })
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Year'
                    }
                },
                y: {
                    suggestedMin: 0, // Set the suggested minimum value to 0
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Amount'
                    }
                }
            }
        }
    });
}

// Add event listener to window load event
window.addEventListener('load', function () {
    Monthlypayment(); // Call the function to generate the monthly chart on page load
});
</script>