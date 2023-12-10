<?php
  if ((isset($_GET['startdate']) && isset($_GET['enddate'])) || isset($_GET['reportclass'])) {
    $parameters = array();

    if (isset($_GET['startdate']) && isset($_GET['enddate'])) {
        $parameters['startdate'] = $_GET['startdate'];
        $parameters['enddate'] = $_GET['enddate'];
    }
    
    if (isset($_GET['reportclass'])) {
        $parameters['reportclass'] = $_GET['reportclass'];
    }

    $sales = reportsController::ctrShowDetailedreport($parameters);
  } else {
    $sales = reportsController::ctrShowGeneralreport();
  }
  // var_dump($sales);
  if (!$sales) {
    echo'<script>
      Swal.fire({
        icon: "error",
        title: "There is no data available",
        showConfirmButton: false,
        timer: 2000 // Set the timer to 2 seconds
      }).then(function () {
        // This will execute after the SweetAlert closes
        window.location = "reports";
      });
  </script>';
  } else {
    $labels = $data = [];

    foreach ($sales as $sale) {
        $labels[] = $sale['SaleDate'];
        $data[] = $sale['TotalSales'];
    }
  }
?>
<style>
    #chartColumn {
      transition: width 0.3s ease-in-out;
    }
    .fcol {
      display: none;
    }
    .modal-open .select2-container--open {
    z-index: 9999 !important;
  }
