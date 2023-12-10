var processedNotifications = [];
var unreadNotificationCounter = 0; // Counter variable for unread notifications


// Function to mark a notification as read using AJAX
function MarkReadNotification(session, notificationId) {

    var datum = new FormData();
    datum.append("sessionid", session);
    datum.append("notificationId", notificationId);
    $.ajax({
        url: "ajax/notifications.ajax.php",
        method: "POST",
        data: datum,
        cache: false,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function(answer) {

        } ,error: function(xhr, status, error) {
          console.error("AJAX request error:", error);
        }
        
    });
}

//  mark as read ajax rejected
function MarkReadNotificationRejected(type, session, status, user, answer) {
    
    var datum = new FormData();
    datum.append("session", session);
    datum.append("type", type);
    datum.append("status", status);
    datum.append("user", user);
    datum.append("name", answer);
    
    $.ajax({
        url: "ajax/notifications.ajax.php",
        method: "POST",
        data: datum,
        cache: false,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function(answer) {
            // Handle the AJAX success response here

        } ,error: function(xhr, status, error) {
          console.error("AJAX request error:", error);
        }

    });

}





// Function to fetch and display notifications in real-time
function fetchNotification() {
  var datum = new FormData();
  getSessionInfo(function(data) {
    if (data !== null) {
      var sessionId = data.sessionId;
      var sessionRole = data.sessionRole;
      var sessionStoreid = data.sessionStoreid;
      datum.append("sessionId", sessionId)
      datum.append("sessionRole", sessionRole)
      datum.append("sessionStoreid", sessionStoreid)
    
      $.ajax({
        url: "ajax/notifications.ajax.php",
        method: "POST",
        data: datum,
        cache: false,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function (notifications) {
          var allowedNotificationTypes = {
            'Administrator': ['Stock notification', 'Category deletion'],
            'Owner': ['Stock notification', 'Category deletion'],
            'Supervisor': ['Stock notification', 'Category deletion'],
            'Store': ['Admin feedback', 'Stock notification', 'Acceptions'],
            'Seller': ['Stock', 'Acceptions']
          };
          var eligibleNotificationTypes = allowedNotificationTypes[sessionRole];
          var newUnreadNotificationCounter = 0; // Initialize counter for new unread notifications

          notifications.forEach((notification) => {
            var notificationId = notification.id;
            var viewedBy = notification.viewed_by;
            var notificationTypeData = notification.notification_type;
            var notificationType = notificationTypeData.split(',')[0];

            if (eligibleNotificationTypes && eligibleNotificationTypes.includes(notificationType)) {
              // Check if the notification has been viewed by the current logged-in user
              if (!viewedBy.includes(sessionId) && !processedNotifications.includes(notificationId)) {
                // Add the notification ID to the processedNotifications array
                processedNotifications.push(notificationId);

                // Create a new HTML notification
                createNotification(notification);

                // Increment the new unread notification counter
                newUnreadNotificationCounter++;
              }
            }
          });

          var bellIcon = document.getElementById("bellIcon");
          if (unreadNotificationCounter > 0) {
            bellIcon.classList.add("shaking-bell");
          } else {
            bellIcon.classList.remove("shaking-bell");
          }

          // Update the unread notification count and display
          unreadNotificationCounter += newUnreadNotificationCounter;
          var rowCountSpan = document.getElementById('rowCountSpan');
          rowCountSpan.textContent = unreadNotificationCounter;

          var rowCountSpanHeader = document.getElementById('rowCountSpanHeader');
          rowCountSpanHeader.textContent = unreadNotificationCounter + " Notifications";
        } ,error: function(xhr, status, error) {
          console.error("AJAX request error:", error);
        }

      });

    }

  });
  
}

// Fetch notifications at regular intervals
setInterval(fetchNotification, 1000);

