<!-- Content Wrapper. Contains page content -->
 <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Sales</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="Home">Home</a></li>
              <li class="breadcrumb-item active">Sales reports</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
<!-- Main content -->
<div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="m-0">Total Monthly Quantity Sold per Product</h5>
                            <div class="ml-auto">
                                <select class="form-control form-control-sm" id="selectedMonth" name="selectedMonth">
                                    <?php
                                        for ($month = 1; $month <= 12; $month++) {
                                            $selected = $month == date('n') ? 'selected' : '';
                                            echo "<option value=\"$month\" $selected>" . date("F", mktime(0, 0, 0, $month, 1)) . "</option>";
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="ml-2">
                                <select class="form-control form-control-sm" id="selectedYear" name="selectedYear">
                                    <?php
                                        $currentYear = date('Y');
                                        for ($year = $currentYear; $year >= 2000; $year--) {
                                            $selected = $year == $currentYear ? 'selected' : '';
                                            echo "<option value=\"$year\" $selected>$year</option>";
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="example1" class="table-striped tables display" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Month</th>
                                        <th>Product</th>
                                        <th>Total Quantity Sold</th>
                                        <th>Total Cash Amount</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody">
                                    <?php
                                        // Fetch sales data from the database
                                        $salesData = productController::fetchSalesData(); // Implement the function to fetch sales data

                                        // Initialize variables to keep track of product quantities and cash amounts
                                        $productQuantities = array();
                                        $productCashAmounts = array();

                                        // Get the selected year and month from the form
                                        $selectedYear = $_POST['selectedYear'] ?? date('Y'); // Default to current year
                                        $selectedMonth = $_POST['selectedMonth'] ?? date('n'); // Default to current month

                                        // Filter data for the selected year and month
                                        $filteredData = array_filter($salesData, function ($row) use ($selectedYear, $selectedMonth) {
                                            $startDate = strtotime($row['startdate']);
                                            return date('Y', $startDate) == $selectedYear && date('n', $startDate) == $selectedMonth;
                                        });

                                        foreach ($filteredData as $index => $row) {
                                            $productName = $row['productName'];
                                            $quantity = $row['Quantity'];
                                            $salePrice = $row['salePrice'];
                                            $discount = $row['Discount'];

                                            // Calculate the total quantity sold and total cash amount for each product
                                            if (!isset($productQuantities[$productName])) {
                                                $productQuantities[$productName] = 0;
                                                $productCashAmounts[$productName] = 0;
                                            }
                                            $productQuantities[$productName] += $quantity;
                                            $productCashAmounts[$productName] += ($quantity * $salePrice) - $discount;
                                        }

                                        // Loop through product quantities and display them in the table
                                        $counter = 1;
                                        foreach ($productQuantities as $productName => $totalQuantity) {
                                            $totalCashAmount = $productCashAmounts[$productName];
                                            echo "<tr>";
                                            echo "<td>" . $counter . "</td>";
                                            echo "<td>" . date("F", mktime(0, 0, 0, $selectedMonth, 1)) . " $selectedYear</td>";
                                            echo "<td>" . $productName . "</td>";
                                            echo "<td>" . $totalQuantity . "</td>";
                                            echo "<td>" . number_format($totalCashAmount) . "</td>";
                                            echo "</tr>";
                                            $counter++;
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!-- /.content -->
</div>
<!-- /.content-wrapper -->