</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Reports</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Reports</li>
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
          <!-- /.col-md-6 -->

            <div class="shadow p-3 mb-5 bg-body-tertiary rounded">
              <div class="card-header">
                <div class="modal-footer justify-content-between">
                    <h5 class="m-0" id="report-header">Sales report</h5>
                    <div class="grid gap-3">
                      <input type="text" id="daterange" placeholder="Select daterange">
                      <button type="button" class="btn btn-primary" id="toggle-view">
                        Tabular View
                      </button>
                      <button type="button" class="btn btn-primary" id="clasifyButton">
                          Report Classifications
                      </button>
                    </div>
                </div>
              </div>
              <div class="mb-4"></div> 
              <div class="card-body">
                <div class="row">
                  <div class="col-md-12"  id="chartColumn">
                    <canvas id="salesChart" class="salesChart" width="400" height="200"></canvas>
                    <table id="example1" class="salesTable table-striped tables display" style="width:100%; display: none;">
                      <thead>
                        <tr>
                          <td>SaleDate</td>
                          <td>TotalSales</td>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                          foreach ($sales as $key => $val) {
                            echo '<tr>
                              <td>' .$val["SaleDate"]. '</td>
                              <td>' .$val["TotalSales"]. '</td>
                            </tr>';
                          }
                        ?>
                      </tbody>
                    </table>
                  </div>
                  <div class="fcol"  id="clasifyColumn">
                    <div class="filters p-4">

                    <div class="container text-center">
                        <div class="row">
                            <button type="button" class="store-cl align-self-center btn shadow p-3 mb-5 bg-body-tertiary rounded-4"  data-bs-toggle="modal" data-bs-target="#storeModal">Store-Based</button>
                        </div>
                    </div>
                    <!-- Store-based select Modal -->
                    <div class="modal fade" id="storeModal" data-bs-keyboard="false" tabindex="-1" aria-labelledby="employeeLabel" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h1 class="modal-title fs-5">Select a store</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body">
                            <div class="form-group">
                              <select class="form-control select2" data-dropdown-css-class="select2-purple" style="width: 100%;" onchange="updateUrl('store', this.value), updateReportheader('store ', this)">
                                  <option value="">-- Select or search --</option>
                                  <?php
                                    $item = null;
                                    $value = null;

                                    $stores = storeController::ctrShowStores($item, $value);

                                    foreach ($stores as $key => $value) {
                                        
                                        echo '<option value="'.$value["store_id"].'">'.$value["store_name"].'</option>';

                                    }
                                  ?>
                              </select>
                            </div>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <!-- <button type="button" class="btn btn-primary">Understood</button> -->
                          </div>
                        </div>
                      </div>
                    </div>
                      
                      <!-- Customer-based  -->
                      <div class="container text-center">
                          <div class="row">
                              <button type="button" class="customer-cl align-self-center btn shadow p-3 mb-5 bg-body-tertiary rounded-4" data-bs-toggle="modal" data-bs-target="#selectcustomerModal" >Customer-Based</button>
                          </div>
                      </div>
                      <!-- Customer-based select Modal -->
                      <div class="modal fade" id="selectcustomerModal">
                        <div class="modal-dialog modal-dialog-centered">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h1 class="modal-title fs-5">Select a customer</h1>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                              <div class="form-group">
                                <select class="form-control select2" data-dropdown-css-class="select2-purple" style="width: 100%;" id="" onchange="updateUrl('customer', this.value), updateReportheader('customer ', this)">
                                    <option value="">-- Select or search --</option>
                                    <?php
                                      $item = "store_id";
                                      $value = $_SESSION['storeid'];

                                      $customers = customerController::ctrShowCustomers($item, $value);

                                      foreach ($customers as $key => $value) {
                                          
                                          echo '<option value="'.$value["customer_id"].'">'.$value["name"].'</option>';

                                      }
                                    ?>
                                </select>
                              </div>
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                              <!-- <button type="button" class="btn btn-primary">Understood</button> -->
                            </div>
                          </div>
                        </div>
                      </div>

                      <div class="container text-center">
                          <div class="row">
                              <button type="button" class="employee-cl align-self-center btn shadow p-3 mb-5 bg-body-tertiary rounded-4" data-bs-toggle="modal" data-bs-target="#selectemployeeModal"  >Employee-Based</button>
                          </div>
                      </div>
                      <!-- Employee-based select Modal -->
                      <div class="modal fade" id="selectemployeeModal" data-bs-keyboard="false" tabindex="-1" aria-labelledby="employeeLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h1 class="modal-title fs-5">Select an employee</h1>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                              <div class="form-group">
                                <select class="form-control select2" data-dropdown-css-class="select2-purple" style="width: 100%;" id="selectEmployee" onchange="updateUrl('employee', this.value), updateReportheader('employee ', this)">
                                    <option value="">-- Select or search --</option>
                                    <?php
                                      $item = "store_id";
                                      $value = $_SESSION['storeid'];

                                      $users = userController::ctrShowAllUsers($item, $value);

                                      foreach ($users as $key => $val) {

                                        if ($_SESSION['role'] == "Administrator" || $_SESSION['role'] == 404) {
                                          if ($val["role"] == "Supervisor" || $val['role'] == "Seller" || $val['role'] == "Administrator") {
                                            echo '<option value="'.$val["userId"].'">'.$val["username"].'</option>';
                                          }
                                        } elseif ($_SESSION['role'] == "Supervisor") {
                                          if ($val['role'] == "Seller") {
                                            echo '<option value="'.$val["userId"].'">'.$val["username"].'</option>';
                                          }
                                        }
                                      }
                                    ?>
                                </select>
                              </div>
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                              <!-- <button type="button" class="btn btn-primary">Understood</button> -->
                            </div>
                          </div>
                        </div>
                      </div>

                      <div class="container text-center">
                          <div class="row">
                              <button type="button" class="category-cl align-self-center btn shadow p-3 mb-5 bg-body-tertiary rounded-4" data-bs-toggle="modal" data-bs-target="#categoryModal">Category-Based</button>
                          </div>
                      </div>
                      <!-- Category-based select Modal -->
                      <div class="modal fade" id="categoryModal" data-bs-keyboard="false" tabindex="-1" aria-labelledby="employeeLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h1 class="modal-title fs-5">Select a category</h1>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                              <div class="form-group">
                                <select class="form-control select2" data-dropdown-css-class="select2-purple" style="width: 100%;" id="" onchange="updateUrl('category', this.value), updateReportheader('category ', this)">
                                    <option value="">-- Select or search --</option>
                                    <?php
                                      $item = "store_id";
                                      $value = $_SESSION['storeid'];

                                      $categories = categoriesController::ctrShowCategories($item, $value);
                                      var_dump($categories);

                                      foreach ($categories as $key => $val) {
                                          
                                          echo '<option value="'.$val["id"].'">'.$val["Category"].'</option>';

                                      }
                                    ?>
                                </select>
                              </div>
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                              <!-- <button type="button" class="btn btn-primary">Understood</button> -->
                            </div>
                          </div>
                        </div>
                      </div>

                      <div class="container text-center">
                          <div class="row">
                              <button type="button" class="product-cl align-self-center btn shadow p-3 mb-5 bg-body-tertiary rounded-4" data-bs-toggle="modal" data-bs-target="#productModal">Product-Based</button>
                          </div>
                      </div>
                      <!-- Product-based select Modal -->
                      <div class="modal fade" id="productModal" data-bs-keyboard="false" tabindex="-1" aria-labelledby="employeeLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h1 class="modal-title fs-5">Select a product</h1>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                              <div class="form-group">
                                <select class="form-control select2" data-dropdown-css-class="select2-purple" style="width: 100%;" id="" onchange="updateUrl('product', this.value), updateReportheader('product ', this)">
                                    <option value="">-- Select or search --</option>
                                    <?php
                                      $item = "store_id";
                                      $value = $_SESSION['storeid'];
                                      $order='id';
                                      $product = productController::ctrShowProducts($item, $value, $order, true);

                                      foreach ($product as $key => $val) {
                                          
                                          echo '<option value="'.$val["id"].'">'.$val["product"].'</option>';

                                      }
                                    ?>
                                </select>
                              </div>
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                              <!-- <button type="button" class="btn btn-primary">Understood</button> -->
                            </div>
                          </div>
                        </div>
                      </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
    
          </div>
          <!-- /.col-md-6 -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
