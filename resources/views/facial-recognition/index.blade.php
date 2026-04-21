@extends('layouts.app')

@section('title', 'Facial Recognition - Users')

@section('content')
    <div class="container-fluid">
        <div class="card mb-5 mb-xl-8 mt-5 shadow-sm">
            <div class="card-header" style="border-top: 2px solid #009ef7 !important">
                <h3 class="card-title">Facial Recognition Users</h3>
                <div class="card-toolbar">
                    <div class="d-flex flex-stack">
                        <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                            <button type="button" class="btn btn-sm btn-primary" id="addUserBtn">
                                <i class="fas fa-plus"></i> Add User
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body py-3">
                <div class="row mb-4">
                    <div class="col-md-6 d-flex align-items-center">
                        <div class="dataTables_length" id="facial-users-table_length">
                            <label>
                                <select name="facial-users-table_length" aria-controls="facial-users-table"
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
                    <div class="col-md-6 d-flex justify-content-end align-items-center">
                        <div class="input-group" style="max-width: 300px;">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" id="customSearchInput" class="form-control form-control-sm"
                                placeholder="Search users...">
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped align-left gs-3 no-footer dataTable" id="facial-users-table">
                        <thead>
                            <tr>
                                <th>Sr no</th>
                                <th>Face ID</th>
                                <th>Name</th>
                                <th>Face Count</th>
                                <th>Tokens Count</th>
                                <th>Created At</th>
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
        $(document).ready(function () {
            // Initialize DataTable
            var table = $('#facial-users-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("facial-recognition.users.data") }}',
                    type: 'GET'
                },
                columns: [
                    { 
                        data: null,
                        name: 'sr_no',
                        orderable: false,
                        searchable: false,
                        render: function (data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    { data: 'face_id', name: 'face_id' },
                    { data: 'name', name: 'name' },
                    { data: 'face_count', name: 'face_count' },
                    { data: 'details_count', name: 'details_count' },
                    { 
                        data: 'created_at', 
                        name: 'created_at',
                        render: function(data) {
                            return moment(data).format('DD MMM YYYY HH:mm');
                        }
                    }
                ],
                order: [[5, 'desc']], // Order by created_at descending
                pageLength: 10,
                dom: 'rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                language: {
                    processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>',
                    emptyTable: "No facial recognition records found",
                    zeroRecords: "No matching records found"
                }
            });

            // Custom length selector
            $('#customLengthSelect').on('change', function() {
                var length = $(this).val();
                table.page.len(parseInt(length)).draw();
            });

            // Custom search input
            $('#customSearchInput').on('keyup', function() {
                table.search(this.value).draw();
            });

            // Add User button (placeholder for future implementation)
            $('#addUserBtn').on('click', function() {
                alert('Add User functionality coming soon!');
            });
        });
    </script>
@endsection
