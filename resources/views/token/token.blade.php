@extends('layouts.app')

@section('title', 'Tokens')
@section('styles')
    <style>
        /* ── Sticky Last Column ─────────────────────── */
        #tokens-table thead tr th:last-child,
        #tokens-table tbody tr td:last-child {
            position: sticky !important;
            right: 0;
            z-index: 3;
            background: #fff;
            box-shadow: -3px 0 8px rgba(0, 0, 0, 0.08);
            min-width: 180px;
        }

        #tokens-table thead tr th:last-child {
            background: #f9f9f9;
            z-index: 4;
        }

        /* Active row highlight */
        #tokens-table tbody tr.table-active td {
            background-color: #e8f4ff !important;
        }

        #tokens-table tbody tr.table-active td:last-child {
            background-color: #e8f4ff !important;
        }

        /* Dark mode support */
        [data-theme="dark"] #tokens-table thead tr th:last-child,
        [data-theme="dark"] #tokens-table tbody tr td:last-child {
            background: #1e1e2d;
            box-shadow: -3px 0 8px rgba(0, 0, 0, 0.3);
        }

        /* Table wrapper scroll */
        .table-responsive {
            overflow-x: auto;
            position: relative;
        }

        #tokens-table thead tr th:last-child,
        #tokens-table tbody tr td:last-child {
            position: sticky !important;
            right: 0;
            z-index: 3;
            background: #fff;
            box-shadow: -3px 0 8px rgba(0, 0, 0, 0.08);
            min-width: 120px;
        }

        #tokens-table thead tr th:last-child {
            background: #f9f9f9;
            z-index: 4;
        }
    </style>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="card mb-5 mb-xl-8 mt-5 shadow-sm">
            <div class="card-header" style="border-top: 2px solid #009ef7 !important">
                <h3 class="card-title">Manage Tokens</h3>
                <div class="card-toolbar d-flex gap-2">

                    {{-- Filter Count Badge --}}
                    <span id="activeFilterCount" class="badge badge-circle badge-primary d-none"
                        style="position:relative; top:-8px; left:-8px; font-size:10px;"></span>

                    <button class="btn btn-sm btn-light-primary" id="btnToggleFilters">
                        <i class="fas fa-filter me-1"></i> Filters
                        <span id="filterBadge" class="badge badge-sm badge-primary ms-1 d-none">0</span>
                    </button>

                    <button class="btn btn-sm btn-light-success" id="btnSaveFilter">
                        <i class="fas fa-save me-1"></i> Save Filter
                    </button>
                </div>
            </div>

            <div class="card-body py-3">

                {{-- ===== SAVED FILTERS BAR ===== --}}
                <div class="d-flex align-items-center flex-wrap gap-2 mb-3" id="savedFiltersBar">
                    <span class="fw-semibold text-muted fs-7">
                        <i class="fas fa-bookmark me-1"></i>Saved:
                    </span>
                    <div id="savedFilterPills" class="d-flex flex-wrap gap-2"></div>
                </div>

                {{-- ===== ACTIVE FILTER TAGS ===== --}}
                <div id="activeFilterTags" class="d-flex flex-wrap gap-2 mb-3"></div>

                {{-- ===== TABLE ===== --}}
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

    {{-- ═══════════════════════════════════════════════ --}}
    {{-- FILTER DRAWER (Right to Left)                  --}}
    {{-- ═══════════════════════════════════════════════ --}}
    <div id="filterDrawerOverlay"
        style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.4); z-index:1040; transition:opacity 0.3s;"
        onclick="closeFilterDrawer()">
    </div>

    <div id="filterDrawer"
        style="position:fixed; top:0; right:-480px; width:460px; height:100vh; background:#fff;
           z-index:1050; box-shadow:-4px 0 20px rgba(0,0,0,0.15); transition:right 0.3s ease;
           display:flex; flex-direction:column; overflow:hidden;">

        {{-- Drawer Header --}}
        <div class="d-flex align-items-center justify-content-between px-5 py-4 border-bottom"
            style="background: linear-gradient(135deg, #009ef7, #0078d4); flex-shrink:0;">
            <div>
                <h5 class="fw-bold text-white mb-0">
                    <i class="fas fa-sliders-h me-2"></i>Advanced Filters
                </h5>
                <span class="text-white opacity-75 fs-7">Filter tokens by any field</span>
            </div>
            <button onclick="closeFilterDrawer()" class="btn btn-sm btn-icon btn-active-light-primary"
                style="background:rgba(255,255,255,0.2);">
                <i class="fas fa-times text-white fs-5"></i>
            </button>
        </div>

        {{-- Drawer Body (Scrollable) --}}
        <div class="flex-grow-1 overflow-auto px-5 py-4">
            <div class="row g-4">

                {{-- Application Type --}}
                <div class="col-12">
                    <label class="form-label fw-semibold fs-7 text-gray-600 mb-1">
                        <i class="fas fa-layer-group me-1 text-primary"></i>Application Type
                    </label>
                    <select class="form-select form-select-sm form-select-solid filter-field" id="filterType">
                        <option value="all">All Applications</option>
                        <option value="token_applications">Pending Applications</option>
                        <option value="approved_applications">Approved Applications</option>
                    </select>
                </div>

                {{-- Token Status --}}
                <div class="col-12">
                    <label class="form-label fw-semibold fs-7 text-gray-600 mb-1">
                        <i class="fas fa-toggle-on me-1 text-primary"></i>Token Status
                    </label>
                    <select class="form-select form-select-sm form-select-solid filter-field" id="filterStatus">
                        <option value="">All Statuses</option>
                        <option value="Pending">Pending</option>
                        <option value="Approved">Approved</option>
                        <option value="Disapproved">Disapproved</option>
                    </select>
                </div>

                {{-- User Type --}}
                <div class="col-12">
                    <label class="form-label fw-semibold fs-7 text-gray-600 mb-1">
                        <i class="fas fa-user-tag me-1 text-primary"></i>User Type
                    </label>
                    <select class="form-select form-select-sm form-select-solid filter-field" id="filterUserType">
                        <option value="">All User Types</option>
                        <option value="normal_person">Normal</option>
                        <option value="working_lady">Working Lady</option>
                    </select>
                </div>

                {{-- Service Type --}}
                <div class="col-12">
                    <label class="form-label fw-semibold fs-7 text-gray-600 mb-1">
                        <i class="fas fa-concierge-bell me-1 text-primary"></i>Service Type
                    </label>
                    <select class="form-select form-select-sm form-select-solid filter-field" id="filterServiceType">
                        <option value="">All Services</option>
                        <option value="Type A">Type A</option>
                        <option value="Type B">Type B</option>
                        <option value="Type C">Type C</option>
                    </select>
                </div>

                {{-- Venue --}}
                <div class="col-12">
                    <label class="form-label fw-semibold fs-7 text-gray-600 mb-1">
                        <i class="fas fa-map-marker-alt me-1 text-primary"></i>Venue
                    </label>
                    <select class="form-select form-select-sm form-select-solid filter-field" id="filterVenue">
                        <option value="">All Venues</option>
                        @foreach (\App\Models\Venue::where('status', 'Active')->get() as $venue)
                            <option value="{{ $venue->id }}">{{ $venue->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Token Code --}}
                <div class="col-12">
                    <label class="form-label fw-semibold fs-7 text-gray-600 mb-1">
                        <i class="fas fa-barcode me-1 text-primary"></i>Token Code
                    </label>
                    <input type="text" class="form-control form-control-sm form-control-solid filter-field"
                        id="filterTokenCode" placeholder="Search token code..." />
                </div>

                {{-- Token Number --}}
                <div class="col-12">
                    <label class="form-label fw-semibold fs-7 text-gray-600 mb-1">
                        <i class="fas fa-hashtag me-1 text-primary"></i>Token Number
                    </label>
                    <input type="text" class="form-control form-control-sm form-control-solid filter-field"
                        id="filterTokenNumber" placeholder="Search token number..." />
                </div>

                {{-- Name --}}
                <div class="col-12">
                    <label class="form-label fw-semibold fs-7 text-gray-600 mb-1">
                        <i class="fas fa-user me-1 text-primary"></i>Name
                    </label>
                    <input type="text" class="form-control form-control-sm form-control-solid filter-field"
                        id="filterName" placeholder="Search name..." />
                </div>

                {{-- City --}}
                <div class="col-12">
                    <label class="form-label fw-semibold fs-7 text-gray-600 mb-1">
                        <i class="fas fa-city me-1 text-primary"></i>City
                    </label>
                    <input type="text" class="form-control form-control-sm form-control-solid filter-field"
                        id="filterCity" placeholder="Search city..." />
                </div>

                {{-- Phone --}}
                <div class="col-12">
                    <label class="form-label fw-semibold fs-7 text-gray-600 mb-1">
                        <i class="fas fa-phone me-1 text-primary"></i>Phone Number
                    </label>
                    <input type="text" class="form-control form-control-sm form-control-solid filter-field"
                        id="filterPhone" placeholder="Search phone..." />
                </div>

                {{-- Created At --}}
                <div class="col-12">
                    <label class="form-label fw-semibold fs-7 text-gray-600 mb-1">
                        <i class="fas fa-calendar-plus me-1 text-primary"></i>Created Date Range
                    </label>
                    <input class="form-control form-control-sm form-control-solid filter-field"
                        placeholder="Pick date range" id="filterCreatedAt" readonly />
                </div>

                {{-- Last Phone Date --}}
                <div class="col-12">
                    <label class="form-label fw-semibold fs-7 text-gray-600 mb-1">
                        <i class="fas fa-calendar-check me-1 text-primary"></i>Last Phone Date Range
                    </label>
                    <input class="form-control form-control-sm form-control-solid filter-field"
                        placeholder="Pick date range" id="filterLastPhoneDate" readonly />
                </div>

                {{-- Has Photo --}}
                <div class="col-12">
                    <label class="form-label fw-semibold fs-7 text-gray-600 mb-1">
                        <i class="fas fa-camera me-1 text-primary"></i>Photo
                    </label>
                    <select class="form-select form-select-sm form-select-solid filter-field" id="filterHasPhoto">
                        <option value="">All</option>
                        <option value="yes">Has Photo</option>
                        <option value="no">No Photo</option>
                    </select>
                </div>

                {{-- Per Page --}}
                <div class="col-12">
                    <label class="form-label fw-semibold fs-7 text-gray-600 mb-1">
                        <i class="fas fa-list-ol me-1 text-primary"></i>Records Per Page
                    </label>
                    <select class="form-select form-select-sm form-select-solid filter-field" id="customLengthSelect">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="-1">All</option>
                    </select>
                </div>

                {{-- Global Search --}}
                <div class="col-12">
                    <label class="form-label fw-semibold fs-7 text-gray-600 mb-1">
                        <i class="fas fa-search me-1 text-primary"></i>Global Search
                    </label>
                    <input type="text" id="customSearchInput"
                        class="form-control form-control-sm form-control-solid filter-field"
                        placeholder="Search anything..." />
                </div>

            </div>
        </div>

        {{-- Drawer Footer --}}
        <div class="d-flex gap-2 px-5 py-4 border-top" style="flex-shrink:0; background:#f9f9f9;">
            <button class="btn btn-light-danger flex-grow-1" id="btnClearFilters">
                <i class="fas fa-times me-1"></i>Clear All
            </button>
            <button class="btn btn-primary flex-grow-1" id="btnApplyFilters">
                <i class="fas fa-search me-1"></i>Apply Filters
            </button>
        </div>

    </div>

    {{-- ═══════════════════════════════════════════════ --}}
    {{-- FLOATING ACTION PANEL                          --}}
    {{-- ═══════════════════════════════════════════════ --}}
    <div id="floatingActionPanel"
        style="display:none; position:fixed; bottom:30px; right:30px; z-index:999;
           background:#fff; border-radius:12px; box-shadow:0 8px 32px rgba(0,0,0,0.18);
           padding:16px 20px; min-width:280px; border:1px solid #e0e0e0;">

        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <span class="fw-bold fs-6 text-dark">Selected Token</span>
                <div class="fs-7 text-muted" id="floatingTokenInfo">—</div>
            </div>
            <button onclick="closeFloatingPanel()" class="btn btn-sm btn-icon btn-light-danger">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="d-flex flex-column gap-2">
            <button class="btn btn-success btn-sm" onclick="confirmFloatingAction('Approved')">
                <i class="fas fa-check me-1"></i> Approve Token
            </button>
            <button class="btn btn-danger btn-sm" onclick="confirmFloatingAction('Disapproved')">
                <i class="fas fa-ban me-1"></i> Disapprove Token
            </button>
            <button class="btn btn-info btn-sm d-none" id="floatingCancelBtn" onclick="confirmFloatingAction('Pending')">
                <i class="fas fa-undo me-1"></i> Mark as Pending
            </button>
        </div>
    </div>

    {{-- ===== SAVE FILTER MODAL ===== --}}
    <div class="modal fade" id="saveFilterModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-500px">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="fas fa-save me-2 text-primary"></i>Save Current Filter
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-4">
                        <label class="form-label fw-semibold required">Filter Name</label>
                        <input type="text" class="form-control form-control-solid" id="saveFilterName"
                            placeholder="e.g. Pending Local Users Last 7 Days" maxlength="50" />
                        <div class="text-danger fs-7 mt-1 d-none" id="saveFilterNameError"></div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-muted">Active Filters to be Saved</label>
                        <div id="saveFilterPreview" class="bg-light rounded p-3 fs-7 border"></div>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="saveFilterSetDefault" />
                        <label class="form-check-label fw-semibold" for="saveFilterSetDefault">
                            Set as default (auto-apply on page load)
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="btnConfirmSaveFilter">
                        <span class="indicator-label">
                            <i class="fas fa-save me-1"></i>Save Filter
                        </span>
                        <span class="indicator-progress d-none">
                            <span class="spinner-border spinner-border-sm me-1"></span>Saving...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // ═══════════════════════════════════════════════
        // FILTER FIELD DEFINITIONS
        // ═══════════════════════════════════════════════
        const FILTER_FIELDS = [{
                id: 'filterType',
                label: 'Application Type',
                type: 'select',
                default: 'all'
            },
            {
                id: 'filterStatus',
                label: 'Token Status',
                type: 'select',
                default: ''
            },
            {
                id: 'filterUserType',
                label: 'User Type',
                type: 'select',
                default: ''
            },
            {
                id: 'filterServiceType',
                label: 'Service Type',
                type: 'select',
                default: ''
            },
            {
                id: 'filterVenue',
                label: 'Venue',
                type: 'select',
                default: ''
            },
            {
                id: 'filterTokenCode',
                label: 'Token Code',
                type: 'text',
                default: ''
            },
            {
                id: 'filterTokenNumber',
                label: 'Token Number',
                type: 'text',
                default: ''
            },
            {
                id: 'filterName',
                label: 'Name',
                type: 'text',
                default: ''
            },
            {
                id: 'filterCity',
                label: 'City',
                type: 'text',
                default: ''
            },
            {
                id: 'filterPhone',
                label: 'Phone',
                type: 'text',
                default: ''
            },
            {
                id: 'filterCreatedAt',
                label: 'Created Date',
                type: 'daterange',
                default: ''
            },
            {
                id: 'filterLastPhoneDate',
                label: 'Last Phone Date',
                type: 'daterange',
                default: ''
            },
            {
                id: 'filterHasPhoto',
                label: 'Photo',
                type: 'select',
                default: ''
            },
            {
                id: 'customLengthSelect',
                label: 'Per Page',
                type: 'select',
                default: '10'
            },
            {
                id: 'customSearchInput',
                label: 'Search',
                type: 'text',
                default: ''
            },
        ];

        // Currently selected token for floating panel
        let selectedTokenId = null;
        let selectedTokenData = null;

        $(document).ready(function() {
            initializeDateRangePickers();
            getTokensData();
            loadSavedFilters();

            // Open drawer
            $('#btnToggleFilters').on('click', openFilterDrawer);

            // Apply filters
            $('#btnApplyFilters').on('click', function() {
                applyFiltersToTable();
                renderActiveFilterTags();
                updateFilterBadge();
                closeFilterDrawer();
            });

            // Clear all
            $('#btnClearFilters').on('click', clearAllFilters);

            // Save filter
            $('#btnSaveFilter').on('click', showSaveFilterModal);

            // Confirm save
            $('#btnConfirmSaveFilter').on('click', saveCurrentFilter);

            // Enter = apply
            $(document).on('keypress', '.filter-field', function(e) {
                if (e.which === 13) {
                    applyFiltersToTable();
                    renderActiveFilterTags();
                    updateFilterBadge();
                    closeFilterDrawer();
                }
            });

            // Close drawer on ESC
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape') closeFilterDrawer();
            });
        });

        // ═══════════════════════════════════════════════
        // DRAWER OPEN / CLOSE
        // ═══════════════════════════════════════════════
        function openFilterDrawer() {
            $('#filterDrawerOverlay').fadeIn(200);
            $('#filterDrawer').css('right', '0');
            $('body').css('overflow', 'hidden');
        }

        function closeFilterDrawer() {
            $('#filterDrawerOverlay').fadeOut(200);
            $('#filterDrawer').css('right', '-480px');
            $('body').css('overflow', '');
        }

        // ═══════════════════════════════════════════════
        // DATE RANGE PICKERS
        // ═══════════════════════════════════════════════
        function initializeDateRangePickers() {
            const pickerOptions = {
                autoUpdateInput: false,
                timePicker: true,
                timePicker24Hour: true,
                timePickerSeconds: true,
                locale: {
                    format: 'YYYY-MM-DD HH:mm:ss',
                    cancelLabel: 'Clear'
                },
                ranges: {
                    'Today': [moment().startOf('day'), moment().endOf('day')],
                    'Yesterday': [moment().subtract(1, 'days').startOf('day'), moment().subtract(1, 'days').endOf(
                        'day')],
                    'Last 7 Days': [moment().subtract(6, 'days').startOf('day'), moment().endOf('day')],
                    'Last 30 Days': [moment().subtract(29, 'days').startOf('day'), moment().endOf('day')],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf(
                        'month')],
                }
            };

            $('#filterCreatedAt, #filterLastPhoneDate').daterangepicker(pickerOptions);

            $('#filterCreatedAt, #filterLastPhoneDate').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(
                    picker.startDate.format('YYYY-MM-DD HH:mm:ss') +
                    ' - ' +
                    picker.endDate.format('YYYY-MM-DD HH:mm:ss')
                );
            });

            $('#filterCreatedAt, #filterLastPhoneDate').on('cancel.daterangepicker', function() {
                $(this).val('');
            });
        }

        // ═══════════════════════════════════════════════
        // GET CURRENT FILTERS
        // ═══════════════════════════════════════════════
        function getCurrentFilters() {
            const filters = {};
            FILTER_FIELDS.forEach(f => {
                filters[f.id] = $('#' + f.id).val() || f.default;
            });
            return filters;
        }

        function applyFiltersToUI(filters) {
            FILTER_FIELDS.forEach(f => {
                if (filters[f.id] !== undefined) $('#' + f.id).val(filters[f.id]);
            });
        }

        function applyFiltersToTable() {
            const filters = getCurrentFilters();
            if (window.tokensTable) {
                window.tokensTable.page.len(parseInt(filters.customLengthSelect || 10));
                window.tokensTable.search(filters.customSearchInput || '');
                window.tokensTable.ajax.reload();
            }
        }

        // ═══════════════════════════════════════════════
        // FILTER BADGE COUNT
        // ═══════════════════════════════════════════════
        function updateFilterBadge() {
            const filters = getCurrentFilters();
            let count = 0;
            FILTER_FIELDS.forEach(f => {
                const val = filters[f.id];
                if (val && val !== f.default) count++;
            });

            if (count > 0) {
                $('#filterBadge').text(count).removeClass('d-none');
                $('#btnToggleFilters').removeClass('btn-light-primary').addClass('btn-primary');
            } else {
                $('#filterBadge').addClass('d-none');
                $('#btnToggleFilters').removeClass('btn-primary').addClass('btn-light-primary');
            }
        }

        // ═══════════════════════════════════════════════
        // CLEAR FILTERS
        // ═══════════════════════════════════════════════
        function clearAllFilters() {
            FILTER_FIELDS.forEach(f => $('#' + f.id).val(f.default));
            $('#activeFilterTags').empty();
            $('.saved-filter-pill').removeClass('badge-primary text-white').addClass('badge-light-primary');
            updateFilterBadge();
            applyFiltersToTable();
        }

        function clearSingleFilter(fieldId, defaultVal) {
            $('#' + fieldId).val(defaultVal);
            applyFiltersToTable();
            renderActiveFilterTags();
            updateFilterBadge();
        }

        // ═══════════════════════════════════════════════
        // ACTIVE FILTER TAGS
        // ═══════════════════════════════════════════════
        function renderActiveFilterTags() {
            const container = $('#activeFilterTags');
            container.empty();

            const filters = getCurrentFilters();

            FILTER_FIELDS.forEach(function(field) {
                const val = filters[field.id];
                if (!val || val === field.default) return;

                let displayVal = val;
                if (field.type === 'select') {
                    const text = $('#' + field.id + ' option:selected').text();
                    if (text) displayVal = text;
                }

                container.append($(`
            <span class="badge badge-light-primary d-inline-flex align-items-center gap-1 px-3 py-2 fs-7">
                <span class="text-muted">${field.label}:</span>
                <strong>${displayVal}</strong>
                <i class="fas fa-times ms-1 cursor-pointer text-danger"
                   onclick="clearSingleFilter('${field.id}', '${field.default}')"></i>
            </span>
        `));
            });
        }

        // ═══════════════════════════════════════════════
        // SAVED FILTERS
        // ═══════════════════════════════════════════════
        function loadSavedFilters() {
            $.get('{{ url('/saved-filters') }}', {
                page: 'tokens'
            }, function(response) {
                renderSavedFilterPills(response.data);
                const def = response.data.find(f => f.is_default);
                if (def) {
                    applyFiltersToUI(def.filters);
                    applyFiltersToTable();
                    renderActiveFilterTags();
                    updateFilterBadge();
                    highlightActivePill(def.id);
                }
            });
        }

        function renderSavedFilterPills(filters) {
            const container = $('#savedFilterPills');
            container.empty();

            if (!filters || !filters.length) {
                container.html('<span class="text-muted fs-7 fst-italic">No saved filters yet.</span>');
                return;
            }

            filters.forEach(function(filter) {
                const starIcon = filter.is_default ?
                    '<i class="fas fa-star text-warning ms-1 fs-8" title="Default"></i>' :
                    '<i class="far fa-star text-muted ms-1 fs-8 pill-star" title="Set as default"></i>';

                const pill = $(`
            <div class="saved-filter-pill d-inline-flex align-items-center gap-1 badge badge-light-primary px-3 py-2 fs-7 cursor-pointer"
                 data-id="${filter.id}" style="border:1px solid #d0e8ff;">
                <i class="fas fa-filter fs-8 text-primary"></i>
                <span>${filter.name}</span>
                ${starIcon}
                <i class="fas fa-times text-danger ms-1 fs-8 pill-delete"></i>
            </div>
        `);

                pill.on('click', function(e) {
                    if ($(e.target).hasClass('pill-delete') || $(e.target).hasClass('pill-star')) return;
                    applyFiltersToUI(filter.filters);
                    applyFiltersToTable();
                    renderActiveFilterTags();
                    updateFilterBadge();
                    highlightActivePill(filter.id);
                });

                pill.find('.pill-star').on('click', function(e) {
                    e.stopPropagation();
                    setDefaultFilter(filter.id);
                });

                pill.find('.pill-delete').on('click', function(e) {
                    e.stopPropagation();
                    deleteFilter(filter.id);
                });

                container.append(pill);
            });
        }

        function highlightActivePill(id) {
            $('.saved-filter-pill').removeClass('badge-primary text-white').addClass('badge-light-primary');
            $(`.saved-filter-pill[data-id="${id}"]`).removeClass('badge-light-primary').addClass(
                'badge-primary text-white');
        }

        function setDefaultFilter(id) {
            $.ajax({
                url: `{{ url('/saved-filters') }}/${id}/default`,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: r => {
                    if (typeof successToaster === 'function') successToaster(r.message);
                    loadSavedFilters();
                }
            });
        }

        function deleteFilter(id) {
            const doDelete = () => $.ajax({
                url: `{{ url('/saved-filters') }}/${id}`,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: r => {
                    if (typeof successToaster === 'function') successToaster(r.message);
                    loadSavedFilters();
                }
            });

            if (typeof showConfirmation === 'function') {
                showConfirmation('Delete this saved filter?').then(r => {
                    if (r.isConfirmed) doDelete();
                });
            } else {
                if (confirm('Delete this saved filter?')) doDelete();
            }
        }

        // ═══════════════════════════════════════════════
        // SAVE FILTER MODAL
        // ═══════════════════════════════════════════════
        function showSaveFilterModal() {
            $('#saveFilterName').val('');
            $('#saveFilterNameError').addClass('d-none').text('');
            $('#saveFilterSetDefault').prop('checked', false);

            const filters = getCurrentFilters();
            let previewHtml = '';
            let hasAny = false;

            FILTER_FIELDS.forEach(function(field) {
                const val = filters[field.id];
                if (!val || val === field.default) return;
                let displayVal = val;
                if (field.type === 'select') {
                    const text = $('#' + field.id + ' option:selected').text();
                    if (text) displayVal = text;
                }
                previewHtml += `
            <div class="d-flex justify-content-between border-bottom py-1">
                <span class="text-muted">${field.label}</span>
                <strong class="text-dark">${displayVal}</strong>
            </div>`;
                hasAny = true;
            });

            $('#saveFilterPreview').html(
                hasAny ? previewHtml : '<div class="text-muted fst-italic">No active filters set.</div>'
            );

            $('#saveFilterModal').modal('show');
        }

        function saveCurrentFilter() {
            const name = $('#saveFilterName').val().trim();
            if (!name) {
                $('#saveFilterNameError').text('Please enter a filter name.').removeClass('d-none');
                return;
            }

            $('#btnConfirmSaveFilter .indicator-label').addClass('d-none');
            $('#btnConfirmSaveFilter .indicator-progress').removeClass('d-none');
            $('#btnConfirmSaveFilter').prop('disabled', true);

            $.ajax({
                url: '{{ url('/saved-filters') }}',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                contentType: 'application/json',
                data: JSON.stringify({
                    name: name,
                    page: 'tokens',
                    filters: getCurrentFilters(),
                    is_default: $('#saveFilterSetDefault').is(':checked'),
                }),
                success: r => {
                    $('#saveFilterModal').modal('hide');
                    if (typeof successToaster === 'function') successToaster(r.message);
                    loadSavedFilters();
                },
                error: xhr => {
                    const msg = xhr.responseJSON?.message || 'Error saving filter.';
                    $('#saveFilterNameError').text(msg).removeClass('d-none');
                },
                complete: () => {
                    $('#btnConfirmSaveFilter .indicator-label').removeClass('d-none');
                    $('#btnConfirmSaveFilter .indicator-progress').addClass('d-none');
                    $('#btnConfirmSaveFilter').prop('disabled', false);
                }
            });
        }

        // ═══════════════════════════════════════════════
        // FLOATING ACTION PANEL
        // ═══════════════════════════════════════════════
        function showFloatingPanel(row) {
            selectedTokenId = row.id;
            selectedTokenData = row;

            $('#floatingTokenInfo').html(
                `<span class="badge badge-light-primary me-1">${row.token_code || '—'}</span>` +
                `<span class="text-muted">${row.user_name || 'Unknown'}</span>`
            );

            // Show correct buttons based on status
            if (row.status === 'Pending') {
                $('#floatingCancelBtn').addClass('d-none');
                $('#floatingActionPanel').find('.btn-success, .btn-danger').removeClass('d-none');
            } else {
                $('#floatingActionPanel').find('.btn-success, .btn-danger').addClass('d-none');
                $('#floatingCancelBtn').removeClass('d-none');
            }

            $('#floatingActionPanel').fadeIn(200);
        }

        function closeFloatingPanel() {
            $('#floatingActionPanel').fadeOut(200);
            selectedTokenId = null;
            selectedTokenData = null;
            // Remove row highlight
            $('#tokens-table tbody tr').removeClass('table-active');
        }

        function confirmFloatingAction(status) {
            if (!selectedTokenId) return;

            const messages = {
                Approved: 'Approve this token?',
                Disapproved: 'Disapprove this token?',
                Pending: 'Mark this token back to Pending?',
            };

            if (typeof showConfirmation === 'function') {
                showConfirmation(messages[status]).then(r => {
                    if (r.isConfirmed) sendUpdate(selectedTokenId, status);
                });
            } else {
                sendUpdate(selectedTokenId, status);
            }
        }

        // ═══════════════════════════════════════════════
        // DATATABLE
        // ═══════════════════════════════════════════════
        function getTokensData() {
            const columns = [{
                    data: null,
                    orderable: true,
                    render: (data, type, row, meta) => meta.row + 1 + meta.settings._iDisplayStart
                },
                {
                    data: 'token_code'
                },
                {
                    data: 'token_number'
                },
                {
                    data: 'venue'
                },
                {
                    data: 'user_type'
                },
                {
                    data: 'service_type'
                },
                {
                    data: 'user_name',
                    render: d => d || 'N/A'
                },
                {
                    data: 'city',
                    render: d => d || 'N/A'
                },
                {
                    data: null,
                    orderable: false,
                    render: (d, t, row) => photoCell(row)
                },
                {
                    data: 'phone_number'
                },
                {
                    data: 'last_phone_date',
                    render: d => formatDateFriendly(d)
                },
                {
                    data: 'status',
                    render: statusBadge
                },
                {
                    data: 'created_at',
                    render: formatDateTimeWithAMPM
                },
                {
                    data: null,
                    orderable: false,
                    render: function(data, type, row) {
                        return ActionButtons(row);
                    }
                }
            ];

            const customOptions = {
                ajax: {
                    url: '{{ route('tokens.data') }}',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    contentType: 'application/json',
                    dataType: 'json',
                    data: function(d) {
                        const f = getCurrentFilters();
                        d.filter_type = f.filterType;
                        d.filter_status = f.filterStatus;
                        d.filter_user_type = f.filterUserType;
                        d.filter_service_type = f.filterServiceType;
                        d.filter_venue = f.filterVenue;
                        d.filter_token_code = f.filterTokenCode;
                        d.filter_token_number = f.filterTokenNumber;
                        d.filter_name = f.filterName;
                        d.filter_city = f.filterCity;
                        d.filter_phone = f.filterPhone;
                        d.filter_created_at = f.filterCreatedAt;
                        d.filter_last_phone = f.filterLastPhoneDate;
                        d.filter_has_photo = f.filterHasPhoto;
                        return JSON.stringify(d);
                    },
                    beforeSend: () => {
                        if (typeof showLoader === 'function') showLoader();
                    },
                    complete: () => {
                        if (typeof hideLoader === 'function') hideLoader();
                    },
                    error: () => {
                        if (typeof errorToaster === 'function') errorToaster('Error loading data.');
                    }
                },
                // Row click handler
                createdRow: function(row, data) {
                    $(row).css('cursor', 'pointer');
                    // Select button click
                    $(row).find('.btn-select-row').on('click', function(e) {
                        e.stopPropagation();
                        $('#tokens-table tbody tr').removeClass('table-active');
                        $(row).addClass('table-active');
                        showFloatingPanel(data);
                    });
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
            return row.user_image_path ?
                `<img src="/storage/${row.user_image_path}" style="width:60px;height:60px;border-radius:6px;object-fit:cover;" />` :
                '<span class="badge badge-light-secondary">No Photo</span>';
        }

        function sendUpdate(id, status) {
            if (typeof showLoader === 'function') showLoader();
            $.ajax({
                url: `{{ url('/tokens') }}/${id}/status`,
                type: 'POST',
                data: {
                    status
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                },
                success: r => {
                    if (typeof successToaster === 'function') successToaster(r.message || 'Status updated');
                    closeFloatingPanel();
                    $('#tokens-table').DataTable().ajax.reload(null, false);
                },
                error: xhr => {
                    if (typeof errorToaster === 'function') errorToaster(xhr.responseJSON?.message || 'Error');
                },
                complete: () => {
                    if (typeof hideLoader === 'function') hideLoader();
                }
            });
        }

        function updateTokenStatus(id, status) {
            const messages = {
                Approved: 'Are you sure you want to Approve this token?',
                Disapproved: 'Are you sure you want to Disapprove this token?',
                Pending: 'Are you sure you want to mark this token back to Pending?',
            };

            if (typeof showConfirmation === 'function') {
                showConfirmation(messages[status]).then(function(result) {
                    if (result.isConfirmed) {
                        sendUpdate(id, status);
                    }
                });
            } else {
                if (confirm(messages[status])) {
                    sendUpdate(id, status);
                }
            }
        }

        function sendUpdate(id, status) {
            if (typeof showLoader === 'function') showLoader();

            $.ajax({
                url: `{{ url('/tokens') }}/${id}/status`,
                type: 'POST',
                data: {
                    status: status
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                },
                success: function(response) {
                    if (typeof successToaster === 'function') {
                        successToaster(response.message || 'Status updated successfully.');
                    }
                    $('#tokens-table').DataTable().ajax.reload(null, false);
                },
                error: function(xhr) {
                    const message = xhr.responseJSON?.message || 'Something went wrong.';
                    if (typeof errorToaster === 'function') {
                        errorToaster(message);
                    }
                },
                complete: function() {
                    if (typeof hideLoader === 'function') hideLoader();
                }
            });
        }

        function ActionButtons(row) {
            if (row.status === 'Pending') {
                return `
            <div class="d-flex flex-column gap-1">
                <button onclick="updateTokenStatus('${row.id}', 'Approved')"
                    class="btn btn-xs btn-success"
                    style="font-size:11px; padding:3px 8px; white-space:nowrap;">
                    <i class="fas fa-check"></i> Approve
                </button>
                <button onclick="updateTokenStatus('${row.id}', 'Disapproved')"
                    class="btn btn-xs btn-danger"
                    style="font-size:11px; padding:3px 8px; white-space:nowrap;">
                    <i class="fas fa-times"></i> Disapprove
                </button>
            </div>`;
            }

            if (row.status === 'Approved') {
                return `
            <div class="d-flex flex-column gap-1">
                <button onclick="updateTokenStatus('${row.id}', 'Pending')"
                    class="btn btn-xs btn-light-warning"
                    style="font-size:11px; padding:3px 8px; white-space:nowrap;">
                    <i class="fas fa-undo"></i> Revert
                </button>
            </div>`;
            }

            if (row.status === 'Disapproved') {
                return `
            <div class="d-flex flex-column gap-1">
                <button onclick="updateTokenStatus('${row.id}', 'Pending')"
                    class="btn btn-xs btn-light-info"
                    style="font-size:11px; padding:3px 8px; white-space:nowrap;">
                    <i class="fas fa-undo"></i> Revert
                </button>
            </div>`;
            }

            return '<span class="text-muted">—</span>';
        }
    </script>
@endsection
