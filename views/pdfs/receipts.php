<!DOCTYPE html>
<html>
<head>
    <style>
        @page {
            size: A7;
            margin: 0;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 10px;
        }

        .container {
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        h1 {
            text-align: center;
            font-size: 18px;
            margin-top: 0;
        }

        .info {
            margin-bottom: 10px;
        }

        .info p {
            margin: 5px 0;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .table th,
        .table td {
            padding: 5px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .table th {
            background-color: #f5f5f5;
        }

        .total {
            text-align: right;
            font-weight: bold;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Receipt</h1>

        <div class="info">
            <p>Customer: John Doe</p>
            <p>Date: <?php echo "hello world"; ?></p>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Quantity</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Item 1</td>
                    <td>2</td>
                    <td>$10</td>
                </tr>
                <tr>
                    <td>Item 2</td>
                    <td>1</td>
                    <td>$20</td>
                </tr>
                <tr>
                    <td>Item 3</td>
                    <td>3</td>
                    <td>$15</td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" class="total">Total:</td>
                    <td>$85</td>
                </tr>
            </tfoot>
        </table>

        <div class="footer">
            Thank you for your purchase!
        </div>
    </div>
</body>
</html>