// Function to create a new HTML notification
function createNotification(notification) {
  var message = notification.message;
  var notificationTypeData = notification.notification_type;
  var createDate = new Date(notification.create_date);
  var name = notification.name;
  var notificationType = notificationTypeData.split(',')[0];
  var notificationTypeValue = notificationTypeData.split(',')[1];

  getSessionInfo(function(data) {
    if (data !== null) {
      // Data retrieval was successful
      var sessionId = data.sessionId;
      var currentDate = new Date();
      var timeDifference = Math.abs(currentDate - createDate) / 1000;

      let timeDifferenceString = '';

      if (timeDifference <= 10) {
        timeDifferenceString = 'just now';
      } else if (timeDifference <= 60) {
        timeDifferenceString = timeDifference + ' seconds ago';
      } else if (timeDifference <= 60 * 60) {
        var minutes = Math.floor(timeDifference / 60);
        timeDifferenceString = minutes + ' minutes ago';
      } else if (timeDifference <= 60 * 60 * 24) {
        var hours = Math.floor(timeDifference / (60 * 60));
        timeDifferenceString = hours + ' hours ago';
      } else if (timeDifference <= 60 * 60 * 24 * 7) {
        var days = Math.floor(timeDifference / (60 * 60 * 24));
        timeDifferenceString = days + ' days ago';
      } else if (timeDifference <= 60 * 60 * 24 * 7 * 4) {
        var weeks = Math.floor(timeDifference / (60 * 60 * 24 * 7));
        timeDifferenceString = weeks + ' weeks ago';
      } else if (timeDifference <= 60 * 60 * 24 * 7 * 4 * 12) {
        var months = Math.floor(timeDifference / (60 * 60 * 24 * 7 * 4));
        timeDifferenceString = months + ' months ago';
      } else {
        var years = Math.floor(timeDifference / (60 * 60 * 24 * 7 * 4 * 12));
        timeDifferenceString = years + ' years ago';
      }

      var notificationItemsContainer = document.querySelector('.notificationItems');

      var notificationItem = document.createElement('a');
      notificationItem.classList.add('dropdown-item', 'notification-link');

      if (notificationType === 'Stock notification') {
        notificationItem.classList.add('stock-notification');
        notificationItem.setAttribute('data-value1', notificationTypeValue);
        notificationItem.setAttribute('data-value2', sessionId);
        notificationItem.innerHTML = `
          <i class="fa-solid fa-boxes-stacked"></i> ${message}<br>
          <span class="float-right text-muted text-sm">${timeDifferenceString}</span><br>
        `;
      } else if (notificationType === 'Category deletion') {
        var item = 'id';
        var value = notificationTypeValue;

        var datum = new FormData();
        datum.append("item", item);
        datum.append("value", value);

        $.ajax({
          url: "ajax/categories.ajax.php",
          method: "POST",
          data: datum,
          cache: false,
          contentType: false,
          processData: false,
          dataType: "json",
          success: function(answer) {
            notificationItem.classList.add('category-deletion');
            notificationItem.setAttribute('data-value1', notificationTypeValue);
            notificationItem.setAttribute('data-value2', sessionId);
            notificationItem.setAttribute('data-value3', name);
            notificationItem.setAttribute('data-value4', message);
            notificationItem.innerHTML = `
              <i class="fa-solid fa-sitemap"></i> ${name} wants to delete the ${answer.Category} category<br>
              <span class="float-right text-muted text-sm">${timeDifferenceString}</span><br>
            `;
          } ,error: function(xhr, status, error) {
            console.error("AJAX request error:", error);
          }
        });
      } else if (notificationType === 'Admin feedback' && notification.viewUser === sessionStorage.username) {
        notificationItem.classList.add('admin-feedback');
        notificationItem.setAttribute('data-value1', notification.notificationType);
        notificationItem.setAttribute('data-value2', sessionId);
        notificationItem.innerHTML = `
          <i class="fa-solid fa-xmark"></i> ${message}<br>
          <span class="float-right text-muted text-sm">${timeDifferenceString}</span><br>
        `;
      }

      notificationItem.addEventListener('click', function() {
        if (notificationType === 'Stock notification') {
          var barcode = this.getAttribute('data-value1');
          var session = this.getAttribute('data-value2');
          var notificationId=notification.id;

          window.location = "index.php?route=viewproduct&product-id=" + barcode;
          MarkReadNotification(session, notificationId);
        } else if (notificationType === 'Category deletion') {
          var idCategory = this.getAttribute('data-value1');
          var session = this.getAttribute('data-value2');
          var user = this.getAttribute('data-value3');
          var message = this.getAttribute('data-value4');
          var notificationId=notification.id;
          var type = "Category deletion," + idCategory;
          var status = "Rejected";

          var datum = new FormData();
          datum.append("idCategory", idCategory);

          $.ajax({
            url: "ajax/categories.ajax.php",
            method: "POST",
            data: datum,
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function(answer) {
              var answer = answer['Category'];
              Swal.fire({
                title: 'Category Deletion',
                html: `Reason: ${message}<br>Are you sure you want to delete the ${answer} category?<br>All the products in this category will also be deleted.<br>Changes made cannot be reverted.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0069d9',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Confirm',
                cancelButtonText: 'Cancel'
              }).then((result) => {
                if (result.isConfirmed) {
                  window.location = "index.php?route=category&idCategory=" + idCategory;
                  MarkReadNotification(session, notificationId);
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                  MarkReadNotificationRejected(type, session, status, user, answer);
                  MarkReadNotification(session, notificationId);
                }
              });
            } ,error: function(xhr, status, error) {
              console.error("AJAX request error:", error);
            }
          });
        } else if (notificationType === 'Admin feedback') {
          var notificationId=notification.id;
          var session = this.getAttribute('data-value2');

          MarkReadNotification(session, notificationId);
        }

        // Remove the notification from the DOM
        notificationItem.remove();
          // Decrement the notification counter
          unreadNotificationCounter--;

          // Update the notification count display
          var notificationCount = unreadNotificationCounter;
          var rowCountSpan = document.getElementById('rowCountSpan');
          rowCountSpan.textContent = notificationCount;

          var rowCountSpanHeader = document.getElementById('rowCountSpanHeader');
          rowCountSpanHeader.textContent = notificationCount + " Notifications";
      });

      notificationItemsContainer.appendChild(notificationItem);
      notificationItemsContainer.appendChild(document.createElement('div')).classList.add('dropdown-divider');
      
    }
  });
}

// Function to get session information
function getSessionInfo(callback) {
  $.ajax({
    url: "ajax/session.ajax.php",
    method: "GET",
    dataType: "json",
    success: function (data) {
      callback(data); // Invoke the callback with the retrieved data
    } ,error: function(xhr, status, error) {
      console.error("AJAX request error:", error);
    }
  });
}
