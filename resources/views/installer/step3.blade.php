@extends('installer::installer.layout')

@section('content')

    {{-- Assume $dbSetupComplete is passed as boolean from controller --}}
    @php
        $dbSetupComplete = session('db_migration_complete') ?? false; // Check session or actual status
    @endphp

    <h2 class="d-flex align-items-center mb-4 h4 text-dark">
        <span class="material-icons me-2 text-primary" style="font-size: 1.25em;">person_add</span> Create Administrator Account
    </h2>

    <div class="mb-4 text-sm text-secondary">
        Create the initial administrator account for accessing the <strong>{{ config('app.name') }}</strong> system.
    </div>

    {{-- ---------------------------------------------------------------- --}}
    {{-- A. DATABASE MIGRATION BUTTON (If not completed) --}}
    {{-- ---------------------------------------------------------------- --}}
    @if (!$dbSetupComplete)
        <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
            <span class="material-icons me-2" style="font-size: 1.5em;">warning</span>
            <div>
                The application database is not yet set up. Click the button below to run migrations and seeding.
            </div>
        </div>

        <form action="{{ route('install.database.setup') }}" method="post" class="text-center pt-3">
            @csrf
            <button type="submit" class="btn btn-success btn-lg d-flex align-items-center justify-content-center mx-auto px-5 py-3">
                <span class="material-icons me-2" style="font-size: 1.5em;">cloud_upload</span> Setup Database & Continue
            </button>
        </form>
    @else

    {{-- ---------------------------------------------------------------- --}}
    {{-- B. ADMIN CREATION FORM (If migration is successful) --}}
    {{-- ---------------------------------------------------------------- --}}

        <div class="alert alert-success d-flex align-items-center mb-4 py-2" role="alert">
            <span class="material-icons me-2" style="font-size: 1.1em;">check_circle</span>
            <div>Database setup completed successfully. Please create the admin account now.</div>
        </div>

        <form action="{{ route('install.admin.store') }}" method="post" class="needs-validation" novalidate>
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label small fw-medium text-secondary">Full Name</label>
                <input type="text" id="name" name="name" placeholder="Full Name"
                    class="form-control" required>
                <div class="form-text">Enter the administrator's full name</div>
                <div class="invalid-feedback">Full Name is required.</div>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label small fw-medium text-secondary">Email Address</label>
                <input type="email" id="email" name="email" placeholder="Email"
                    class="form-control" required>
                <div class="form-text">This will be used for login and notifications</div>
                <div class="invalid-feedback">A valid Email Address is required.</div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="password" class="form-label small fw-medium text-secondary">Password</label>
                    <input type="password" id="password" name="password" placeholder="Password"
                        class="form-control" required>
                    <div class="invalid-feedback">Password is required.</div>
                </div>

                <div class="col-md-6">
                    <label for="password_confirmation" class="form-label small fw-medium text-secondary">Confirm Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm Password"
                        class="form-control" required>
                    <div class="invalid-feedback">Confirmation password is required.</div>
                </div>
            </div>

            <div class="alert alert-info py-3" role="alert">
                <h4 class="d-flex align-items-center mb-2 h6 text-info">
                    <span class="material-icons me-2" style="font-size: 1.2em;">security</span> Security Recommendations
                </h4>
                <ul class="list-unstyled mb-0 ms-2 text-sm">
                    <li class="d-flex align-items-center mb-1">
                        <span class="material-icons me-2" style="font-size: 1em;">done</span> Use a strong, unique password
                    </li>
                    <li class="d-flex align-items-center mb-1">
                        <span class="material-icons me-2" style="font-size: 1em;">done</span> Include uppercase, lowercase, numbers, and symbols
                    </li>
                    <li class="d-flex align-items-center">
                        <span class="material-icons me-2" style="font-size: 1em;">done</span> Avoid using personal information
                    </li>
                </ul>
            </div>
            <div class="d-flex justify-content-between align-items-center pt-3">
                <a href="{{ url()->previous() }}" class="btn btn-link text-decoration-none d-flex align-items-center">
                    <span class="material-icons me-2" style="font-size: 1.2em;">arrow_back</span> Back
                </a>

                <button type="submit" class="btn btn-primary d-flex align-items-center px-4 py-2">
                    Create Account <span class="material-icons ms-2" style="font-size: 1.2em;">person_add_alt_1</span>
                </button>
            </div>
        </form>
    @endif

    <script>
        (function () {
            'use strict'
            const form = document.querySelector('.needs-validation');
            if (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            }
        })()
    </script>
@endsection
