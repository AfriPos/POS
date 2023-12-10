<?php
$item = null;
$value = null;
$sales = PaymentController::ctrShowInvoices($item, $value);
$seller = userController::ctrShowUsers($item, $value);

$fullyPaid = 0;
$partiallyPaid = 0;
$unpaid = 0;

$totalFullyPaid = 0;
$totalPartiallyPaid = 0;
$totalUnpaid = 0;

foreach ($sales as $salesvalue) {
    if ($salesvalue['dueamount'] == 0) {
        $fullyPaid++;
        $totalFullyPaid += $salesvalue['total'];
    } elseif ($salesvalue['dueamount'] == $salesvalue['total']) {
        $unpaid++;
        $totalUnpaid += $salesvalue['total'];
    } else {
        $partiallyPaid++;
        $totalPartiallyPaid += $salesvalue['total'];
    }
}
?>

<!-- BAR CHART -->
<div class="card">
  <div class="card-header">
    <h3 class="card-title">Invoice Status</h3>

    <div class="card-tools">
      <button type="button" class="btn btn-tool" data-card-widget="collapse">
        <i class="fas fa-minus"></i>
      
    </div>
  </div>
  <div class="card-body">
    <div class="chart">
      <canvas id="barChart1" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
    </div>
  </div>
  <!-- /.card-body -->
</div>
<!-- /.card -->

<script>
  //-------------
  //- BAR CHART -
  //-------------
  var barChartCanvas = $('#barChart1').get(0).getContext('2d');

  var invoiceStatusData = {
    labels: ['Fully Paid', 'Partially Paid', 'Unpaid'],
    datasets: [
      {
        label: 'Invoice Status',
        backgroundColor: ['rgba(60,141,188,0.9)', 'rgba(210, 214, 222, 1)', 'rgba(255, 99, 132, 0.9)'],
        borderColor: ['rgba(60,141,188,0.8)', 'rgba(210, 214, 222, 1)', 'rgba(255, 99, 132, 1)'],
        pointRadius: false,
        pointColor: '#3b8bba',
        pointStrokeColor: 'rgba(60,141,188,1)',
        pointHighlightFill: '#fff',
        pointHighlightStroke: 'rgba(60,141,188,1)',
        data: [<?php echo $totalFullyPaid; ?>, <?php echo $totalPartiallyPaid; ?>, <?php echo $totalUnpaid; ?>]
      }
    ],
    yAxisLabel: 'Total Amount',
    xAxisLabel: 'Invoice Status'
  };

  var barChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    scales: {
      y: {
        beginAtZero: true,
        ticks: {
          callback: function (value, index, values) {
            return '$' + value;
          }
        },
        title: {
          display: true,
          text: invoiceStatusData.yAxisLabel
        }
      },
      x: {
        title: {
          display: true,
          text: invoiceStatusData.xAxisLabel
        }
      }
    }
  };

  new Chart(barChartCanvas, {
    type: 'bar',
    data: invoiceStatusData,
    options: barChartOptions
  });
</script>
