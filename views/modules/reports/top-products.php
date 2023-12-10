<?php
$pdo=connection::connect();
$topproducts=$pdo->prepare('SELECT  p.id AS Productid, p.product AS ProductName, p.image AS ProductImage, SUM(si.Quantity) AS TotalSales FROM  sales s JOIN  saleitems si ON s.SaleID = si.SaleID JOIN  products p ON si.ProductID = p.id WHERE storeid = :storeid GROUP BY  p.product ORDER BY  TotalSales DESC LIMIT 3');
$topproducts -> bindParam(":storeid", $_SESSION['storeid'], PDO::PARAM_STR);
$topproducts->execute();
$results=$topproducts->fetchAll();
// var_dump($results);
?>
<div class="card">
  <div class="card-header border-0">
    <h3 class="card-title">Top Selling Products</h3>
    <div class="card-tools">
      <button id="topproductsPdf" class="btn btn-tool btn-sm" onclick="Extracthtmlpdf('topproductList', 'top-product-list')">
        <i class="fas fa-download"></i>
      </button>
      <button type="button" class="btn btn-tool" data-card-widget="collapse">
        <i class="fas fa-minus"></i>
      </button>
    </div>
  </div>
  <div id="topproductList" class="card-body table-responsive p-0">
    <table class="table table-striped table-valign-middle">
      <thead>
      <tr>
        <th>Product</th>
        <th>Sales</th>
        <th>View</th>
      </tr>
      </thead>
      <tbody>
      <?php
        foreach ($results as $result) {
          echo '<tr>';
          echo '<td>';
          echo '<img src="' . $result['ProductImage'] . '" class="img-circle img-size-32 mr-2">';
          echo $result['ProductName'];
          echo '</td>';
          echo '<td>';
          echo '<small class="text-success mr-1">';
          echo '</small>';
          echo $result['TotalSales'] . ' Sold';
          echo '</td>';
          echo '<td>';
          echo '<a href="index.php?route=viewproduct&product-id=' . $result['Productid'] . '&image=views/img/products/' . $result['ProductImage'] . '" class="text-muted">';
          echo '<i class="fas fa-search"></i>';
          echo '</a>';
          echo '</td>';
          echo '</tr>';
        }
      ?>
      </tbody>
    </table>
  </div>
</div>
