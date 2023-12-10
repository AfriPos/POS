
<?php
/*=============================================
INVOICE HANDLER
=============================================*/
// Set the initial and final dates based on the parameters or default values

$initialDate = null; // Set your default initial date here
$finalDate = null; // Set your default final date here

// Fetch the data for the graph using the provided parameters
$answer = PaymentController::ctrSalesDatesRange($initialDate, $finalDate);

// Prepare the chart labels and sales data
$monthlyLabels = array();
$monthlyData = array();
$monthlyPaidData = array();
$monthlyUnpaidData = array();
$monthlyPartiallyPaidData = array();
$yearlyLabels = array();
$yearlyData = array();
$yearlyPaidData = array();
$yearlyUnpaidData = array();
$yearlyPartiallyPaidData = array();

foreach ($answer as $row) {
    $date = $row['DateCreated'];
    $total = $row['TotalAmount'];
    $dueAmount = $row['DueAmount'];
    $formattedMonth = date('F', strtotime($date));
    $formattedYear = date('Y', strtotime($date));

    // Monthly data
    if (!in_array($formattedMonth, $monthlyLabels)) {
        $monthlyLabels[] = $formattedMonth;
        $monthlyData[] = $total;
        $monthlyPaidData[] = ($dueAmount == 0) ? $total : 0;
        $monthlyUnpaidData[] = ($dueAmount == $total) ? $total : 0;
        $monthlyPartiallyPaidData[] = (abs($dueAmount) > 0 && abs($dueAmount) < $total) ? $total : 0;
    } else {
        $index = array_search($formattedMonth, $monthlyLabels);
        $monthlyData[$index] += $total;
        $monthlyPaidData[$index] += ($dueAmount == 0) ? $total : 0;
        $monthlyUnpaidData[$index] += ($dueAmount == $total) ? $total : 0;
        $monthlyPartiallyPaidData[$index] += (abs($dueAmount) > 0 && abs($dueAmount) < $total) ? $total : 0;
    }

    // Yearly data
    if (!in_array($formattedYear, $yearlyLabels)) {
        $yearlyLabels[] = $formattedYear;
        $yearlyData[] = $total;
        $yearlyPaidData[] = ($dueAmount == 0) ? $total : 0;
        $yearlyUnpaidData[] = ($dueAmount == $total) ? $total : 0;
        $yearlyPartiallyPaidData[] = (abs($dueAmount) > 0 && abs($dueAmount) < $total) ? $total : 0;
    } else {
        $index = array_search($formattedYear, $yearlyLabels);
        $yearlyData[$index] += $total;
        $yearlyPaidData[$index] += ($dueAmount == 0) ? $total : 0;
        $yearlyUnpaidData[$index] += ($dueAmount == $total) ? $total : 0;
        $yearlyPartiallyPaidData[$index] += (abs($dueAmount) > 0 && abs($dueAmount) < $total) ? $total : 0;
    }
}

?>



<div class="card card-outline">
    <div class="card-header">
        <h5 class="m-0 d-flex align-items-center justify-content-between">
            Invoicing for period (VAT included)
            <div class="d-flex">
                <button onclick="generateMonthlyIncomeChart()" class="btn btn-outline-primary btn-sm mx-1">Monthly</button>
                <button onclick="generateYearlyIncomeChart()" class="btn btn-outline-primary btn-sm mx-1">Yearly</button>
            </div>
        </h5>
    </div>
    <div class="card-body">
        <canvas id="salesChart"></canvas>
    </div>
</div>


<script>
    /*=============================================
    INVOICE HANDLER JS
    =============================================*/
    // Declare the chart variable globally
    var salesChart;

    // Function to round off a value to the nearest 1000
    function roundToNearestThousand(value) {
        return Math.round(value / 1000) * 1000;
    }

    // Function to generate the monthly income chart
    function generateMonthlyIncomeChart() {
        if (salesChart) {
            salesChart.destroy(); // Destroy the existing chart object if it exists
        }

        var ctx = document.getElementById("salesChart").getContext("2d");
        salesChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($monthlyLabels); ?>,
                datasets: [
                    {
                        label: 'Total Paid',
                        data: <?php echo json_encode($monthlyPaidData); ?>,
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Total Unpaid',
                        data: <?php echo json_encode($monthlyUnpaidData); ?>,
                        backgroundColor: 'rgba(255, 99, 132, 0.5)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Total Partially Paid',
                        data: <?php echo json_encode($monthlyPartiallyPaidData); ?>,
                        backgroundColor: 'rgba(75, 192, 192, 0.5)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Function to generate the yearly income chart
    function generateYearlyIncomeChart() {
        if (salesChart) {
            salesChart.destroy(); // Destroy the existing chart object if it exists
        }

        var ctx = document.getElementById("salesChart").getContext("2d");
        salesChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($yearlyLabels); ?>,
                datasets: [
                    {
                        label: 'Total Paid',
                        data: <?php echo json_encode($yearlyPaidData); ?>,
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Total Unpaid',
                        data: <?php echo json_encode($yearlyUnpaidData); ?>,
                        backgroundColor: 'rgba(255, 99, 132, 0.5)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Total Partially Paid',
                        data: <?php echo json_encode($yearlyPartiallyPaidData); ?>,
                        backgroundColor: 'rgba(75, 192, 192, 0.5)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function (value, index, values) {
                                return roundToNearestThousand(value);
                            }
                        }
                    }
                }
            }
        });
    
    }
// Add event listener to window load event
window.addEventListener('load', function () {
    generateMonthlyIncomeChart(); // Call the function to generate the monthly chart on page load
});
</script>