<script>
// ==================== INITIALIZES THE DATERANGEPICKER ==================== //
// daterange
new Litepicker({
    element: document.getElementById('daterange'),
    setup: (picker) => {
        picker.on('selected', (startDateObj, endDateObj) => {
            const startDate = startDateObj.dateInstance;
            const endDate = endDateObj.dateInstance;

            if (startDate instanceof Date && endDate instanceof Date) {
                const formattedStartDate = startDate.toISOString().substring(0, 10);
                const formattedEndDate = endDate.toISOString().substring(0, 10);
                var baseUrl = 'index.php?route=reports';
                var currentUrl = window.location.href;

                // Check if the base URL is present
                if (currentUrl.indexOf(baseUrl) === -1) {
                    // If the base URL is not present, add it
                    window.location.href = baseUrl + `&startdate=${formattedStartDate}&enddate=${formattedEndDate}`;
                } else {
                    // Check if startdate is present
                    if (currentUrl.indexOf('startdate') === -1) {
                        // If startdate is not present, add it along with enddate
                        window.location.href = currentUrl + `&startdate=${formattedStartDate}&enddate=${formattedEndDate}`;
                    } else {
                        // If startdate is already present, update its value along with enddate
                        var updatedUrl = currentUrl.replace(/(startdate=)[^&]+/, '$1' + formattedStartDate);
                        updatedUrl = updatedUrl.replace(/(enddate=)[^&]+/, '$1' + formattedEndDate);
                        window.location.href = updatedUrl;
                    }
                }


                // window.location = `index.php?route=reports&startdate=${formattedStartDate}&enddate=${formattedEndDate}`;
            } else{
                Swal.fire({
                    title: 'Dates not set',
                    text: 'Failed to process dates',
                    icon: 'error'
                });
            }
        });
    },
    plugins: ['mobilefriendly'],
    mobilefriendly: {
        breakpoint: 480,
    },
    singleMode: false,
    tooltipText: {
        one: 'day',
        other: 'days'
    },
    tooltipNumber: (totalDays) => {
        return totalDays - 0;
    }
})

// ==================== INITIALIZES THE DATERANGEPICKER ==================== //


// ==================== TOOGLES BETWEEN TABULAR AND GRAPHICAL VIEW OF THE DATA ==================== //
$("#toggle-view").click(function () {
  // Hide both canvas and table
  $(".salesChart, .salesTable").hide();

  // Determine which view to show based on button text
  var buttonText = $(this).text().trim();
  if (buttonText === "Tabular View") {
      $(".salesTable").show();
  } else {
      $(".salesChart").show();
  }

  // Toggle button text
  $(this).text(buttonText === "Tabular View" ? "Graphical View" : "Tabular View");
});
// ==================== TOOGLES BETWEEN TABULAR AND GRAPHICAL VIEW OF THE DATA ==================== //

// ==================== UPDATES THE REPORT HEADER WHEN AN ELEMENT IS SELECTED USING MODALS ==================== //
function updateReportheader(type, selectElement) {
  // Get the selected option element
  var selectedOption = selectElement.options[selectElement.selectedIndex];

  // Get the text (username) of the selected option
  var selectedValue = "Performance on " + type + " " + selectedOption.text;

  // Set a local storage variable
  localStorage.setItem('ReportHeader', selectedValue);
}

