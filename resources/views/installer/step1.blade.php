@extends('installer::installer.layout')

@section('content')
    <div class="mb-5 pb-3 border-bottom text-center">
        <h2 class="h3 fw-bold text-dark mb-3 d-flex justify-content-center align-items-center">
            <i class="fa-solid fa-cloud-arrow-down me-2 text-primary" style="font-size: 1.25em;"></i>
            Welcome to {{ config('app.name') }} Installer
        </h2>
        <p class="text-secondary">This quick wizard will guide you through setting up your application.</p>

        <div class="alert alert-info mt-4 mx-auto" style="max-width: 90%;">
            <strong class="d-block mb-2">Before you start:</strong>
            <ul class="list-unstyled mb-0 ms-3">
                <li class="d-flex align-items-center mb-1">
                    <i class="fa-solid fa-key me-2 text-info" style="font-size: 1.1em;"></i> Database credentials ready
                </li>
                <li class="d-flex align-items-center mb-1">
                    <i class="fa-solid fa-server me-2 text-info" style="font-size: 1.1em;"></i> Server meets requirements
                </li>
                <li class="d-flex align-items-center">
                    <i class="fa-solid fa-folder-open me-2 text-info" style="font-size: 1.1em;"></i> Write permissions to storage folders
                </li>
            </ul>
        </div>
    </div>

    <hr class="my-4">

    <h3 class="d-flex align-items-center mb-4 h4 text-dark">
        <i class="fa-solid fa-desktop me-2 text-primary" style="font-size: 1.25em;"></i>
        System Requirements
    </h3>

    <p class="mb-3 text-sm text-secondary">
        Your server must meet the following requirements to run <strong>{{ config('app.name') }}</strong>.
    </p>

    <div class="mb-4 border-top border-bottom">
        @foreach ($requirements as $key => $status)
            <div class="row g-0 py-2 requirement-item border-bottom">
                <div class="col-8 fw-medium">{{ $key }}</div>
                <div class="col-4 text-end">
                    @if ($status)
                        <span class="text-success d-flex align-items-center justify-content-end">
                            <i class="fa-solid fa-circle-check me-1" style="font-size: 1.1em;"></i> OK
                        </span>
                    @else
                        <span class="text-danger d-flex align-items-center justify-content-end">
                            <i class="fa-solid fa-circle-xmark me-1" style="font-size: 1.1em;"></i> Missing
                        </span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    @if (in_array(false, $requirements))
        <div class="alert alert-warning d-flex align-items-center py-2">
            <i class="fa-solid fa-triangle-exclamation me-2" style="font-size: 1.1em;"></i>
            <div>
                One or more requirements are not met. Please resolve these issues before proceeding.
            </div>
        </div>
        <div class="text-center mt-4">
            <button class="btn btn-secondary btn-lg" disabled>
                Resolve Requirements to Start
            </button>
        </div>
    @else
        <div class="alert alert-success d-flex align-items-center py-2">
            <i class="fa-solid fa-circle-check me-2" style="font-size: 1.1em;"></i>
            <div>All system requirements are met.</div>
        </div>
        <div class="text-center mt-4">
            <a href="{{ route('install.database') }}" class="btn btn-primary btn-lg d-flex align-items-center justify-content-center mx-auto" style="max-width: 300px;">
                Start Installation
                <i class="fa-solid fa-arrow-right ms-2" style="font-size: 1.2em;"></i>
            </a>
        </div>
    @endif
@endsection
