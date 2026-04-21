@extends('layouts.app')

@section('title', 'Site Admin - QR Scanner')

@section('content')
    <div class="container-fluid">
        <div class="card mb-5 mb-xl-8 mt-5 shadow-sm">
            <div class="card-header" style="border-top: 2px solid #009ef7 !important">
                <h3 class="card-title">Site Admin - QR Code Scanner</h3>
            </div>

            <div class="card-body py-5">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="text-center mb-5">
                            <h4 class="mb-3">Scan QR Code with Scanner Gun</h4>
                            <p class="text-muted">Focus on the input field below and scan the QR code using your scanner gun</p>
                        </div>

                        <!-- Scanner Input Field -->
                        <div class="mb-5">
                            <label for="qrCodeInput" class="form-label fs-4 fw-bold">QR Code Scanner Input</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text">
                                    <i class="fas fa-qrcode fs-2"></i>  
                                </span>
                                <input 
                                    type="text" 
                                    id="qrCodeInput" 
                                    class="form-control form-control-lg" 
                                    placeholder="Click here and scan QR code..."
                                    autocomplete="off"
                                    autofocus
                                />
                            </div>
                            <div class="form-text">The scanned GUID will appear here automatically</div>
                        </div>

                        <!-- Loading Indicator -->
                        <div id="loadingIndicator" class="text-center mb-3" style="display: none;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Searching for token...</p>
                        </div>

                        <!-- Error Message -->
                        <div id="errorMessage" class="alert alert-danger" style="display: none;">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span id="errorText"></span>
                        </div>

                        <!-- Success Message -->
                        <div id="successMessage" class="alert alert-success" style="display: none;">
                            <i class="fas fa-check-circle"></i>
                            Token found! Opening details...
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Token Details Modal -->
    <div class="modal fade" id="tokenDetailsModal" tabindex="-1" aria-labelledby="tokenDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white" id="tokenDetailsModalLabel">
                        <i class="fas fa-ticket-alt me-2"></i>Token Details
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="tokenDetailsContent">
                        <!-- Token details will be loaded here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="printToken()">
                        <i class="fas fa-print me-2"></i>Print
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            // Auto-focus on the input field
            $('#qrCodeInput').focus();

            // Keep focus on input after modal closes
            $('#tokenDetailsModal').on('hidden.bs.modal', function () {
                $('#qrCodeInput').val('').focus();
            });

            // Add space bar listener for printing when modal is open
            $(document).on('keydown', function(e) {
                if (e.which === 32 && $('#tokenDetailsModal').hasClass('show')) { // Space bar
                    e.preventDefault();
                    printToken();
                }
            });

            // Handle input change (when QR code is scanned)
            $('#qrCodeInput').on('input', function() {
                const scannedValue = $(this).val().trim();
                
                // Check if the input looks like a GUID
                if (scannedValue.length >= 32) {
                    searchToken(scannedValue);
                }
            });

            // Also handle Enter key press
            $('#qrCodeInput').on('keypress', function(e) {
                if (e.which === 13) { // Enter key
                    e.preventDefault();
                    const scannedValue = $(this).val().trim();
                    if (scannedValue) {
                        searchToken(scannedValue);
                    }
                }
            });
        });

        function searchToken(tokenId) {
            // Hide previous messages
            $('#errorMessage, #successMessage').hide();
            
            // Show loading indicator
            $('#loadingIndicator').show();

            // Make AJAX request to search for token
            $.ajax({
                url: '{{ route("tokens.search") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    token_id: tokenId
                },
                success: function(response) {
                    $('#loadingIndicator').hide();
                    
                    if (response.success) {
                        // Show success message
                        $('#successMessage').show();
                        
                        // Display token details in modal
                        displayTokenDetails(response.data);
                        
                        // Hide success message after a short delay
                        setTimeout(function() {
                            $('#successMessage').hide();
                        }, 2000);
                    } else {
                        showError(response.message || 'Token not found');
                    }
                },
                error: function(xhr, status, error) {
                    $('#loadingIndicator').hide();
                    
                    let errorMessage = 'An error occurred while searching for the token';
                    
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    
                    showError(errorMessage);
                }
            });
        }

        function showError(message) {
            $('#errorText').text(message);
            $('#errorMessage').show();
            
            // Hide error after 5 seconds
            setTimeout(function() {
                $('#errorMessage').hide();
            }, 5000);
        }

        function displayTokenDetails(token) {
            let statusBadge = '';
            switch(token.status) {
                case 'pending':
                    statusBadge = '<span class="badge badge-warning">Pending</span>';
                    break;
                case 'approved':
                    statusBadge = '<span class="badge badge-success">Approved</span>';
                    break;
                case 'rejected':
                    statusBadge = '<span class="badge badge-danger">Rejected</span>';
                    break;
                default:
                    statusBadge = '<span class="badge badge-secondary">' + token.status + '</span>';
            }

            const photoUrl = token.user_image_path ? '{{ asset("storage") }}/' + token.user_image_path : '{{ asset("assets/media/avatars/blank.png") }}';
            const qrCodeUrl = token.qr_code_path ? '{{ asset("storage") }}/' + token.qr_code_path : '';

            // Check for already checked in or printed
            let warningMessages = '';
            if (token.checked_in_count > 1) {
                warningMessages += `
                    <div class="alert alert-warning alert-dismissible fade show mb-3" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Already Checked In!</strong> This token has been checked in ${token.checked_in_count - 1} time(s) before.
                    </div>
                `;
            }
            if (token.print_count > 0) {
                warningMessages += `
                    <div class="alert alert-info alert-dismissible fade show mb-3" role="alert">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Already Printed!</strong> This token has been printed ${token.print_count} time(s) before.
                    </div>
                `;
            }

            const html = `
                ${warningMessages}
                <div class="row screen-only">
                    <div class="col-md-4 text-center mb-4">
                        <div class="mb-3">
                            <h6 class="text-muted mb-2">User Photo</h6>
                            <img src="${photoUrl}" alt="User Photo" class="img-fluid rounded mb-3 border" style="max-height: 200px;">
                        </div>
                        ${qrCodeUrl ? `
                        <div>
                            <h6 class="text-muted mb-2">QR Code</h6>
                            <img src="${qrCodeUrl}" alt="QR Code" class="img-fluid border p-2 bg-white" style="max-width: 180px;">
                        </div>
                        ` : `
                        <div class="alert alert-light">
                            <i class="fas fa-qrcode"></i> No QR Code Available
                        </div>
                        `}
                    </div>
                    <div class="col-md-8">
                        <table class="table table-row-bordered">
                            <tbody>
                                <tr>
                                    <td class="fw-bold">Token Code:</td>
                                    <td>${token.token_code || 'N/A'}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Token Number:</td>
                                    <td>${token.token_number || 'N/A'}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Status:</td>
                                    <td>${statusBadge}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">User Name:</td>
                                    <td>${token.user_name || 'N/A'}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">User Type:</td>
                                    <td>${token.user_type || 'N/A'}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Service Type:</td>
                                    <td>${token.service_type || 'N/A'}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">City:</td>
                                    <td>${token.city || 'N/A'}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Phone Number:</td>
                                    <td>${token.phone_number || 'N/A'}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Venue:</td>
                                    <td>${token.venue ? token.venue.venue_name : 'N/A'}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Created At:</td>
                                    <td>${token.created_at ? new Date(token.created_at).toLocaleString() : 'N/A'}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Print-Only Token Card -->
                <div class="print-only">
                    <div class="token-card">
                        <div class="token-header">
                            <h2>TOKEN</h2>
                        </div>
                        <div class="token-body">
                            ${qrCodeUrl ? `
                            <div class="qr-section">
                                <img src="${qrCodeUrl}" alt="QR Code" class="qr-code-print">
                            </div>
                            ` : ''}
                            <div class="token-details">
                                <div class="detail-row">
                                    <span class="label">Token Code:</span>
                                    <span class="value">${token.token_code || 'N/A'}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="label">Token Number:</span>
                                    <span class="value">${token.token_number || 'N/A'}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="label">Venue:</span>
                                    <span class="value">${token.venue ? token.venue.venue_name : 'N/A'}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="label">Phone:</span>
                                    <span class="value">${token.phone_number || 'N/A'}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="label">Date:</span>
                                    <span class="value">${token.created_at ? new Date(token.created_at).toLocaleDateString('en-US', { year: 'numeric', month: '2-digit', day: '2-digit' }) : 'N/A'}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            $('#tokenDetailsContent').html(html);
            // Store token ID and counts for later use
            $('#tokenDetailsModal').data('tokenId', token.id);
            $('#tokenDetailsModal').data('printCount', token.print_count);
            $('#tokenDetailsModal').modal('show');
        }

        function printToken() {
            const tokenId = $('#tokenDetailsModal').data('tokenId');
            
            if (!tokenId) {
                console.error('No token ID found');
                return;
            }

            // Update print count in database
            $.ajax({
                url: '{{ route("tokens.update-print-count") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    token_id: tokenId
                },
                success: function(response) {
                    if (response.success) {
                        // Update stored print count
                        const newPrintCount = response.print_count;
                        $('#tokenDetailsModal').data('printCount', newPrintCount);
                        
                        // Update warning message if it now exceeds threshold
                        if (newPrintCount > 0) {
                            const existingWarning = $('.alert-info');
                            if (existingWarning.length) {
                                existingWarning.html(`
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Already Printed!</strong> This token has been printed ${newPrintCount} time(s) before.
                                `);
                            }
                        }
                        
                        // Trigger print
                        window.print();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error updating print count:', error);
                    // Still print even if the count update fails
                    window.print();
                }
            });
        }
    </script>

    <style>
        /* Screen-only styles */
        .print-only {
            display: none;
        }

        @media print {
            /* Remove default headers/footers and page title */
            @page {
                margin: 0;
                size: auto;
            }
            
            html, body {
                margin: 0 !important;
                padding: 0 !important;
            }
            
            /* Hide everything except the token card */
            body * {
                visibility: hidden;
            }
            
            .print-only,
            .print-only * {
                visibility: visible;
            }
            
            .screen-only {
                display: none !important;
            }
            
            .print-only {
                display: block;
                position: fixed;
                left: 20px;
                top: 20px;
                width: auto;
                margin: 0;
                padding: 0;
            }
            
            /* Token card styles */
            .token-card {
                width: 400px;
                margin: 0;
                border: 3px solid #000;
                border-radius: 10px;
                padding: 20px;
                background: #fff;
                box-shadow: none;
                page-break-inside: avoid;
            }
            
            .token-header {
                text-align: center;
                border-bottom: 2px solid #009ef7;
                padding-bottom: 10px;
                margin-bottom: 20px;
            }
            
            .token-header h2 {
                margin: 0;
                font-size: 28px;
                font-weight: bold;
                color: #009ef7;
                letter-spacing: 2px;
            }
            
            .qr-section {
                text-align: center;
                margin-bottom: 20px;
                padding: 15px;
                background: #f8f9fa;
                border-radius: 8px;
            }
            
            .qr-code-print {
                width: 200px;
                height: 200px;
                border: 2px solid #000;
                padding: 10px;
                background: #fff;
            }
            
            .token-details {
                margin-top: 15px;
            }
            
            .detail-row {
                display: flex;
                justify-content: space-between;
                padding: 10px 0;
                border-bottom: 1px solid #ddd;
            }
            
            .detail-row:last-child {
                border-bottom: none;
            }
            
            .detail-row .label {
                font-weight: bold;
                color: #333;
                font-size: 14px;
            }
            
            .detail-row .value {
                color: #000;
                font-size: 14px;
                font-weight: 600;
            }
            
            /* Hide modal elements */
            .modal-header,
            .modal-footer,
            .btn-close,
            .alert {
                display: none !important;
            }
        }

        #qrCodeInput {
            font-size: 1.2rem;
            text-align: center;
            font-weight: bold;
        }

        #qrCodeInput:focus {
            border-color: #009ef7;
            box-shadow: 0 0 0 0.25rem rgba(0, 158, 247, 0.25);
        }
    </style>
@endsection
