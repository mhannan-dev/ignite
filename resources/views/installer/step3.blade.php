@extends('installer::installer.layout')

@section('content')

    @php
        $dbSetupComplete = session('db_migration_complete') ?? false;
    @endphp

    <h2 class="d-flex align-items-center mb-4 h4 text-dark">
        <i class="material-icons-outlined me-2 text-primary" style="font-size: 1.25em;">person_add</i> Create Administrator Account
    </h2>

    <div class="mb-4 text-sm text-secondary">
        Create the initial administrator account for accessing the <strong>{{ config('app.name') }}</strong> system.
    </div>

    @if (!$dbSetupComplete)
        <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
            <i class="material-icons-outlined me-2" style="font-size: 1.5em;">warning</i>
            <div>
                The application database is not yet set up. Click the button below to run migrations and seeding.
            </div>
        </div>

        <form action="{{ route('install.database.setup') }}" method="post" class="text-center pt-3">
            @csrf
            <button type="submit" class="btn btn-success btn-lg d-flex align-items-center justify-content-center mx-auto px-5 py-3">
                <i class="material-icons-outlined me-2" style="font-size: 1.5em;">cloud_upload</i> Setup Database & Continue
            </button>
        </form>
    @else
        <div class="alert alert-success d-flex align-items-center mb-4 py-2" role="alert">
            <i class="material-icons-outlined me-2" style="font-size: 1.1em;">check_circle</i>
            <div>Database setup completed successfully. Please create the admin account now.</div>
        </div>

        <form action="{{ route('install.admin.store') }}" method="post" class="needs-validation" novalidate>
            @csrf

            <!-- Full Name -->
            <div class="mb-3">
                <label for="name" class="form-label small fw-medium text-secondary">Full Name</label>
                <input type="text" id="name" name="name" placeholder="Full Name"
                    class="form-control" required>
                <div class="form-text">Enter the administrator's full name</div>
                <div class="invalid-feedback">Full Name is required.</div>
            </div>

            <!-- Email -->
            <div class="mb-3">
                <label for="email" class="form-label small fw-medium text-secondary">Email Address</label>
                <input type="email" id="email" name="email" placeholder="Email"
                    class="form-control" required>
                <div class="form-text">This will be used for login and notifications</div>
                <div class="invalid-feedback">A valid Email Address is required.</div>
            </div>

            <!-- Passwords -->
            <div class="row g-3 mb-3">
                <div class="col-md-6 position-relative">
                    <label for="password" class="form-label small fw-medium text-secondary">Password</label>
                    <div class="input-group">
                        <input type="password" id="password" name="password" placeholder="Password"
                            class="form-control" required>
                        <span class="input-group-text bg-transparent border-start-0" style="cursor: pointer;"
                              onclick="togglePassword('password', this)">
                            <i class="material-icons-outlined">visibility_off</i>
                        </span>
                    </div>
                    <div class="invalid-feedback">Password is required.</div>
                </div>

                <div class="col-md-6 position-relative">
                    <label for="password_confirmation" class="form-label small fw-medium text-secondary">Confirm Password</label>
                    <div class="input-group">
                        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm Password"
                            class="form-control" required>
                        <span class="input-group-text bg-transparent border-start-0" style="cursor: pointer;"
                              onclick="togglePassword('password_confirmation', this)">
                            <i class="material-icons-outlined">visibility_off</i>
                        </span>
                    </div>
                    <div class="invalid-feedback">Confirmation password is required.</div>
                </div>
            </div>

            <!-- Security Recommendations -->
            <div class="alert alert-info py-3" role="alert">
                <h4 class="d-flex align-items-center mb-2 h6 text-info">
                    <i class="material-icons-outlined me-2" style="font-size: 1.2em;">security</i> Security Recommendations
                </h4>
                <ul class="list-unstyled mb-0 ms-2 text-sm">
                    <li class="d-flex align-items-center mb-1">
                        <i class="material-icons-outlined me-2" style="font-size: 1em;">done</i> Use a strong, unique password
                    </li>
                    <li class="d-flex align-items-center mb-1">
                        <i class="material-icons-outlined me-2" style="font-size: 1em;">done</i> Include uppercase, lowercase, numbers, and symbols
                    </li>
                    <li class="d-flex align-items-center">
                        <i class="material-icons-outlined me-2" style="font-size: 1em;">done</i> Avoid using personal information
                    </li>
                </ul>
            </div>

            <div class="d-flex justify-content-between align-items-center pt-3">
                <a href="{{ url()->previous() }}" class="btn btn-link text-decoration-none d-flex align-items-center">
                    <i class="material-icons-outlined me-2" style="font-size: 1.2em;">arrow_back</i> Back
                </a>

                <button type="submit" class="btn btn-primary d-flex align-items-center px-4 py-2">
                    Create Account <i class="material-icons-outlined ms-2" style="font-size: 1.2em;">person_add_alt_1</i>
                </button>
            </div>
        </form>
    @endif

    <script>
        // Password visibility toggle
        function togglePassword(fieldId, iconElement) {
            const input = document.getElementById(fieldId);
            const icon = iconElement.querySelector('.material-icons-outlined');
            input.type = input.type === 'password' ? 'text' : 'password';
            icon.textContent = input.type === 'password' ? 'visibility_off' : 'visibility';
        }

        // Bootstrap validation
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
