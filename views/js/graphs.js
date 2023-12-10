const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
let lastMonthlyData = null;
let lastStoreData = null;
let chartInstance = null;

function fetchData() {
    $.ajax({
        url: 'ajax/graphs.ajax.php',
        type: 'POST',
        cache: false,
        dataType: 'json',
        success: function (graphdata) {
            // console.log(graphdata);
            //--------------------
            // - DASHBOARD TABS -
            //--------------------
            // Loop through all properties in data object
            for (let prop in graphdata) {
                // If the property value is null, set it to 0
                if (graphdata[prop] === null) {
                    graphdata[prop] = 0;
                }
            }

            // Update the frontend with the fetched data
            $('#totalProducts').text(graphdata.totalProducts);
            $('#totalMerchandise').text('Ksh ' + formatCurrency(graphdata.totalMerchandise));
            $('#totalSales').text('Ksh ' + formatCurrency(graphdata.totalSales));
            $('#totalUsers').text(graphdata.totalUsers + ' persons');
            $('#totalStores').text(graphdata.totalStores + ' stores');
            $('#totalQuantity').text(graphdata.totalQuantity + ' units');

            //--------------------------
            // - END OF DASHBOARD TABS -
            //---------------------------
            
            //--------------------
            // - FINANCE DASHBOARD TABS -
            //--------------------
            $('#totalAmount').text('Ksh ' + graphdata.totalAmount);
            $('#paymentCount').text(graphdata.paymentCount);
            $('#totalInvoiceAmount').text('Ksh ' + graphdata.totalInvoiceAmount);
            $('#invoicePaymentCount').text(graphdata.invoicePaymentCount);
            $('#totalPaidInvoiceAmount').text('Ksh ' +graphdata.totalPaidInvoiceAmount);
            $('#invoicePaidCount').text(graphdata.invoicePaidCount);
            //--------------------
            // - FINANCE DASHBOARD TABS -
            //--------------------

            //--------------------
            // - REVENUE GRAPH -
            //--------------------
            // Compare the data with the last fetched data
            if (JSON.stringify(graphdata.monthlyPayment) !== JSON.stringify(lastMonthlyData)) {
                const paymentData = graphdata.monthlyPayment;
                const expenseData = graphdata.monthlyExpense;

                const labels = paymentData.map(data => data.month);
                const paymentDataset = {
                    label: 'Revenue',
                    data: paymentData.map(data => data.total_amount),
                    borderColor: 'rgba(54, 162, 235, 0.5)',
                    fill: false
                };

                const expenseDataset = {
                    label: 'Expenses',
                    data: expenseData.map(data => data.total_expenses),
                    borderColor: 'rgba(255, 99, 132, 0.5)',
                    fill: false
                };

                const ctx = document.getElementById('monthlyLineGraph').getContext('2d');

                // Destroy the old chart instance if it exists
                if (chartInstance) {
                    chartInstance.destroy();
                }

                // Create a new chart instance
                chartInstance = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [paymentDataset, expenseDataset]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                // Update lastMonthlyData with the new data
                lastMonthlyData = graphdata.monthlyPayment;
            }
            
            //-----------------------
            // - END OF REVENUE GRAPH -
            //------------------------
            
            //--------------------
            // - TOP STORE -
            //--------------------
            
            // Process the data and display the top performing stores
            // Check if graphdata.storeData has changed
            if (JSON.stringify(graphdata.storeData) !== JSON.stringify(lastStoreData)) {
                // If there are changes, update graphdata.storeData and call the function
                lastStoreData = graphdata.storeData;
                displayTopPerformingStores(graphdata.storeData);
            }
            //--------------------
            // - END OF TOP STORE -
            //--------------------
        },
        error: function (error) {
            // Handle any errors here
            console.error('Error fetching data:', error);
        }
    });
}

// Fetch data initially when the page loads
$(document).ready(function () {
    fetchData();

    // Set up a timer to fetch data every 5 seconds (adjust the interval as needed)
    setInterval(function () {
        fetchData();
    }, 1000); // 5000 milliseconds = 5 seconds
});


function displayTopPerformingStores(data) {
    // Data is an array of top performing stores in JSON format
    // Process the data and display the information using JavaScript/DOM manipulation

    // Example: Assuming you have a div with ID 'top-performing-stores-container'
    const container = $('#top-performing-stores-container');

    // Reset the container content
    container.empty();

    // Loop through the data and create a list of top performing stores
    data.forEach((store, index) => {
        const storeElement = $('<div>').addClass('store-item');
        const storeLink = $('<a>')
            .attr('href', '#')
            .attr('id', 'store_id')
            .attr('value', store.store_id)
            .addClass('store-link link');

        const storeImage = $('<img>')
            .attr('src', store.logo)
            .addClass('img-thumbnail')
            .attr('width', '60px')
            .css('margin-right', '10px');

        const storeName = $('<span>').text(store.store_name);
        const storeRevenue = $('<span>')
            .addClass('float-right text-' + getColorByIndex(index))
            .text('Ksh ' + store.total_revenue);

        storeLink.append(storeImage, storeName, storeRevenue);
        storeElement.append(storeLink);

        container.append(storeElement);
        // Add divider after each store item except the last one
        if (index < data.length - 1) {
            const divider = $('<hr>').addClass('my-2');
            container.append(divider);
        }
    });
  
    // Assuming you have a function to create the pie chart with the data
    createPieChart(data);
  }
  
  function getColorByIndex(index) {
    const color = [
        'rgba(54, 162, 235, 0.5)',
        'rgba(255, 99, 132, 0.5)',
        'rgba(75, 192, 192, 0.5)',
        'rgba(255, 165, 0, 0.5)',   // Orange
        'rgba(128, 0, 128, 0.5)'    // Purple
        // Add more colors as needed
    ];
    return color[index % color.length];
  }
  
  function createPieChart(data) {
    // Prepare data for the pie chart
    const storeNames = data.map(store => store.store_name);
    const revenues = data.map(store => store.total_revenue);
  
    const pieChartCanvas = $('#topStorepieChart').get(0).getContext('2d');
    const pieData = {
      labels: storeNames,
      datasets: [{
        data: revenues,
        backgroundColor: data.map((store, index) => getColorByIndex(index))
      }]
    };
    const pieOptions = {
      legend: {
        display: true,
        position: 'right',
        labels: {
          fontColor: '#333',
          fontSize: 12,
          boxWidth: 15
        }
      }
    };
  
    // Create the pie chart
    new Chart(pieChartCanvas, {
      type: 'doughnut',
      data: pieData,
      options: pieOptions
    });
  }
// Function to format currency with 2 decimal places
function formatCurrency(value) {
    return parseFloat(value).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

// Function to format numbers
function formatNumber(value) {
    return parseFloat(value).toLocaleString();
}

// function to create a pdf out of html elements
function Extracthtmlpdf(bodyid, filename) {
    // Create an HTML element containing your product list
    const productListContainer = document.getElementById(bodyid); // Replace with your actual container ID
    const productListHTML = productListContainer.innerHTML;

        // Generate a filename with the current date
        const currentDate = new Date();
        const dateString = currentDate.toISOString().split('T')[0]; // Format: YYYY-MM-DD
        const finalfilename = `${filename}-${dateString}.pdf`;

    // Use html2pdf to generate and download the PDF
    html2pdf(productListHTML, {
      margin: 10,
      filename: finalfilename,
      image: { type: 'jpeg', quality: 0.98 },
      html2canvas: { scale: 2 },
      jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' },
    });
  }