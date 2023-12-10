<?php
class reportsModel {
    
    /*=============================================
    SHOW A GENERAL REPORT
    =============================================*/

    static public function mdlShowGeneralreport($table, $storeid) {

        $stmt = connection::connect()->prepare("SELECT DATE_FORMAT(Date, '%Y-%m-%d') as SaleDate, SUM(TotalAmount) as TotalSales FROM $table WHERE storeid = :storeid GROUP BY DATE_FORMAT(Date, '%Y-%m-%d') ORDER BY Date ASC");

        $stmt->bindParam(":storeid", $storeid, PDO::PARAM_STR);

        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt->closeCursor();

        $stmt = null;

        return $result;
    }
    
    /*=============================================
	SHOW A DETAILED REPORT
	=============================================*/

    static public function mdlShowDetailedreport($table, $storeid, $parameters) {
        $query = "SELECT DATE_FORMAT($table.Date, '%Y-%m-%d') as SaleDate, SUM($table.TotalAmount) as TotalSales, SUM(si.Quantity) AS MonthlySoldUnits FROM $table";
    
        // Dynamically add conditions based on parameters
        if (isset($parameters['reportclass']) && strpos($parameters['reportclass'], 'customer') === 0) {
            $query .= " JOIN saleitems si ON $table.SaleID = si.SaleID";
        }
        // Dynamically add conditions based on parameters
        if (isset($parameters['reportclass']) && strpos($parameters['reportclass'], 'category') === 0) {
            $query .= "  JOIN saleitems si ON $table.SaleID = si.SaleID JOIN products p ON si.ProductID = p.id JOIN categories c ON p.idCategory = c.id";
        }
        // Dynamically add conditions based on parameters
        if (isset($parameters['reportclass']) && strpos($parameters['reportclass'], 'product') === 0) {
            $query .= " JOIN saleitems si ON $table.SaleID = si.SaleID JOIN products p ON si.ProductID = p.id";
        }
        // Dynamically add conditions based on parameters
        if ((isset($parameters['reportclass']) && (strpos($parameters['reportclass'], 'store') === 0 || strpos($parameters['reportclass'], 'employee') === 0)) ) {
            $query .= " JOIN saleitems si ON $table.SaleID = si.SaleID";
        }
        // Dynamically add conditions based on parameters
        if (!isset($parameters['reportclass']) && isset($_GET['startdate']) && $_GET['enddate']) {
            $query .= " JOIN saleitems si ON $table.SaleID = si.SaleID";
        }


        // Dynamically add conditions based on parameters
        if (isset($parameters['reportclass']) && $parameters['reportclass'] === 'monthlygsi') {
            if ($storeid !== null) {
                $query .= " JOIN saleitems si ON $table.SaleID = si.SaleID";
                $query .= " WHERE MONTH($table.Date) = MONTH(CURRENT_DATE()) AND YEAR($table.Date) = YEAR(CURRENT_DATE()) AND $table.storeid = :storeid";
            } else{
                $query .= " JOIN saleitems si ON $table.SaleID = si.SaleID";
                $query .= " WHERE MONTH($table.Date) = MONTH(CURRENT_DATE()) AND YEAR($table.Date) = YEAR(CURRENT_DATE())";
            }
        } elseif (isset($parameters['reportclass']) && $parameters['reportclass'] === 'monthlygsi' && $storeid !== null) {
            $query .= " WHERE MONTH($table.Date) = MONTH(CURRENT_DATE()) AND YEAR($table.Date) = YEAR(CURRENT_DATE()) AND $table.storeid = :storeid";
        } else {
            if ($storeid !== null) {
                $query .= " WHERE $table.storeid = :storeid";
            }
        }

        // Dynamically add conditions based on parameters
        if (isset($parameters['startdate']) && isset($parameters['enddate'])) {
            $query .= " AND $table.Date >= :startdate AND $table.Date <= :enddate";
        }
        // Dynamically add conditions based on parameters
        if (isset($parameters['reportclass']) && strpos($parameters['reportclass'], 'customer') === 0) {
            $query .= " AND CustomerID = :customerid";
        }
        // Dynamically add conditions based on parameters
        if (isset($parameters['reportclass']) && strpos($parameters['reportclass'], 'category') === 0) {
            $query .= " AND c.id = :categoryid";
        }
        // Dynamically add conditions based on parameters
        if (isset($parameters['reportclass']) && strpos($parameters['reportclass'], 'product') === 0) {
            $query .= " AND p.id = :productid";
        }        // Dynamically add conditions based on parameters
        if (isset($parameters['reportclass']) && strpos($parameters['reportclass'], 'employee') === 0) {
            $query .= " AND userId = :employeeid";
        }    

    
        // You can add more conditions for other parameters as needed
    
        // Group and order by
        $query .= " GROUP BY DATE_FORMAT($table.Date, '%Y-%m-%d') ORDER BY $table.Date ASC";
        // var_dump($query);
        $stmt = connection::connect()->prepare($query);

        if (isset($parameters['reportclass'])) {
            $reportclass = $parameters['reportclass'];
        
            // Check if $reportclass starts with "store"
            if (strpos($reportclass, 'store~') === 0) {
                // Extract storeid from the reportclass
                $store_id = substr($reportclass, strlen('store~'));
                $stmt->bindParam(":storeid", $store_id, PDO::PARAM_STR);    
            } elseif (strpos($reportclass, 'customer~') === 0) {
                // Extract storeid from the reportclass
                $customerid = intval(substr($reportclass, strlen('customer~')));
                $stmt->bindParam(":customerid", $customerid, PDO::PARAM_INT);
                $stmt->bindParam(":storeid", $storeid, PDO::PARAM_STR);
            } elseif (strpos($reportclass, 'category~') === 0) {
                // Extract storeid from the reportclass
                $categoryid = intval(substr($reportclass, strlen('category~')));
                $stmt->bindParam(":categoryid", $categoryid, PDO::PARAM_INT);
                $stmt->bindParam(":storeid", $storeid, PDO::PARAM_STR);
            } elseif (strpos($reportclass, 'product~') === 0) {
                // Extract storeid from the reportclass
                $productid = intval(substr($reportclass, strlen('product~')));
                $stmt->bindParam(":productid", $productid, PDO::PARAM_STR);
                $stmt->bindParam(":storeid", $storeid, PDO::PARAM_STR);
            } elseif (strpos($reportclass, 'employee~') === 0) {
                // Extract storeid from the reportclass
                $employeeid = intval(substr($reportclass, strlen('employee~')));
                $stmt->bindParam(":employeeid", $employeeid, PDO::PARAM_STR);
                $stmt->bindParam(":storeid", $storeid, PDO::PARAM_STR);
            } elseif (isset($parameters['reportclass']) && $parameters['reportclass'] === 'monthlygsi' && $storeid !== null) {
                $stmt->bindParam(":storeid", $storeid, PDO::PARAM_STR);                
            }else{
                // echo "................................not found..............................................................";
            }
        } else {
            // Bind common parameters
            $stmt->bindParam(":storeid", $storeid, PDO::PARAM_STR);
        }
        
        // Bind dynamic parameters
        if (isset($parameters['startdate']) && isset($parameters['enddate'])) {
            $stmt->bindParam(":startdate", $parameters['startdate'], PDO::PARAM_STR);
            $stmt->bindParam(":enddate", $parameters['enddate'], PDO::PARAM_STR);
        }
        // $stmt->debugDumpParams();
        // Execute the query
        $stmt->execute();
    
        // Fetch the results
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        // Close cursor and connection
        $stmt->closeCursor();
        $stmt = null;
    
        return $result;
    }
    

}
