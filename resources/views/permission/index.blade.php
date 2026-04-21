@extends('layouts.app')

@section('title', 'Permission')

@section('content')
    <style>

    </style>
    <div class="container-fluid">
        <div class="card mb-5 mb-xl-8 mt-5 shadow-sm">
            <div class="card-header " style="border-top: 2px solid #009ef7 !important">
                <h3 class="card-title"> Manage Permissions </h3>
                <div class="card-toolbar">
                    <div class="d-flex flex-stack">
                        <!--begin::Label-->
                        <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">

                            <a> <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#permissionModal">Create
                                    Permission</button> </a>
                            <!--end::Add customer-->
                        </div>

                    </div>
                </div>
            </div>

            <div class="card-body py-3">
                <div class="row mb-4">
                    <div class="col-md-6 d-flex align-items-center">
                        <!-- Custom Length Selector -->
                        <div class="dataTables_length" id="permission-table_length">
                            <label>
                                <select name="permission-table_length" aria-controls="permission-table"
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
                        <!-- Search bar above the table -->
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" id="customSearchInput" class="form-control form-control-sm"
                                placeholder="Search permissions...">
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped align-left gs-3 no-footer dataTable" id="permission-table">
                        <thead>
                            <tr>
                                <th>Sr no</th>
                                <th>Name</th>
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
    <div class="modal fade" id="permissionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Create Permission</h2>
                    <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                        <span class="svg-icon svg-icon-1">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                    transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)"
                                    fill="currentColor" />
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                    <form id="kt_modal_new_card_form" class="form" action="#">
                        <div class="d-flex flex-column mb-7 fv-row">
                            <label class="d-flex align-items-center fs-6 fw-semibold form-label mb-2">
                                <span class="required">Enter Permission Name</span>

                            </label>
                            <input type="text" id="permissionName" class="form-control form-control-sm">
                        </div>
                        <div class="text-end pt-5">
                            <button type="reset" class="btn btn-sm btn-light me-3" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-sm btn-primary" onclick="createNewPermission(event)">
                                <span class="indicator-label">Save</span>
                                <span class="indicator-progress">Please wait...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="permissionEditModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Edit Permission</h2>
                    <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                        <span class="svg-icon svg-icon-1">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                    transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)"
                                    fill="currentColor" />
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                    <form id="kt_modal_new_card_form" class="form" action="#" onsubmit="return false;">
                        <div class="d-flex flex-column mb-7 fv-row">
                            <label class="d-flex align-items-center fs-6 fw-semibold form-label mb-2">
                                <span class="required">Update Permission Name</span>

                            </label>
                            <input type="text" id="editPermissionName" class="form-control form-control-sm">
                        </div>
                        <div class="text-end pt-5">
                            <button type="reset" class="btn btn-sm btn-light me-3" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-sm btn-primary" onclick="updatePermission(event)">
                                <span class="indicator-label">Update</span>
                                <span class="indicator-progress">Please wait...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Constants
        const PERMISSION_URL = {
            BASE: '/permission',
            DATA: "{{ route('permission.get-permissions') }}",
            SAVE_PERMISSION: "{{ route('permission.create-permission') }}",
            GET_ONE: function (id) {
                debugger;
                return `${this.BASE}/${id}/get-permission`;
            },
            UPDATE: function (id) {
                return `${this.BASE}/${id}/update-permission`;
            }
        };

        $(document).ready(function () {
            getPermissionData();
        });

        function getPermissionData() {
            const columns = [{
                data: null,
                orderable: true,
                searchable: true,
                render: function (data, type, row, meta) {
                    return meta.row + 1 + meta.settings._iDisplayStart;
                }
            },
            {
                data: 'name',
                orderable: true,
                searchable: true
            },
            {
                data: 'status',
                orderable: true,
                searchable: true,
                render: statusBadge
            },
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    return row.id ? ActionPrivilege(row) : '';
                }
            }
            ];

            initializeDataTable(
                '#permission-table',
                PERMISSION_URL.DATA,
                columns,
                "#customSearchInput",
                "#customLengthSelect"

            );
        }

        function ActionPrivilege(row) {
            var html =
                `<a onClick="openEditModal(${row.id})" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Edit </a> `;
            return html;
        }

        function createNewPermission(event) {
            if (event) event.preventDefault();
            var permissionName = $("#permissionName").val().trim();

            if (!permissionName) {
                message = "Please Enter permission"
                errorToaster(message);
                return;
            }

            showConfirmation("Are You Sure You Want to Create Permission?")
                .then((prompt) => {
                    if (!prompt.isConfirmed) {
                        return;
                    };
                    showLoader();
                    $.ajax({
                        //url: PERMISSION_URL.BASE,
                        url: PERMISSION_URL.SAVE_PERMISSION,
                        type: 'POST',
                        data: {
                            name: permissionName
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                            successToaster(response.message);
                            $('#permissionModal').modal('hide');
                            $('#permission-table').DataTable().ajax.reload();
                            $('#permissionName').val(''); // Clear the input field
                        },
                        error: function (error) {
                            let message = error.responseJSON?.message || 'Error creating permission';
                            errorToaster(message);
                        },
                        complete: function () {
                            hideLoader();
                        }
                    });
                });
        }

        function openEditModal(id) {
            $('#permissionEditModal').data('permission-id', id);
            showLoader();
            $.ajax({
                url: PERMISSION_URL.GET_ONE(id),
                type: 'GET',
                success: function (response) {
                    debugger;
                    $('#editPermissionName').val(response.name);

                    $('#permissionEditModal').modal('show');
                },
                error: function (xhr) {
                    errorToaster("Error Fetching Data");
                },
                complete: function () {
                    hideLoader();
                }
            });
        }

        function updatePermission() {
            const permissionId = $('#permissionEditModal').data('permission-id');
            const editPermissionName = $("#editPermissionName").val();
            if (!editPermissionName) {
                message = "Please Enter permission"
                errorToaster(message);
                return;
            }

            showConfirmation("Are You Sure You Want to Update Permission?")
                .then((prompt) => {
                    if (!prompt.isConfirmed) {
                        return;
                    };
                    showLoader();
                    $.ajax({
                        url: PERMISSION_URL.UPDATE(permissionId),
                        type: 'PUT',
                        data: {
                            name: editPermissionName
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            'Accept': 'application/json'
                        },
                        success: function (response) {
                            debugger;
                            successToaster(response.message);
                            $('#permissionEditModal').modal('hide');
                            $('#permission-table').DataTable().ajax.reload();
                            $('#editPermissionName').val(''); // Clear the input field
                        },
                        error: function (error) {
                            let message = error.responseJSON?.message || 'Error creating permission';
                            errorToaster(message);
                        },
                        complete: function () {
                            hideLoader();
                        }
                    });
                });
        }
    </script>
@endsection