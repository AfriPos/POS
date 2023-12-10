localStorage.removeItem("captureRange");
if (localStorage.getItem('captureRange') !== null) {
    $('#daterange-btn2 span').html(localStorage.getItem('captureRange'));
  } else {
    $('#daterange-btn2 span').html('<i class="far fa-calendar-alt"></i> Date range');
  }

/*=============================================
DATES RANGE
=============================================*/

$('#daterange-btn2').daterangepicker(
    {
      ranges   : {
        'Today'       : [moment(), moment()],
        'Yesterday'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        'Last 7 days' : [moment().subtract(6, 'days'), moment()],
        'Last 30 days': [moment().subtract(29, 'days'), moment()],
        'This month'  : [moment().startOf('month'), moment().endOf('month')],
        'Last month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
      },
      startDate: moment(),
      endDate  : moment()
    },
    function (start, end) {
      $('#daterange-btn2 span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
  
      var initialDate = start.format('YYYY-MM-DD');
  
      var finalDate = end.format('YYYY-MM-DD');
  
      var captureRange = $("#daterange-btn2 span").html();
     
         localStorage.setItem("captureRange", captureRange);
         console.log("localStorage", localStorage);
  
         window.location = "index.php?route=sales&initialDate="+initialDate+"&finalDate="+finalDate;
  
    }
  
  )
  
  /*=============================================
  CANCEL DATES RANGE
  =============================================*/
  
  $(".daterangepicker.opensright .ranges .cancelBtn").on("click", function(){
  
      localStorage.removeItem("captureRange");
      localStorage.clear();
      window.location = "sales";
  })
  
  /*=============================================
  CAPTURE TODAY'S BUTTON
  =============================================*/
  
  $(".daterangepicker.opensright .ranges li").on("click", function(){
  
      var todayButton = $(this).attr("data-range-key");
  
      if(todayButton == "Today"){
  
          var d = new Date();
          
          var day = d.getDate();
          var month= d.getMonth()+1;
          var year = d.getFullYear();
  
          if(month < 10){
  
              var initialDate = year+"-0"+month+"-"+day;
              var finalDate = year+"-0"+month+"-"+day;
  
          }else if(day < 10){
  
              var initialDate = year+"-"+month+"-0"+day;
              var finalDate = year+"-"+month+"-0"+day;
  
          }else if(month < 10 && day < 10){
  
              var initialDate = year+"-0"+month+"-0"+day;
              var finalDate = year+"-0"+month+"-0"+day;
  
          }else{
  
              var initialDate = year+"-"+month+"-"+day;
              var finalDate = year+"-"+month+"-"+day;
  
          }	
  
          localStorage.setItem("captureRange", "Today");
  
          window.location = "index.php?route=sales&initialDate="+initialDate+"&finalDate="+finalDate;
  
      }
  
  })


 /*=============================================
  SALES PER PRODUCT DATE FUNCTION
  =============================================*/
  $(document).ready(function() {
    // Function to update the table based on selected year and month
    function updateTable() {
        var selectedMonth = $("#selectedMonth").val();
        var selectedYear = $("#selectedYear").val();

        $.ajax({
            url: "ajax/products.ajax.php",
            method: "POST",
            data: {
                selectedMonth: selectedMonth,
                selectedYear: selectedYear
            },
            success: function(data) {
                $("#tableBody").html(data);
            } ,error: function(xhr, status, error) {
                console.error("AJAX request error:", error);
            }
        });
    }

    // Attach the change event handlers
    $("#selectedMonth, #selectedYear").change(function() {
        updateTable();
    });

    // Initial update
    updateTable();
});