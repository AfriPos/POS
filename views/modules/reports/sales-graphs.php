<?php
// Set the initial and final dates based on the parameters or default values
if (isset($_GET["initialDate"])) {
    $initialDate = $_GET["initialDate"];
    $finalDate = $_GET["finalDate"];
} else {
    $initialDate = null; // Set your default initial date here
    $finalDate = null; // Set your default final date here
}

// Fetch the data for the graph using the provided parameters
$answer = PaymentController::ctrSalesDatesRange($initialDate, $finalDate);

// Prepare the chart labels and sales data
$labels = array();
$data = array();

foreach ($answer as $row) {
    $date = $row['startdate'];
    $total = $row['total'];
    $formattedDate = date('Y-m-d', strtotime($date));
    
    if (!in_array($formattedDate, $labels)) {
        $labels[] = $formattedDate;
        $data[] = $total;
    } else {
        $index = array_search($formattedDate, $labels);
        $data[$index] += $total;
    }
}

$datasets = array(
    array(
        'label' => 'Total Daily Income',
        'data' => $data,
        'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
        'borderColor' => 'rgba(255, 99, 132, 1)',
        'borderWidth' => 1,
        'pointRadius' => 3,
        'pointBackgroundColor' => 'rgba(255, 99, 132, 1)',
        'pointBorderColor' => '#fff',
        'pointHoverRadius' => 5,
        'pointHoverBackgroundColor' => 'rgba(255, 99, 132, 1)',
        'pointHoverBorderColor' => 'rgba(220,220,220,1)',
        'pointHitRadius' => 10,
        'pointBorderWidth' => 2,
        'fill' => false,
        'lineTension' => 0
    )
);
?>


<!-- BAR CHART -->
<div class="card">
  <div class="card-header">
    <h3 class="card-title">Sales Graph</h3>

    <div class="card-tools">
      <button type="button" class="btn btn-tool" data-card-widget="collapse">
        <i class="fas fa-minus"></i>
      </button>
      <button type="button" class="btn btn-tool" data-card-widget="remove">
        <i class="fas fa-times"></i>
      </button>
    </div>
  </div>
  <div class="card-body">
    <div class="chart">
        <canvas id="salesChart"></canvas>
    </div>
  </div>
  <!-- /.card-body -->
</div>
<!-- /.card -->

<script>
    var ctx = document.getElementById("salesChart").getContext("2d");
    var salesChart = new Chart(ctx, {
        type: "bar",
        data: {
            labels: <?= json_encode($labels) ?>,
            datasets: <?= json_encode($datasets) ?>
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    display: true,
                    stacked: false,
                    title: {
                        display: true,
                        text: "Date"
                    }
                },
                y: {
                    display: true,
                    stacked: false,
                    title: {
                        display: true,
                        text: "Total Income"
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: "top" // Place legends at the top
                }
            }
        }
    });

    function minimizeChart() {
        var chartCanvas = $("#salesChart");
        if (chartCanvas.css("display") === "none") {
            chartCanvas.fadeIn(500); // Show the chart canvas with a fade-in animation
        } else {
            chartCanvas.fadeOut(500); // Hide the chart canvas with a fade-out animation
        }
    }

    function closeChart() {
        var chartContainer = document.getElementById("chartContainer");
        chartContainer.style.opacity = "0"; // Set opacity to 0 for a smooth transition
        setTimeout(function() {
            chartContainer.parentNode.removeChild(chartContainer);
        }, 500); // Remove the chart container after the transition duration (500ms)
    }
</script>