<?php

use Illuminate\Support\Facades\Route;
use Sparktro\Ignite\Http\Controllers\SystemCheckController;

    Route::middleware([\Sparktro\Ignite\Middleware\CheckInstallation::class, 'web'])
        ->prefix('install')
        ->as('install.')
        ->group(function () {

            Route::match(['get'], '/', [SystemCheckController::class, 'step1'])->name('step1');

            Route::match(['get'], '/step2', [SystemCheckController::class, 'step2'])->name('step2');

            Route::match(['post'], '/environment', [SystemCheckController::class, 'environmentSet'])->name('environment.set');
            Route::post('database-setup', [SystemCheckController::class, 'setupDatabase'])->name('database.setup');

            Route::match(['get'], '/admin', [SystemCheckController::class, 'step3'])->name('admin.step3');

            Route::match(['post'], '/admin/store', [SystemCheckController::class, 'adminStore'])->name('admin.store');

            Route::match(['get', 'post'], '/finish', [SystemCheckController::class, 'finish'])->name('finish');
        });

