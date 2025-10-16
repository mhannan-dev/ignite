<?php

use Illuminate\Support\Facades\Route;
use Sparktro\Installer\Http\Controllers\SystemCheckController;

    Route::middleware([\Sparktro\Ignite\Middleware\CheckInstallation::class, 'web'])
        ->prefix('install')
        ->as('install.')
        ->group(function () {

            Route::match(['get'], '/', [SystemCheckController::class, 'welcome'])->name('welcome');

            Route::match(['get'], '/requirements', [SystemCheckController::class, 'dbForm'])->name('requirements');

            Route::match(['post'], '/environment', [SystemCheckController::class, 'environmentSet'])->name('environment.set');
            Route::post('database-setup', [SystemCheckController::class, 'setupDatabase'])->name('database.setup');

            Route::match(['get'], '/admin', [SystemCheckController::class, 'adminForm'])->name('admin.form');

            Route::match(['post'], '/admin/store', [SystemCheckController::class, 'adminStore'])->name('admin.store');

            Route::match(['get', 'post'], '/finish', [SystemCheckController::class, 'finish'])->name('finish');
        });

