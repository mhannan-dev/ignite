@extends('installer::installer.layout')
@section('content')
    <div class="py-4 text-center">
        <div class="mb-5">

            <div class="d-flex justify-content-center align-items-center mx-auto mb-4 rounded-circle"
                 style="width: 80px; height: 80px; background-color: #d1e7dd;">
                <i class="material-icons-outlined text-success" style="font-size: 3rem;">check_circle</i>
            </div>

            <h2 class="mb-2 h3 fw-bold text-dark">Installation Completed Successfully!</h2>
            <p class="mb-4 text-secondary">{{ config('app.name') }} is now ready to use. You can now access your application.</p>

            <div class="p-4 mx-auto mb-5 border border-success-subtle rounded" style="max-width: 450px; background-color: #f0fdf7;">
                <h4 class="d-flex justify-content-center align-items-center h6 fw-medium text-success">
                    <i class="material-icons-outlined me-2" style="font-size: 1.2em;">rocket_launch</i> What's Next?
                </h4>
                <ul class="list-unstyled mt-2 text-sm text-start text-success-dark">
                    <li class="d-flex align-items-start mb-2">
                        <i class="material-icons-outlined text-success me-2" style="font-size: 1.2em;">check_circle</i>
                        <span>Log in with your administrator account</span>
                    </li>
                    <li class="d-flex align-items-start mb-2">
                        <i class="material-icons-outlined text-success me-2" style="font-size: 1.2em;">check_circle</i>
                        <span>Configure your organization settings</span>
                    </li>
                    <li class="d-flex align-items-start">
                        <i class="material-icons-outlined text-success me-2" style="font-size: 1.2em;">check_circle</i>
                        <span>Add team members and set up workflows</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="mt-4">
            <a href="{{ $appUrl ?? url('/') }}"
                class="btn btn-primary btn-lg d-inline-flex align-items-center px-5 py-3 shadow-lg">
                Go to Application <i class="material-icons-outlined ms-2" style="font-size: 1.4em;">open_in_new</i>
            </a>
        </div>

        <div class="mt-4 text-sm text-secondary">
            <p>Thank you for choosing <strong>{{ config('app.name') }}</strong>!</p>
        </div>
    </div>
@endsection
