<?php
$item = null;
$value = null;
$sales = PaymentController::ctrShowInvoices($item, $value);
$seller = userController::ctrShowUsers($item, $value);

$sellerArray = array();
$subtotalArray = array();

foreach ($sales as $salesvalue) {
    $sellerId = $salesvalue['userId'];

    // Find the seller in the $seller array
    $matchingSeller = array_filter($seller, function ($sellerValue) use ($sellerId) {
        return $sellerValue['userId'] == $sellerId;
    });

    if (!empty($matchingSeller)) {
        $sellervalue = reset($matchingSeller);
        $sellerName = $sellervalue['name'];
        $subtotal = $salesvalue['subtotal'];

        $sellerArray[$sellerId] = $sellerName;

        if (!isset($subtotalArray[$sellerId])) {
            $subtotalArray[$sellerId] = 0;
        }

        $subtotalArray[$sellerId] += $subtotal;
    }
}

arsort($subtotalArray); // Sort the subtotal array in descending order based on sales

$topSellers = array_slice($subtotalArray, 0, 5, true); // Get the top 5 sellers (you can adjust the number as needed)

$noRepeat = array_values($sellerArray);

?>

<!-- BAR CHART -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Top Sellers</h3>

        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="chart">
            <canvas id="barChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
        </div>
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->

<script>
    //-------------
    //- BAR CHART -
    //-------------
    var barChartCanvas = $('#barChart').get(0).getContext('2d');

    var sellerNames = <?php echo json_encode($noRepeat); ?>;
    var sellerSales = <?php echo json_encode(array_values($topSellers)); ?>;

    var barChartData = {
        labels: sellerNames,
        datasets: [{
            label: 'Total Sales',
            backgroundColor: 'rgba(60,141,188,0.9)',
            borderColor: 'rgba(60,141,188,0.8)',
            pointRadius: false,
            pointColor: '#3b8bba',
            pointStrokeColor: 'rgba(60,141,188,1)',
            pointHighlightFill: '#fff',
            pointHighlightStroke: 'rgba(60,141,188,1)',
            data: sellerSales
        }]
    };

    var barChartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        datasetFill: false
    };

    new Chart(barChartCanvas, {
        type: 'bar',
        data: barChartData,
        options: barChartOptions
    });
</script>
