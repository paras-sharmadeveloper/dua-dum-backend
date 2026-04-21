<!-- resources/views/pages/dashboard.blade.php -->
@extends('layouts.app')

@section('title', 'Role')

@section('content')
    <style>
        .permission-list {
            max-height: 300px;
            overflow-y: auto;
            padding: 10px;
            border: 1px solid #eee;
            border-radius: 5px;
        }

        .permission-item {
            margin-bottom: 8px;
        }
    </style>
    <div class="container-fluid">
        <div class="card mb-5 mb-xl-8 mt-5 shadow-sm">
            <div class="card-header " style="border-top: 2px solid #009ef7 !important">
                <h3 class="card-title"> Manage Roles </h3>
                <div class="card-toolbar">
                    <div class="d-flex flex-stack">
                        <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                            <a> <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#roleModal">Create
                                    Role</button> </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body py-3">
                <div class="row mb-4">
                    <div class="col-md-6 d-flex align-items-center">
                        <!-- Custom Length Selector -->
                        <div class="dataTables_length" id="role-table_length">
                            <label>
                                <select name="role-table_length" aria-controls="role-table"
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
                                placeholder="Search roles...">
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped align-left gs-3 no-footer dataTable" id="role-table">
                        <thead>
                            <tr>
                                <th>Sr no</th>
                                <th>Name</th>
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
    <div class="modal fade" id="roleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-800px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Create Role</h2>
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
                    <form id="roleForm" class="form" action="#" onsubmit="return false;">
                        <div class="d-flex flex-column mb-7 fv-row">
                            <label class="d-flex align-items-center fs-6 fw-semibold form-label mb-2">
                                <span class="required">Role Name</span>
                            </label>
                            <input type="text" id="roleName" class="form-control form-control-sm">
                        </div>

                        <div class="d-flex flex-column mb-7 fv-row">
                            <label class="d-flex align-items-center fs-6 fw-semibold form-label mb-2">
                                <span class="required">Select Permissions</span>
                            </label>
                            <div class="permission-list">
                                <div class="row" id="permissions-container">
                                    <!-- Permissions will be loaded here -->
                                </div>
                            </div>
                        </div>

                        <div class="text-end pt-5">
                            <button type="reset" class="btn btn-sm btn-light me-3" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-sm btn-primary" onclick="createNewRole(event)">
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
    <div class="modal fade" id="roleEditModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-800px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Edit Role</h2>
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
                    <form id="roleEditForm" class="form" action="#" onsubmit="return false;">
                        <div class="d-flex flex-column mb-7 fv-row">
                            <label class="d-flex align-items-center fs-6 fw-semibold form-label mb-2">
                                <span class="required">Role Name</span>
                            </label>
                            <input type="text" id="editRoleName" class="form-control form-control-sm">
                        </div>

                        <div class="d-flex flex-column mb-7 fv-row">
                            <label class="d-flex align-items-center fs-6 fw-semibold form-label mb-2">
                                <span class="required">Select Permissions</span>
                            </label>
                            <div class="permission-list">
                                <div class="row" id="edit-permissions-container">
                                    <!-- Permissions will be loaded here -->
                                </div>
                            </div>
                        </div>

                        <div class="text-end pt-5">
                            <button type="reset" class="btn btn-sm btn-light me-3" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-sm btn-primary" onclick="updateRole(event)">
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
        const ROLE_URL = {
            BASE: '/role',
            DATA: "{{ route('role.get-roles') }}",
            PERMISSIONS: "{{ route('role.get-permissions') }}",
            GET_ONE: function (id) {
                return `${this.BASE}/${id}/get-role`;
            },
            UPDATE: function (id) {
                return `${this.BASE}/${id}`;
            },
            STORE: "{{ route('role.store') }}"
        };

        $(document).ready(function () {
            getRoleData();
            loadPermissions();
        });

        function getRoleData() {
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
                data: null,
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    return row.id ? ActionButtons(row) : '';
                }
            }
            ];

            initializeDataTable(
                '#role-table',
                ROLE_URL.DATA,
                columns,
                "#customSearchInput",
                "#customLengthSelect"
            );
        }

        function ActionButtons(row) {
            var html =
                `<a onClick="openEditModal(${row.id})" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Edit </a> `;
            return html;
        }

        function loadPermissions() {
            showLoader();
            $.ajax({
                url: ROLE_URL.PERMISSIONS,
                type: 'GET',
                success: function (permissions) {
                    renderPermissionCheckboxes(permissions, '#permissions-container');
                    renderPermissionCheckboxes(permissions, '#edit-permissions-container');
                },
                error: function (error) {
                    errorToaster('Error loading permissions');
                },
                complete: function () {
                    hideLoader();
                }
            });
        }

        function renderPermissionCheckboxes(permissions, containerId) {
            let html = '';
            permissions.forEach(function (permission) {
                html += `
                        <div class="col-lg-4 col-md-6 col-sm-12 permission-item">
                            <div class="form-check">
                                <input class="form-check-input permission-checkbox" type="checkbox" id="permission-${permission.id}-${containerId.replace('#', '')}" value="${permission.id}" name="permissions[]">
                                <label class="form-check-label" for="permission-${permission.id}-${containerId.replace('#', '')}">
                                    ${permission.name}
                                </label>
                            </div>
                        </div>`;
            });
            $(containerId).html(html);
        }

        function getSelectedPermissions(containerId) {
            let selectedPermissions = [];
            $(`${containerId} .permission-checkbox:checked`).each(function () {
                selectedPermissions.push($(this).val());
            });
            return selectedPermissions;
        }

        function createNewRole(event) {
            if (event) event.preventDefault();

            const roleName = $("#roleName").val().trim();
            const permissions = getSelectedPermissions('#permissions-container');

            if (!roleName) {
                errorToaster("Please enter role name");
                return;
            }

            showConfirmation("Are you sure you want to create this role?")
                .then((prompt) => {
                    if (!prompt.isConfirmed) {
                        return;
                    }

                    showLoader();
                    $.ajax({
                        url: ROLE_URL.STORE,
                        type: 'POST',
                        data: {
                            name: roleName,
                            permissions: permissions
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                            successToaster(response.message);
                            $('#roleModal').modal('hide');
                            $('#role-table').DataTable().ajax.reload();

                            // Clear form
                            $('#roleName').val('');
                            $('#permissions-container .permission-checkbox').prop('checked', false);
                        },
                        error: function (error) {
                            let message = error.responseJSON?.message || 'Error creating role';
                            errorToaster(message);
                        },
                        complete: function () {
                            hideLoader();
                        }
                    });
                });
        }

        function openEditModal(id) {
            $('#roleEditModal').data('role-id', id);

            // Clear previous selections
            $('#editRoleName').val('');
            $('#edit-permissions-container .permission-checkbox').prop('checked', false);

            showLoader();
            $.ajax({
                url: ROLE_URL.GET_ONE(id),
                type: 'GET',
                success: function (response) {
                    $('#editRoleName').val(response.name);

                    // Check the permissions that belong to this role
                    response.permissions.forEach(function (permissionId) {
                        $(`#permission-${permissionId}-edit-permissions-container`).prop('checked', true);
                    });

                    $('#roleEditModal').modal('show');
                },
                error: function (error) {
                    errorToaster("Error fetching role data");
                },
                complete: function () {
                    hideLoader();
                }
            });
        }

        function updateRole(event) {
            if (event) event.preventDefault();

            const roleId = $('#roleEditModal').data('role-id');
            const roleName = $("#editRoleName").val().trim();
            const permissions = getSelectedPermissions('#edit-permissions-container');

            if (!roleName) {
                errorToaster("Please enter role name");
                return;
            }

            showConfirmation("Are you sure you want to update this role?")
                .then((prompt) => {
                    if (!prompt.isConfirmed) {
                        return;
                    }

                    showLoader();
                    $.ajax({
                        url: ROLE_URL.UPDATE(roleId),
                        type: 'PUT',
                        data: {
                            name: roleName,
                            permissions: permissions
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            'Accept': 'application/json'
                        },
                        success: function (response) {
                            successToaster(response.message);
                            $('#roleEditModal').modal('hide');
                            $('#role-table').DataTable().ajax.reload();
                        },
                        error: function (error) {
                            let message = error.responseJSON?.message || 'Error updating role';
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