// Call this function on page load to retrieve and update the header text
function updateHeaderTextOnLoad() {
  // Get the local storage variable
  var retrievedValue = localStorage.getItem('ReportHeader');

  // Check if there's a value
  if (retrievedValue) {
    // Update the text of the element with id 'report-header'
    document.getElementById('report-header').innerText = retrievedValue;
  }
}

var currentUrl = window.location.href;
// Check if the URL contains "reportclass"
if (currentUrl.includes('reportclass')) {
  // Call the function on page load
  updateHeaderTextOnLoad();
}
// ==================== UPDATES THE REPORT HEADER WHEN AN ELEMENT IS SELECTED USING MODALS ==================== //


// ==================== UPDATES THE URL WHEN A CLASSIFICATION IS SELECTED ==================== //
function updateUrl(reportType, elementid) {
  var baseUrl = 'index.php?route=reports';
  var currentUrl = window.location.href;

  if (currentUrl.indexOf(baseUrl) === -1) {
    // If the base URL is not present, add it
    window.location.href = baseUrl + '&reportclass=' + reportType + '~' + elementid;
  } else {
    // Check if reportclass is already in the URL
    if (currentUrl.indexOf('reportclass=') === -1) {
        // If reportclass is not present, add it to the URL
        window.location.href = currentUrl + '&reportclass=' + reportType + '~' + elementid;
    } else {
        // If reportclass is already present, update its value
        var updatedUrl = currentUrl.replace(/(\?|&)reportclass=[^&]*/, '$1reportclass=' + reportType + '~' + elementid);
        window.location.href = updatedUrl;
    }
  }
}
// ==================== UPDATES THE URL WHEN A CLASSIFICATION IS SELECTED ==================== //

// ==================== REALIGNS ELEMENTS TO FIT CLASSIFICATION BUTTON SELECTORS ==================== //
$('#clasifyButton').click(function () {
  $('#chartColumn').toggleClass('col-md-10 col-md-12');
  $('#clasifyColumn').toggleClass('col-md-2 fcol');
});
// ==================== REALIGNS ELEMENTS TO FIT CLASSIFICATION BUTTON SELECTORS ==================== //

// ==================== CREATES THE REPORT GRAPH ==================== //
// Assuming $labels and $data are already defined in PHP
var ctx = document.getElementById('salesChart').getContext('2d');

// Function to group data by intervals dynamically based on thresholds
function groupDataByDynamicInterval(labels, data) {
  var result = {};
  var interval = 'day'; // Default interval
  var tittlelabel = 'Total Sales in Ksh (Daily data)'; // Declare outside the loop

  // Check if the date range is greater than 31 days
  if (labels.length > 31) {
      interval = 'week';
      tittlelabel = 'Total Sales in Ksh (Weekly data)';
  }

  // Check if the date range is greater than 31 weeks
  if (labels.length > 31 * 7) {
      interval = 'month';
      tittlelabel = 'Total Sales in Ksh (Monthly data)';
  }

  // You can add more conditions for larger intervals if needed

  labels.forEach(function (date, index) {
    var group;

    switch (interval) {
      case 'week':
        group = moment(date, 'YYYY-MM-DD').startOf('isoWeek').format('YYYY [(W)]WW');
        break;
      case 'month':
        group = moment(date, 'YYYY-MM-DD').startOf('month').format('YYYY-MM');
        break;
      // Add more cases for larger intervals if needed
      default:
        group = date;
    }

    if (!result[group]) {
      result[group] = 0.0;
    }

    // Parse data value as a double before summing
    result[group] += parseFloat(data[index]);
    // Round to 2 decimal places
    result[group] = parseFloat(result[group].toFixed(2));
  });

  return { result, tittlelabel }; // Return both the result and the title label
}

// Group data dynamically
var { result: groupedData, tittlelabel: labell } = groupDataByDynamicInterval(<?php echo json_encode($labels); ?>, <?php echo json_encode($data); ?>);

var labels = Object.keys(groupedData);
var data = Object.values(groupedData);

var salesChart = new Chart(ctx, {
  type: 'line',
  data: {
      labels: labels,
      datasets: [{
          label: labell,
          data: data,
          backgroundColor: 'rgba(54, 162, 235, 0.5)',
          borderColor: 'rgba(54, 162, 235, 1)',
          borderWidth: 1
      }]
  },
  options: {
      scales: {
          y: {
              beginAtZero: true
          }
      }
  }
});
// ==================== CREATES THE REPORT GRAPH ==================== //
</script>