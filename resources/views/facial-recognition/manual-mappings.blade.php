@extends('layouts.app')

@section('title', 'Facial Recognition - Manual Mappings')

@section('content')
    <div class="container-fluid">
        <div class="card mb-5 mb-xl-8 mt-5 shadow-sm">
            <div class="card-header" style="border-top: 2px solid #009ef7 !important">
                <h3 class="card-title">Manual Mappings</h3>
                <div class="card-toolbar">
                    <div class="d-flex flex-stack">
                        <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                            <button type="button" class="btn btn-sm btn-primary" id="addMappingBtn">
                                <i class="fas fa-plus"></i> Add Mapping
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body py-3">
                <div class="row mb-4">
                    <div class="col-md-6 d-flex align-items-center">
                        <div class="dataTables_length" id="mappings-table_length">
                            <label>
                                <select name="mappings-table_length" aria-controls="mappings-table"
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
                                placeholder="Search mappings...">
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped align-left gs-3 no-footer dataTable" id="mappings-table">
                        <thead>
                            <tr>
                                <th>Sr no</th>
                                <th>User</th>
                                <th>Facial ID</th>
                                <th>Confidence Score</th>
                                <th>Mapped By</th>
                                <th>Status</th>
                                <th>Created At</th>
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
        $(document).ready(function () {
            // Initialize DataTable or other functionality here
            console.log('Manual Mappings page loaded');
        });
    </script>
@endsection
