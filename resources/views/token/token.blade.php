@extends('layouts.app')

@section('title', 'Tokens')

@section('content')
    <div class="container-fluid">
        <div class="card mb-5 mb-xl-8 mt-5 shadow-sm">
            <div class="card-header" style="border-top: 2px solid #009ef7 !important">
                <h3 class="card-title">Manage Tokens</h3>
                <div class="card-toolbar">
                    <div class="d-flex flex-stack">
                        <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                            <!-- Optional actions for tokens can go here -->
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body py-3">
                <div class="row mb-4">
                    <div class="col-md-3 d-flex align-items-center">
                        <div class="dataTables_length" id="tokens-table_length">
                            <label>
                                <select name="tokens-table_length" aria-controls="tokens-table"
                                        class="form-select form-select-sm form-select-solid" id="customLengthSelect">
                                     
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                    <option value="-1">All</option>
                                </select>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-3 d-flex align-items-center">
                        <select class="form-select form-select-sm form-select-solid" id="filterType">
                            <option value="all" selected>All Applications</option>
                            <option value="token_applications">Pending Applications</option>
                            <option value="approved_applications">Approved Applications</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-center">
                        <input class="form-control form-control-sm form-control-solid" placeholder="Pick date range" id="dateRangePicker"/>
                    </div>
                    <div class="col-md-3 d-flex justify-content-end align-items-center">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" id="customSearchInput" class="form-control form-control-sm" placeholder="Search tokens...">
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped align-left gs-3 no-footer dataTable" id="tokens-table">
                        <thead>
                            <tr>
                                <th>Sr no</th>
                                <th>Token Code</th>
                                <th>Token Number</th>
                                <th>Venue</th>
                                <th>User Type</th>
                                <th>Service Type</th>
                                <th>Name</th>
                                <th>City</th>
                                <th>Photo</th>
                                <th>Phone</th>
                                <th>Last Phone Date</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            // Initialize date range picker
            initializeDateRangePicker();
            getTokensData();
            
            // Reload table when filter changes
            $('#filterType, #dateRangePicker').on('change', function() {
                $('#tokens-table').DataTable().ajax.reload();
            });
        });
        
        function initializeDateRangePicker() {
            var start = moment().subtract(29, 'days');
            var end = moment();
            
            $('#dateRangePicker').daterangepicker({
                startDate: start,
                endDate: end,
                timePicker: true,
                timePicker24Hour: true,
                timePickerSeconds: true,
                locale: {
                    format: 'YYYY-MM-DD HH:mm:ss'
                },
                ranges: {
                    'Today': [moment().startOf('day'), moment().endOf('day')],
                    'Yesterday': [moment().subtract(1, 'days').startOf('day'), moment().subtract(1, 'days').endOf('day')],
                    'Last 7 Days': [moment().subtract(6, 'days').startOf('day'), moment().endOf('day')],
                    'Last 30 Days': [moment().subtract(29, 'days').startOf('day'), moment().endOf('day')],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            });
        }

        function getTokensData() {
            const columns = [
                {
                    data: null,
                    orderable: true,
                    searchable: true,
                    render: function (data, type, row, meta) {
                        return meta.row + 1 + meta.settings._iDisplayStart;
                    }
                },
                { data: 'token_code', orderable: true, searchable: true },
                { data: 'token_number', orderable: true, searchable: true },
                { data: 'venue', orderable: true, searchable: true },
                { data: 'user_type', orderable: true, searchable: true },
                { data: 'service_type', orderable: true, searchable: true },
                { data: 'user_name', orderable: true, searchable: true, render: function(data){ return data ? data : 'N/A'; } },
                { data: 'city', orderable: true, searchable: true, render: function(data){ return data ? data : 'N/A'; } },
                { data: null, orderable: false, searchable: false, render: function(data, type, row){ return photoCell(row); } },
                { data: 'phone_number', orderable: true, searchable: true },
                {
                    data: 'last_phone_date', orderable: true, searchable: false,
                    render: function(data) { return formatDateFriendly(data); }
                },
                { data: 'status', orderable: true, searchable: true , render: statusBadge},
                { data: 'created_at', orderable: true, searchable: false, render: formatDateTimeWithAMPM },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        return ActionButtons(row);
                    }
                }
            ];

            // Properly extend ajax data function to include filters
            const customOptions = {
                ajax: {
                    url: '{{ route('tokens.data') }}',
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: function(d) {
                        // Add custom filter parameters to the DataTable request
                        d.filter_type = $('#filterType').val() || 'all';
                        d.date_range = $('#dateRangePicker').val() || '';
                        return JSON.stringify(d);
                    },
                    beforeSend: function() {
                        if (typeof showLoader === 'function') showLoader();
                    },
                    complete: function() {
                        if (typeof hideLoader === 'function') hideLoader();
                    },
                    contentType: "application/json",
                    dataType: "json",
                    error: function(xhr, error, thrown) {
                        console.error('DataTable AJAX error:', error);
                        console.error('Response:', xhr.responseText);
                        console.error('Status:', xhr.status);
                        if (typeof errorToaster === 'function') {
                            errorToaster('Error loading data. Please try again.');
                        }
                    }
                }
            };

            window.tokensTable = initializeDataTable(
                '#tokens-table',
                '{{ route('tokens.data') }}',
                columns,
                '#customSearchInput',
                '#customLengthSelect',
                customOptions
            );
        }

        function photoCell(row) {
            if (row.user_image_path) {
                return `<img src="/storage/${row.user_image_path}" alt="user" style="width:80px;height:80px;border-radius:3px;object-fit:cover;"/>`;
            }
            return 'N/A';
        }

        

        function ActionButtons(row) {
            if (row.status === 'Pending') {
                const approveBtn = `<a onClick="updateTokenStatus('${row.id}','Approved')" class="btn btn-sm btn-success">Approve</a>`;
                const disapproveBtn = `<a onClick="updateTokenStatus('${row.id}','Disapproved')" class="btn btn-sm btn-danger">Disapprove</a>`;
                return `${approveBtn} ${disapproveBtn}`;
            } else {
                const cancelBtn = `<a onClick="updateTokenStatus('${row.id}','Pending')" class="btn btn-sm btn-info">Cancel</a>`;
                return cancelBtn;
            }
        }

        function updateTokenStatus(id, status) {
            let msg = 'Update status?';
            if (status === 'Approved') msg = 'Approve this token?';
            else if (status === 'Disapproved') msg = 'Disapprove this token?';
            else if (status === 'Pending') msg = 'Mark this token back to Pending?';
            if (typeof showConfirmation === 'function') {
                showConfirmation(msg).then((prompt) => {
                    if (!prompt.isConfirmed) return;
                    sendUpdate(id, status);
                });
            } else {
                // Fallback without confirmation helper
                sendUpdate(id, status);
            }
        }

        function sendUpdate(id, status) {
            if (typeof showLoader === 'function') showLoader();
            $.ajax({
                url: `{{ url('/tokens') }}/${id}/status`,
                type: 'POST',
                data: { status: status },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                },
                success: function (response) {
                    if (typeof successToaster === 'function') {
                        successToaster(response.message || 'Status updated');
                    }
                    $('#tokens-table').DataTable().ajax.reload();
                },
                error: function (xhr) {
                    const message = xhr.responseJSON?.message || 'Error updating status';
                    if (typeof errorToaster === 'function') errorToaster(message);
                },
                complete: function () {
                    if (typeof hideLoader === 'function') hideLoader();
                }
            });
        }
    </script>
@endsection
