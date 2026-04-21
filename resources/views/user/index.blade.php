@extends('layouts.app')

@section('title', 'Users')

@section('content')
    <div class="container-fluid">
        <div class="card mb-5 mb-xl-8 mt-5 shadow-sm">
            <div class="card-header" style="border-top: 2px solid #009ef7 !important">
                <h3 class="card-title">Manage Users</h3>
                <div class="card-toolbar">
                    <div class="d-flex flex-stack">
                        <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                            <a> <button type="button" class="btn btn-sm btn-primary" id="addUserBtn">Create User</button>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body py-3">
                <div class="row mb-4">
                    <div class="col-md-6 d-flex align-items-center">
                        <!-- Custom Length Selector -->
                        <div class="dataTables_length" id="users-table_length">
                            <label>
                                <select name="users-table_length" aria-controls="users-table"
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
                                placeholder="Search users...">
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped align-left gs-3 no-footer dataTable" id="users-table">
                        <thead>
                            <tr>
                                <th>Sr no</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
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

    <!-- Add/Edit User Modal -->
    <div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 id="modalTitle">Create User</h2>
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
                    <form id="userForm" class="form" action="#" onsubmit="return false;">
                        @csrf
                        <input type="hidden" id="userId" name="userId">

                        <div class="d-flex flex-column mb-7 fv-row">
                            <label class="d-flex align-items-center fs-6 fw-semibold form-label mb-2">
                                <span class="required">Name</span>
                            </label>
                            <input type="text" class="form-control form-control-sm" id="name" name="name" required>
                        </div>

                        <div class="d-flex flex-column mb-7 fv-row">
                            <label class="d-flex align-items-center fs-6 fw-semibold form-label mb-2">
                                <span class="required">Email</span>
                            </label>
                            <input type="email" class="form-control form-control-sm" id="email" name="email" required>
                        </div>

                        <div class="d-flex flex-column mb-7 fv-row">
                            <label class="d-flex align-items-center fs-6 fw-semibold form-label mb-2">
                                <span class="required password-required">Password</span>
                            </label>
                            <input type="password" class="form-control form-control-sm" id="password" name="password">
                            <small class="text-muted" id="passwordNote">Leave blank to keep unchanged (for editing)</small>
                        </div>

                        <div class="d-flex flex-column mb-7 fv-row">
                            <label class="d-flex align-items-center fs-6 fw-semibold form-label mb-2">
                                <span class="required password-required">Confirm Password</span>
                            </label>
                            <input type="password" class="form-control form-control-sm" id="password_confirmation"
                                name="password_confirmation">
                        </div>

                        <div class="d-flex flex-column mb-7 fv-row">
                            <label class="d-flex align-items-center fs-6 fw-semibold form-label mb-2">
                                <span>Role</span>
                            </label>
                            <select class="form-select form-select-sm" id="role_id" name="role_id">
                                <option value="">Select Role</option>
                                <!-- Roles will be loaded via AJAX -->
                            </select>
                        </div>

                        <div class="d-flex flex-column mb-7 fv-row">
                            <label class="d-flex align-items-center fs-6 fw-semibold form-label mb-2">
                                <span>Status</span>
                            </label>
                            <select class="form-select form-select-sm" id="status" name="status">
                                <option value="Active">Active</option>
                                <option value="In Active">In Active</option>
                            </select>
                        </div>

                        <div class="text-end pt-5">
                            <button type="reset" class="btn btn-sm btn-light me-3" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-sm btn-primary" id="saveUserBtn" data-kt-indicator="off">
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

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
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
                <div class="modal-body">
                    <p>Are you sure you want to delete this user?</p>
                    <p class="text-danger">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-light me-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-sm btn-danger" id="confirmDeleteBtn" data-kt-indicator="off">
                        <span class="indicator-label">Delete</span>
                        <span class="indicator-progress">Please wait...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Constants
        const USER_URL = {
            BASE: '/users',
            DATA: "{{ route('users.data') }}",
            ROLES: "{{ route('users.roles') }}",
            STORE: "{{ route('users.store') }}",
            GET_ONE: function (id) {
                return `${this.BASE}/${id}`;
            },
            UPDATE: function (id) {
                return `${this.BASE}/${id}`;
            },
            DELETE: function (id) {
                return `${this.BASE}/${id}`;
            },
            TOGGLE_STATUS: function (id) {
                return `${this.BASE}/${id}/toggle-status`;
            }
        };

        $(document).ready(function () {
            getUsersData();
            loadRoles();
        });

        function getUsersData() {
            const columns = [
                {
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
                    data: 'email',
                    orderable: true,
                    searchable: true
                },
                {
                    data: 'role_name',
                    orderable: false,
                    searchable: false
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
                        return row.id ? ActionButtons(row) : '';
                    }
                }
            ];

            initializeDataTable(
                '#users-table',
                USER_URL.DATA,
                columns,
                "#customSearchInput",
                "#customLengthSelect"
            );
        }

        function statusBadge(status) {
            if (status === 'Active') {
                return '<span class="badge badge-light-success">Active</span>';
            } else {
                return '<span class="badge badge-light-danger">Inactive</span>';
            }
        }

        function ActionButtons(row) {
            var statusBtn = row.status === 'Active'
                ? `<a onClick="toggleUserStatus(${row.id})" class="btn btn-sm btn-warning"><i class="fas fa-ban"></i> Deactivate</a>`
                : `<a onClick="toggleUserStatus(${row.id})" class="btn btn-sm btn-success"><i class="fas fa-check"></i> Activate</a>`;

            var html =
                `<a onClick="editUser(${row.id})" class="btn btn-sm btn-info"><i class="fas fa-edit"></i> Edit</a> 
                                                                                                                                 ${statusBtn}
                                                                                                                                 <a onClick="deleteUser(${row.id})" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i> Delete</a>`;
            return html;
        }

        function loadRoles() {
            showLoader();
            $.ajax({
                url: USER_URL.ROLES,
                type: "GET",
                success: function (response) {
                    var select = $('#role_id');
                    $.each(response, function (key, role) {
                        select.append('<option value="' + role.id + '">' + role.name + '</option>');
                    });
                    hideLoader();
                },
                error: function (xhr) {
                    errorToaster("Error loading roles");
                    hideLoader();
                }
            });
        }

        // Add User Button Click
        $('#addUserBtn').on('click', function () {
            resetForm();
            $('#modalTitle').text('Create User');
            $('#passwordNote').hide();
            $('.password-required').show();
            $('#userModal').modal('show');
        });

        // Edit User
        function editUser(userId) {
            resetForm();
            $('#modalTitle').text('Edit User');
            $('#passwordNote').show();
            $('.password-required').hide();
            $('#userId').val(userId);

            // Fetch user data
            showLoader();
            $.ajax({
                url: USER_URL.GET_ONE(userId),
                type: "GET",
                headers: {
                    'Accept': 'application/json'
                },
                success: function (response) {
                    $('#name').val(response.name);
                    $('#email').val(response.email);
                    $('#status').val(response.status);
                    $('#role_id').val(response.role_id);
                    $('#userModal').modal('show');
                },
                error: function (xhr) {
                    errorToaster("Error Fetching User Data");
                },
                complete: function () {
                    hideLoader();
                }
            });
        }

        // Toggle User Status
        function toggleUserStatus(userId) {
            showConfirmation("Are You Sure You Want to Change User Status?")
                .then((prompt) => {
                    if (!prompt.isConfirmed) {
                        return;
                    }

                    showLoader();
                    $.ajax({
                        url: USER_URL.TOGGLE_STATUS(userId),
                        type: "POST",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            'Accept': 'application/json'
                        },
                        success: function (response) {
                            successToaster(response.message);
                            $('#users-table').DataTable().ajax.reload();
                        },
                        error: function (xhr) {
                            errorToaster(xhr.responseJSON?.message || 'Error changing status');
                        },
                        complete: function () {
                            hideLoader();
                        }
                    });
                });
        }

        // Delete User
        function deleteUser(userId) {
            showConfirmation("Are You Sure You Want to Delete This User?")
                .then((prompt) => {
                    if (!prompt.isConfirmed) {
                        return;
                    }

                    showLoader();
                    $.ajax({
                        url: USER_URL.DELETE(userId),
                        type: "DELETE",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            'Accept': 'application/json'
                        },
                        success: function (response) {
                            successToaster(response.message);
                            $('#users-table').DataTable().ajax.reload();
                        },
                        error: function (xhr) {
                            errorToaster(xhr.responseJSON?.message || 'Error deleting user');
                        },
                        complete: function () {
                            hideLoader();
                        }
                    });
                });
        }

        // Save User
        $('#saveUserBtn').on('click', function (event) {
            if (event) event.preventDefault();

            var userId = $('#userId').val();
            var formData = {
                name: $('#name').val().trim(),
                email: $('#email').val().trim(),
                role_id: $('#role_id').val(),
                status: $('#status').val(),
                _token: $('meta[name="csrf-token"]').attr('content')
            };

            // Validate required fields
            if (!formData.name || !formData.email) {
                errorToaster("Please fill all required fields");
                return;
            }

            // Add password fields only if they are filled or for new user
            if ($('#password').val() || !userId) {
                formData.password = $('#password').val();
                formData.password_confirmation = $('#password_confirmation').val();

                // For new user, password is required
                if (!userId && !formData.password) {
                    errorToaster("Password is required for new users");
                    return;
                }

                // Validation for password confirmation
                if (formData.password !== formData.password_confirmation) {
                    errorToaster("Password and confirmation do not match");
                    return;
                }
            }

            var url = userId ? USER_URL.UPDATE(userId) : USER_URL.STORE;
            var method = userId ? "PUT" : "POST";
            var confirmMessage = userId ? "Are You Sure You Want to Update User?" : "Are You Sure You Want to Create User?";

            // Show the loading indicator in the button
            const button = $(this);
            button.attr("data-kt-indicator", "on");
            button.prop("disabled", true);

            showConfirmation(confirmMessage)
                .then((prompt) => {
                    if (!prompt.isConfirmed) {
                        // Reset button state if user cancels
                        button.removeAttr("data-kt-indicator");
                        button.prop("disabled", false);
                        return;
                    }

                    showLoader();
                    $.ajax({
                        url: url,
                        type: method,
                        data: formData,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            'Accept': 'application/json'
                        },
                        success: function (response) {
                            successToaster(response.message);
                            $('#userModal').modal('hide');
                            $('#users-table').DataTable().ajax.reload();
                            resetForm();
                        },
                        error: function (xhr) {
                            var errors = xhr.responseJSON?.errors;
                            if (errors) {
                                var errorMsg = '';
                                $.each(errors, function (key, value) {
                                    errorMsg += value[0] + '<br>';
                                });
                                errorToaster(errorMsg);
                            } else {
                                errorToaster(xhr.responseJSON?.message || 'Error saving user');
                            }
                        },
                        complete: function () {
                            hideLoader();
                            button.removeAttr("data-kt-indicator");
                            button.prop("disabled", false);
                        }
                    });
                });
        });

        // Helper Functions
        function resetForm() {
            $('#userForm')[0].reset();
            $('#userId').val('');
        }
    </script>
@endsection