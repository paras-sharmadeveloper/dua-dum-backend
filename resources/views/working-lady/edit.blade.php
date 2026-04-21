@extends('layouts.app')

@section('title', 'Edit Working Lady')

@section('content')
    <div class="container-fluid">
        <div class="card mb-5 mb-xl-8 mt-5 shadow-sm">
            <div class="card-header" style="border-top: 2px solid #009ef7 !important">
                <h3 class="card-title">View/Edit Working Lady</h3>
                <div class="card-toolbar">
                    <div class="d-flex flex-stack">
                        <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                            <a href="{{ route('working-lady.index') }}" class="btn btn-sm btn-primary">
                                Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body py-3">
                <form action="{{ route('working-lady.update', $workingLady->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- QR Code Display -->
                    <div class="row mb-5">
                        <div class="col-md-12">
                            <label class="d-flex align-items-center fs-7 fw-semibold form-label mb-1">
                                <span>QR Code</span>
                            </label>
                            <div class="d-flex align-items-center gap-3">
                                @if($workingLady->qr_code_path)
                                    <img src="{{ asset('storage/' . $workingLady->qr_code_path) }}" alt="QR Code" 
                                         style="width: 200px; height: 200px; border: 1px solid #ddd; padding: 10px; background: white;">
                                    <a href="{{ asset('storage/' . $workingLady->qr_code_path) }}" 
                                       download="working-lady-{{ $workingLady->id }}-qr.png" 
                                       class="btn btn-sm btn-success">
                                        <i class="bi bi-download"></i> Download QR Code
                                    </a>
                                @else
                                    <span class="text-muted">No QR code available</span>
                                @endif
                            </div>
                            <div class="form-text">QR Code cannot be edited</div>
                        </div>
                    </div>

                    <!-- First Row: First Name, Last Name -->
                    <div class="row mb-5">
                        <div class="col-md-6">
                            <label class="d-flex align-items-center fs-7 fw-semibold form-label mb-1">
                                <span class="required">First Name</span>
                            </label>
                            <div class="input-group input-group-sm flex-nowrap">
                                <span class="input-group-text py-1 px-2"><i class="bi bi-person-fill fs-5"></i></span>
                                <div class="overflow-hidden flex-grow-1">
                                    <input type="text" name="first_name" value="{{ old('first_name', $workingLady->first_name) }}"
                                        class="form-control form-control-sm rounded-start-0 @error('first_name') is-invalid @enderror"
                                        placeholder="Enter first name" required>
                                </div>
                            </div>
                            @error('first_name')
                                <div class="text-danger fs-7 mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="d-flex align-items-center fs-7 fw-semibold form-label mb-1">
                                <span class="required">Last Name</span>
                            </label>
                            <div class="input-group input-group-sm flex-nowrap">
                                <span class="input-group-text py-1 px-2"><i class="bi bi-person-fill fs-5"></i></span>
                                <div class="overflow-hidden flex-grow-1">
                                    <input type="text" name="last_name" value="{{ old('last_name', $workingLady->last_name) }}"
                                        class="form-control form-control-sm rounded-start-0 @error('last_name') is-invalid @enderror"
                                        placeholder="Enter last name" required>
                                </div>
                            </div>
                            @error('last_name')
                                <div class="text-danger fs-7 mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Second Row: Designation, Company Name -->
                    <div class="row mb-5">
                        <div class="col-md-6">
                            <label class="d-flex align-items-center fs-7 fw-semibold form-label mb-1">
                                <span class="required">Designation</span>
                            </label>
                            <div class="input-group input-group-sm flex-nowrap">
                                <span class="input-group-text py-1 px-2"><i class="bi bi-briefcase-fill fs-5"></i></span>
                                <div class="overflow-hidden flex-grow-1">
                                    <input type="text" name="designation" value="{{ old('designation', $workingLady->designation) }}"
                                        class="form-control form-control-sm rounded-start-0 @error('designation') is-invalid @enderror"
                                        placeholder="Enter designation" required>
                                </div>
                            </div>
                            @error('designation')
                                <div class="text-danger fs-7 mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="d-flex align-items-center fs-7 fw-semibold form-label mb-1">
                                <span class="required">Company Name</span>
                            </label>
                            <div class="input-group input-group-sm flex-nowrap">
                                <span class="input-group-text py-1 px-2"><i class="bi bi-building fs-5"></i></span>
                                <div class="overflow-hidden flex-grow-1">
                                    <input type="text" name="company_name" value="{{ old('company_name', $workingLady->company_name) }}"
                                        class="form-control form-control-sm rounded-start-0 @error('company_name') is-invalid @enderror"
                                        placeholder="Enter company name" required>
                                </div>
                            </div>
                            @error('company_name')
                                <div class="text-danger fs-7 mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Third Row: Place of Work, Email -->
                    <div class="row mb-5">
                        <div class="col-md-6">
                            <label class="d-flex align-items-center fs-7 fw-semibold form-label mb-1">
                                <span class="required">Place of Work</span>
                            </label>
                            <div class="input-group input-group-sm flex-nowrap">
                                <span class="input-group-text py-1 px-2"><i class="bi bi-geo-alt-fill fs-5"></i></span>
                                <div class="overflow-hidden flex-grow-1">
                                    <input type="text" name="place_of_work" value="{{ old('place_of_work', $workingLady->place_of_work) }}"
                                        class="form-control form-control-sm rounded-start-0 @error('place_of_work') is-invalid @enderror"
                                        placeholder="Enter place of work" required>
                                </div>
                            </div>
                            @error('place_of_work')
                                <div class="text-danger fs-7 mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="d-flex align-items-center fs-7 fw-semibold form-label mb-1">
                                <span class="required">Email</span>
                            </label>
                            <div class="input-group input-group-sm flex-nowrap">
                                <span class="input-group-text py-1 px-2"><i class="bi bi-envelope-fill fs-5"></i></span>
                                <div class="overflow-hidden flex-grow-1">
                                    <input type="email" name="email" value="{{ old('email', $workingLady->email) }}"
                                        class="form-control form-control-sm rounded-start-0 @error('email') is-invalid @enderror"
                                        placeholder="Enter email address" required>
                                </div>
                            </div>
                            @error('email')
                                <div class="text-danger fs-7 mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Fourth Row: Phone Number, Case Type -->
                    <div class="row mb-5">
                        <div class="col-md-6">
                            <label class="d-flex align-items-center fs-7 fw-semibold form-label mb-1">
                                <span class="required">Phone Number</span>
                            </label>
                            <div class="input-group input-group-sm flex-nowrap">
                                <span class="input-group-text py-1 px-2"><i class="bi bi-telephone-fill fs-5"></i></span>
                                <div class="overflow-hidden flex-grow-1">
                                    <input type="text" name="phone_number" value="{{ old('phone_number', $workingLady->phone_number) }}"
                                        class="form-control form-control-sm rounded-start-0 @error('phone_number') is-invalid @enderror"
                                        placeholder="Enter phone number" required>
                                </div>
                            </div>
                            @error('phone_number')
                                <div class="text-danger fs-7 mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="d-flex align-items-center fs-7 fw-semibold form-label mb-1">
                                <span class="required">Case Type</span>
                            </label>
                            <div class="input-group input-group-sm flex-nowrap">
                                <span class="input-group-text py-1 px-2"><i class="bi bi-flag-fill fs-5"></i></span>
                                <div class="overflow-hidden flex-grow-1">
                                    <select name="case_type"
                                        class="form-select form-select-sm rounded-start-0 @error('case_type') is-invalid @enderror"
                                        required>
                                        <option value="">Select case type</option>
                                        <option value="normal" {{ old('case_type', $workingLady->case_type) == 'normal' ? 'selected' : '' }}>Normal</option>
                                        <option value="critical" {{ old('case_type', $workingLady->case_type) == 'critical' ? 'selected' : '' }}>Critical</option>
                                    </select>
                                </div>
                            </div>
                            @error('case_type')
                                <div class="text-danger fs-7 mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Fifth Row: Status -->
                    <div class="row mb-5">
                        <div class="col-md-6">
                            <label class="d-flex align-items-center fs-7 fw-semibold form-label mb-1">
                                <span>Status</span>
                            </label>
                            <div class="input-group input-group-sm flex-nowrap">
                                <span class="input-group-text py-1 px-2"><i class="bi bi-check-circle-fill fs-5"></i></span>
                                <div class="overflow-hidden flex-grow-1">
                                    <select name="status"
                                        class="form-select form-select-sm rounded-start-0 @error('status') is-invalid @enderror">
                                        <option value="">Pending</option>
                                        <option value="Approved" {{ old('status', $workingLady->status) == 'Approved' ? 'selected' : '' }}>Approved</option>
                                        <option value="Disapproved" {{ old('status', $workingLady->status) == 'Disapproved' ? 'selected' : '' }}>Disapproved</option>
                                    </select>
                                </div>
                            </div>
                            @error('status')
                                <div class="text-danger fs-7 mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Sixth Row: Remarks (full width) -->
                    <div class="row mb-5">
                        <div class="col-md-12">
                            <label class="d-flex align-items-center fs-7 fw-semibold form-label mb-1">
                                <span>Remarks</span>
                            </label>
                            <div class="input-group input-group-sm flex-nowrap">
                                <span class="input-group-text py-1 px-2"><i class="bi bi-card-text fs-5"></i></span>
                                <div class="overflow-hidden flex-grow-1">
                                    <textarea name="remarks" rows="3"
                                        class="form-control form-control-sm rounded-start-0 @error('remarks') is-invalid @enderror"
                                        placeholder="Enter remarks (optional)">{{ old('remarks', $workingLady->remarks) }}</textarea>
                                </div>
                            </div>
                            @error('remarks')
                                <div class="text-danger fs-7 mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-sm btn-primary">
                                <i class="bi bi-check-circle"></i> Update Working Lady
                            </button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection
