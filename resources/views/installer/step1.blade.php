@extends('installer::installer.layout')

@section('content')
    <div class="pb-3 mb-5 text-center border-bottom">
        <h2 class="mb-3 h3 fw-bold text-dark d-flex justify-content-center align-items-center">
            <i class="fa-solid fa-cloud-arrow-down me-2 text-primary" style="font-size: 1.25em;"></i>
            Welcome to {{ config('app.name') }} Installer
        </h2>
        <p class="text-secondary">This quick wizard will guide you through setting up your application.</p>

        <div class="mx-auto mt-4 alert alert-info" style="max-width: 90%;">
            <strong class="mb-2 d-block">Before you start:</strong>
            <ul class="mb-0 list-unstyled ms-3">
                <li class="mb-1 d-flex align-items-center">
                    <i class="fa-solid fa-key me-2 text-info" style="font-size: 1.1em;"></i> Database credentials ready
                </li>
                <li class="mb-1 d-flex align-items-center">
                    <i class="fa-solid fa-server me-2 text-info" style="font-size: 1.1em;"></i> Server meets requirements
                </li>
                <li class="d-flex align-items-center">
                    <i class="fa-solid fa-folder-open me-2 text-info" style="font-size: 1.1em;"></i> Write permissions to storage folders
                </li>
            </ul>
        </div>
    </div>

    <hr class="my-4">

    <h3 class="mb-4 d-flex align-items-center h4 text-dark">
        <i class="fa-solid fa-desktop me-2 text-primary" style="font-size: 1.25em;"></i>
        System Requirements
    </h3>

    <p class="mb-3 text-sm text-secondary">
        Your server must meet the following requirements to run <strong>{{ config('app.name') }}</strong>.
    </p>

    <div class="mb-4 border-top border-bottom">
        @foreach ($requirements as $key => $status)
            <div class="py-2 row g-0 requirement-item border-bottom">
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
        <div class="py-2 alert alert-warning d-flex align-items-center">
            <i class="fa-solid fa-triangle-exclamation me-2" style="font-size: 1.1em;"></i>
            <div>
                One or more requirements are not met. Please resolve these issues before proceeding.
            </div>
        </div>
        <div class="mt-4 text-center">
            <button class="btn btn-secondary btn-lg" disabled>
                Resolve Requirements to Start
            </button>
        </div>
    @else
        <div class="py-2 alert alert-success d-flex align-items-center">
            <i class="fa-solid fa-circle-check me-2" style="font-size: 1.1em;"></i>
            <div>All system requirements are met.</div>
        </div>
        <div class="mt-4 text-center">
            <a href="{{ route('install.step2') }}" class="mx-auto btn btn-primary btn-lg d-flex align-items-center justify-content-center" style="max-width: 300px;">
                Start Installation
                <i class="fa-solid fa-arrow-right ms-2" style="font-size: 1.2em;"></i>
            </a>
        </div>
    @endif
@endsection