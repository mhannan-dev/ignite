<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} - Installation Wizard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }

        .installer-card {
            border-radius: 12px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1),
                        0 4px 6px -2px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            background-color: white;
        }

        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
        }

        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            width: 120px;
        }

        .step:not(:last-child):after {
            content: '';
            position: absolute;
            top: 20px;
            left: 60px;
            width: 60px;
            height: 2px;
            background-color: #d1d5db;
        }

        .step.active:not(:last-child):after {
            background-color: #0d6efd;
        }

        .step-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin-bottom: 8px;
            background-color: #e5e7eb;
            color: #6b7280;
        }

        .step.active .step-circle {
            background-color: #0d6efd;
            color: white;
        }

        .step.completed .step-circle {
            background-color: #198754;
            color: white;
        }

        .step-label {
            font-size: 0.875rem;
            color: #6b7280;
            text-align: center;
        }

        .step.active .step-label {
            color: #0d6efd;
            font-weight: 500;
        }

        .input-group .btn i {
            cursor: pointer;
        }
    </style>
</head>
<body class="d-flex justify-content-center align-items-center p-4">

<div class="container-md" style="max-width: 768px;">
    <div class="mb-5 text-center">
        <div class="d-flex justify-content-center align-items-center mb-3">
            <span class="material-icons-outlined me-2 fs-3 text-primary">handshake</span>
            <h1 class="h3 fw-bold text-dark mb-0">{{ config('app.name') }}</h1>
        </div>
        <p class="text-secondary">Installation Wizard</p>

        <!-- Step Indicator -->
        <div class="step-indicator">
            <!-- Step 1 -->
            <div class="step @if(Request::is('install/requirements')) active @elseif(Request::is('install/database') || Request::is('install/admin') || Request::is('install/complete')) completed @endif">
                <div class="step-circle">
                    @if(Request::is('install/requirements')) 1 @else <i class="material-icons-outlined">check</i> @endif
                </div>
                <span class="step-label">Requirements</span>
            </div>

            <!-- Step 2 -->
            <div class="step @if(Request::is('install/database')) active @elseif(Request::is('install/admin') || Request::is('install/complete')) completed @endif">
                <div class="step-circle">
                    @if(Request::is('install/database')) 2
                    @elseif(Request::is('install/admin') || Request::is('install/complete')) <i class="material-icons-outlined">check</i>
                    @else 2 @endif
                </div>
                <span class="step-label">Database</span>
            </div>

            <!-- Step 3 -->
            <div class="step @if(Request::is('install/admin')) active @elseif(Request::is('install/complete')) completed @endif">
                <div class="step-circle">
                    @if(Request::is('install/admin')) 3
                    @elseif(Request::is('install/complete')) <i class="material-icons-outlined">check</i>
                    @else 3 @endif
                </div>
                <span class="step-label">Admin Setup</span>
            </div>

            <!-- Step 4 -->
            <div class="step @if(Request::is('install/complete')) active @endif">
                <div class="step-circle">
                    @if(Request::is('install/complete')) 4 @else 4 @endif
                </div>
                <span class="step-label">Complete</span>
            </div>
        </div>
    </div>

    <!-- Card Content -->
    <div class="p-4 p-md-5 installer-card">
        @yield('content')
    </div>

    <div class="mt-4 text-center text-secondary small">
        {{ config('app.name') }} Installer &copy; {{ date('Y') }}
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<script>
    // Form validation
    document.addEventListener('DOMContentLoaded', function() {
        const forms = document.querySelectorAll('form');

        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                const inputs = this.querySelectorAll('input[required]');
                let valid = true;

                inputs.forEach(input => {
                    input.classList.remove('is-invalid');
                    const existingFeedback = input.parentNode.querySelector('.invalid-feedback');
                    if (existingFeedback) existingFeedback.remove();

                    if (!input.value.trim()) {
                        valid = false;
                        input.classList.add('is-invalid');
                        const errorMsg = document.createElement('div');
                        errorMsg.className = 'invalid-feedback';
                        errorMsg.textContent = 'This field is required';
                        input.parentNode.appendChild(errorMsg);
                    }
                });

                if (!valid) {
                    e.preventDefault();
                    e.stopPropagation();
                }
            }, false);
        });
    });

    // Password toggle
    document.querySelectorAll('[data-toggle-password]').forEach(button => {
        button.addEventListener('click', function () {
            const targetInput = document.querySelector(this.dataset.target);
            const icon = this.querySelector('i');
            const isVisible = targetInput.type === 'text';
            targetInput.type = isVisible ? 'password' : 'text';
            icon.textContent = isVisible ? 'visibility_off' : 'visibility';
        });
    });
</script>

</body>
</html>
