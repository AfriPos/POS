<?php
$item = "store_id";
$value = $_SESSION['storeid'];
$order = 'id';
$product = productController::ctrShowProducts($item, $value, $order, true);

?>
 <style>
  .link  {
  text-decoration: none;
  }
</style>
<!-- PRODUCT LIST -->
<div class="card">
  <div class="card-header">
    <h3 class="card-title">Recently Added Products</h3>
    <div class="card-tools">
      <button id="recentproductsPdf" class="btn btn-tool btn-sm" onclick="Extracthtmlpdf('recentproductList', 'recent-product-list')">
        <i class="fas fa-download"></i>
      </button>
      <button type="button" class="btn btn-tool" data-card-widget="collapse">
        <i class="fas fa-minus"></i>
      </button>
    </div>
  </div>
  <!-- /.card-header -->
  <div id="recentproductList" class="card-body p-0">
    <ul class="products-list product-list-in-card pl-2 pr-2">
      <?php
      $productCount = count($product); // Get the number of products
      $productLimit = min($productCount, 5); // Limit the loop to a maximum of 10 products or the available number of products

      for ($i = 0; $i < $productLimit; $i++) {
        echo '
        <li class="item">
          <div class="product-img">
            <img src="' . $product[$i]['image'] . '" class="img-size-50">
          </div>
          <div class="product-info">
            <a href="index.php?route=viewproduct&product-id='.$product[$i]['id'].'" class="product-title link">' . $product[$i]['product'] . '
              <span class="badge badge-warning float-right">Ksh ' . $product[$i]['saleprice'] . '</span></a>
            <span class="product-description">
              ' . $product[$i]['description'] . '
            </span>
          </div>
        </li>';
      }
      ?>
    </ul>
  </div>
  <!-- /.card-body -->
  <div class="card-footer text-center">
    <a href="products" class="uppercase link">View All Products</a>
  </div>
  <!-- /.card-footer -->
</div>
<!-- /.card -->