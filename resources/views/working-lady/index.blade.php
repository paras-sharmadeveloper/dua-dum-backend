@extends('layouts.app')

@section('title', 'Working Ladies')

@section('content')
    <style>
        .btn i {
            padding-right: 0rem !important;
        }
    </style>
    <div class="container-fluid">
        <div class="card mb-5 mb-xl-8 mt-5 shadow-sm">
            <div class="card-header" style="border-top: 2px solid #009ef7 !important">
                <h3 class="card-title">Manage Working Ladies</h3>
                <div class="card-toolbar">
                    <div class="d-flex flex-stack">
                        <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                            <a href="{{ route('working-lady.create') }}" class="btn btn-sm btn-primary">
                                Add Working Lady
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body py-3">
                <div class="row mb-4">
                    <div class="col-md-6 d-flex align-items-center">
                        <div class="dataTables_length" id="working-lady-table_length">
                            <label>
                                <select name="working-lady-table_length" aria-controls="working-lady-table"
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
                    <div class="col-md-2"></div>
                    <div class="col-md-4 d-flex justify-content-end align-items-center">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" id="customSearchInput" class="form-control form-control-sm"
                                placeholder="Search working ladies...">
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped align-left gs-3 no-footer dataTable" id="working-lady-table">
                        <thead>
                            <tr>
                                <th>Sr no</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Designation</th>
                                <th>Company</th>
                                <th>Place of Work</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Case Type</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will come from AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            var table = initializeDataTable('#working-lady-table', "{{ route('working-lady.data') }}", [{
                    data: null,
                    searchable: false,
                    orderable: false,
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'first_name',
                    name: 'first_name'
                },
                {
                    data: 'last_name',
                    name: 'last_name'
                },
                {
                    data: 'designation',
                    name: 'designation'
                },
                {
                    data: 'company_name',
                    name: 'company_name'
                },
                {
                    data: 'place_of_work',
                    name: 'place_of_work'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'phone_number',
                    name: 'phone_number'
                },
                {
                    data: 'case_type',
                    name: 'case_type',
                    render: function(data) {
                        return statusBadge(data);
                    }
                },
                {
                    data: 'status',
                    name: 'status',
                    render: statusBadge
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        return `
                            <div class="d-flex gap-2">
                                <a href="/working-lady/${row.id}/edit" class="btn btn-sm btn-warning" >
                                   Edit
                                </a>
                                <button class="btn btn-sm btn-danger delete-btn" data-id="${row.id}" >
                                    Delete
                                </button>
                            </div>
                        `;
                    }
                }
            ], 'First Name');

            // Delete handler
            $(document).on('click', '.delete-btn', function() {
                const id = $(this).data('id');

                showConfirmation('Are you sure you want to delete this working lady?',
                    'This action cannot be undone.',
                    'warning',
                    'Yes, delete it!'
                ).then((result) => {
                    if (result.isConfirmed) {
                        showLoader();
                        $.ajax({
                            url: `/working-lady/${id}`,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                hideLoader();
                                successToaster(response.message);
                                table.ajax.reload();
                            },
                            error: function(xhr) {
                                hideLoader();
                                const message = xhr.responseJSON?.message || 'Failed to delete working lady';
                                errorToaster(message);
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection