

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Top Performing Stores</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <div class="chart-responsive">
                    <canvas id="topStorepieChart" height="150"></canvas>
                </div>
                <!-- ./chart-responsive -->
            </div>
            <!-- /.col -->
            <div class="col-md-4">
                <ul class="chart-legend clearfix">
                </ul>
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </div>
    <!-- /.card-body -->

    <!-- New Section: Display top performing stores as a list -->
    <div class="card-footer p-0">
        <ul class="nav nav-pills flex-column">
            <li class="nav-item">
                <div class="d-flex justify-content-between align-items-center p-2">
                    <div>
                        <h5 class="m-0">Store</h5>
                    </div>
                    <div>
                        <h5 class="m-0">Revenue</h5>
                    </div>
                </div>
            </li>
            <div id="top-performing-stores-container" class="container">
                <!-- The content will be dynamically populated by the AJAX request -->
            </div>
        </ul>
    </div>

    <!-- /.footer -->
</div>
<!-- /.card -->
