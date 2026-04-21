/**
 * Initializes a DataTable with custom search and length selectors
 * @param {string} tableID - The ID of the table element (e.g., "#permission-table")
 * @param {string} ajaxURL - The URL for the AJAX request
 * @param {Object} columns - DataTable columns configuration
 * @param {Object} [options] - Additional DataTable options
 * @param {string} [searchInputID] - ID of custom search input (default: '#customSearchInput')
 * @param {string} [lengthSelectID] - ID of custom length selector (default: '#customLengthSelect')
 */
function initializeDataTable(
    tableID, 
    ajaxURL, 
    columns,
    searchInputID,
    lengthSelectID,
    options = {}
   
  ) {
    // Default configuration
    const defaultConfig = {
      processing: false,
      responsive: false, // Disable Responsive to prevent column hiding/plus button
      scrollX: true,     // Enable horizontal scrolling
      autoWidth: true,  // Prevent auto width recalculation (keeps columns fixed)
      serverSide: true,
      searching: true, // Enable searching feature
      bFilter: false, // Disable default filter control since we're using custom
      bLengthChange: false, // Disable default length control
      ajax: {
        url: ajaxURL,
        type: "POST",
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: function(d) {
          return JSON.stringify(d);
        },
        beforeSend: function() {
            showLoader(); // Show loader when request starts
        },
        complete: function() {
           hideLoader(); // Hide loader when request completes
        },
        contentType: "application/json",
        dataType: "json",
        error: function(xhr, error, thrown) {
          console.error('DataTable error:', error, thrown, xhr);
          errorToaster('Error loading data. Please try again or contact support.');
        }
      },
      columns: columns,
      dom: '<"top"l>rt<"bottom"ip><"clear">',
      pageLength: 10,
      lengthMenu: [
        [10, 25, 50, 100, -1],
        ['10 rows', '25 rows', '50 rows', '100 rows', 'Show all']
      ],
      language: {
        processing: "Loading data, please wait...",
        zeroRecords: "No matching records found",
        emptyTable: "No data available",
        info: "Showing _START_ to _END_ of _TOTAL_ entries",
        infoEmpty: "Showing 0 to 0 of 0 entries",
        infoFiltered: "(filtered from _MAX_ total entries)",
        paginate: {
          first: "First",
          last: "Last",
          next: ">",
          previous: "<"
        }
      }
    };
  
    // Merge default config with custom options
    const config = { ...defaultConfig, ...options };
  
    // Initialize DataTable
    const table = $(tableID).DataTable(config);
  
    // Custom search functionality
    if ($(searchInputID).length) {
      let searchTimer;
      $(searchInputID).on('keyup', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => {
          // Use the DataTable's search API
          table.search($(this).val()).draw();
        }, 300);
      });
      
      // Clear search when escape is pressed
      $(searchInputID).on('keydown', function(e) {
        if (e.key === 'Escape') {
          $(this).val('');
          table.search('').draw();
        }
      });
    }
  
    // Custom length selector functionality
    if ($(lengthSelectID).length) {
      $(lengthSelectID).on('change', function() {
        table.page.len(parseInt(this.value)).draw();
      });
      
      // Set initial length if different from default
      const initialLength = $(lengthSelectID).val();
      if (initialLength && parseInt(initialLength) !== config.pageLength) {
        table.page.len(parseInt(initialLength)).draw();
      }
    }
  
    return table;
}
  
function showConfirmation(message = "Are You Sure?") {
    return Swal.fire({
        text: message,
        icon: "warning",
        iconColor: "text-warning",
        showCancelButton: true,
        confirmButtonClass: "btn btn-primary",
        cancelButtonClass: "btn btn-secondary ",
        confirmButtonText: "Yes",
        cancelButtonText: "No",
    });
}

function successToaster(message)
{
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": true,
        "progressBar": true,
        "positionClass": "toastr-top-right",
        "preventDuplicates": true,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };

    toastr.success(message);
}

function errorToaster(message)
{
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": true,
        "progressBar": true,
        "positionClass": "toastr-top-right",
        "preventDuplicates": true,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };

    toastr.error(message);
}


function statusBadge(status) {
    if (!status) status = '';
    const statusLower = String(status).toLowerCase();
    
    switch (statusLower) {
        case "active":
            return `<span class="badge badge-light-success">${status}</span>`;
        case "in active":
        case "inactive":
            return `<span class="badge badge-light-danger">${status}</span>`;
        case "approved":
            return `<span class="badge badge-light-success">${status}</span>`;
        case "disapproved":
            return `<span class="badge badge-light-danger">${status}</span>`;
        case "pending":
        case "":
            return `<span class="badge badge-light-warning">Pending</span>`;
        case "critical":
            return `<span class="badge badge-light-danger">Critical</span>`;
        case "normal":
            return `<span class="badge badge-light-success">Normal</span>`;
        default:
            return `<span class="badge badge-light-secondary">${status}</span>`;
    }
}

function showLoader() {
    $('#loader-overlay').show();
}
function hideLoader() {
    $('#loader-overlay').hide();
} 

function formatDateFriendly(str) {
            if (!str) return 'N/A';
            try {
                var datePart = String(str).substring(0, 10); // YYYY-MM-DD
                var parts = datePart.split('-');
                if (parts.length !== 3) return 'N/A';
                var year = parts[0];
                var monthIdx = parseInt(parts[1], 10) - 1;
                var day = parseInt(parts[2], 10);
                var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                var monthName = months[monthIdx] || '';
                if (!monthName || isNaN(day)) return 'N/A';
                return day + ' ' + monthName + ' ' + year;
            } catch (e) {
                return 'N/A';
            }
};

function formatDateTimeWithAMPM(data) {
    if (!data) return 'N/A';
    try {
        var str = String(data);
        var datePart = str.substring(0, 10); // YYYY-MM-DD
        var timePart = str.substring(11, 19); // HH:mm:ss
        var parts = datePart.split('-');
        if (parts.length !== 3) return data;
        var year = parts[0];
        var monthIdx = parseInt(parts[1], 10) - 1;
        var day = parseInt(parts[2], 10);
        var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        var monthName = months[monthIdx] || '';
        if (!monthName || isNaN(day)) return data;
        
        // Return 24-hour format
        return day + ' ' + monthName + ' ' + year + ' ' + timePart;
    } catch (e) {
        return data;
    }
}