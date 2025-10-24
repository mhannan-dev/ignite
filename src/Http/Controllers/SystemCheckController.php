<?php

namespace Sparktro\Ignite\Http\Controllers;



use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class SystemCheckController extends Controller
{
    public function step1()
    {
        $this->ensureStorageExists();
        $requirements = [
            'PHP >= 8.2' => version_compare(PHP_VERSION, '8.2.0', '>='),
            'PDO' => extension_loaded('pdo'),
            'Mbstring' => extension_loaded('mbstring'),
            'OpenSSL' => extension_loaded('openssl'),
            'Ctype' => extension_loaded('ctype'),
            'JSON' => extension_loaded('json'),
            'BCMath' => extension_loaded('bcmath'),
            'XML' => extension_loaded('xml'),
            'Tokenizer' => extension_loaded('tokenizer'),
            'Writable storage/' => is_writable(storage_path()),
            'Writable storage/app/' => is_writable(storage_path('app')), // updated
            'Writable storage/framework/' => is_writable(storage_path('framework')),
            'Writable storage/logs/' => is_writable(storage_path('logs')),
            'Writable bootstrap/cache/' => is_writable(base_path('bootstrap/cache')),
            'Writable .env' => ! File::exists(base_path('.env')) || is_writable(base_path('.env')),
        ];

        $allRequirementsMet = ! in_array(false, $requirements, true);

        return view('installer::installer.step1', compact('requirements', 'allRequirementsMet'));
    }

    public function step2()
    {

        return view('installer::installer.step2');
    }

    // In your InstallerController or wherever this method resides

    public function environmentSet(Request $request)
    {
        ini_set('memory_limit', '-1');        // unlimited memory
        ini_set('max_execution_time', 600);   // 10 minutes
        $data = $request->validate(
            [
                // Application Identity
                'application_url' => 'required|string|max:255', // * Required in form
                'app_name' => 'required|string|max:255',        // * Required in form

                // License Details
                'domain_name' => 'required|string|max:255',      // * Required in form
                'codecanyon_username' => 'required|string|max:255', // * Required in form
                'codecanyon_license_key' => 'required|string|max:255', // * Required in form

                // Database Connection
                'db_host' => 'required|string',                  // * Required in form
                'db_port' => 'required|numeric',                 // * Required in form
                'db_user' => 'required|string',                  // * Required in form (using db_user)
                'db_name' => 'required|string',                  // * Required in form (using db_name)
                'db_pass' => 'required|string',                  // Matches form: optional, with helper text
            ],
            [
                // 2. Custom Error Messages (Optional, but helpful for clarity)
                'application_url.required' => 'The Application URL is essential and cannot be empty.',
                'app_name.required' => 'The Application Name field is required.',
                'domain_name.required' => 'The Domain Name is required for license validation.',
                'codecanyon_username.required' => 'Your Envato/CodeCanyon Username is required.',
                'codecanyon_license_key.required' => 'The CodeCanyon License Key (Purchase Code) is required.',
                'db_host.required' => 'The Database Host (e.g., 127.0.0.1) is required.',
                'db_port.required' => 'The Database Port (e.g., 3306) is required.',
                'db_user.required' => 'The Database User is required.',
                'db_name.required' => 'The Database Name is required.',
            ]
        );

        $envData = [
            // Database
            'DB_CONNECTION' => 'mysql',
            'DB_HOST' => $data['db_host'],
            'DB_PORT' => $data['db_port'],
            'DB_DATABASE' => $data['db_name'],      // Mapped from db_name
            'DB_USERNAME' => $data['db_user'],      // Mapped from db_user
            'DB_PASSWORD' => $data['db_pass'] ?? '', // Mapped from db_pass
            'APP_DB' => 'true',

            // Application/License
            'APP_NAME' => $data['app_name'],
            'APP_URL' => $data['application_url'],
            'CODECANYON_USERNAME' => $data['codecanyon_username'],
            'CODECANYON_LICENSE' => $data['codecanyon_license_key'],
        ];

        try {
            // Your existing environment logic
            $this->ensureEnv();
            $this->setEnv($envData);

            Artisan::call('config:clear');
            Artisan::call('cache:clear');
            Artisan::call('route:clear');

            // Test DB connection
            config([
                'database.default' => 'mysql',
                'database.connections.mysql.host' => $envData['DB_HOST'],
                'database.connections.mysql.port' => $envData['DB_PORT'],
                'database.connections.mysql.database' => $envData['DB_DATABASE'],
                'database.connections.mysql.username' => $envData['DB_USERNAME'],
                'database.connections.mysql.password' => $envData['DB_PASSWORD'],
            ]);

            Log::info('Database connection test successful', ['host' => $envData['DB_HOST'], 'database' => $envData['DB_DATABASE']]);
            $request->session()->put('installer_data', $envData);
            return redirect()->route('install.admin.step3')->with('success', 'Environment settings saved and database connected successfully.');
        } catch (Exception $e) {
            Log::error('Environment setup failed: ' . $e->getMessage());
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function step3()
    {
        return view('installer::installer.step3');
    }


    public function setupDatabase(Request $request)
    {
        ini_set('memory_limit', '-1');        // unlimited memory
        ini_set('max_execution_time', 600);   // 10 minutes

        DB::connection()->getPdo();
        try {

            $exitCode = Artisan::call('migrate:fresh', [
                '--force' => true,
                '--seed' => true,
            ]);

            if ($exitCode !== 0) {
                $output = Artisan::output();
                throw new Exception('Migration Seeding failed. Output: ' . $output);
            }

            $this->setEnv(['APP_DB_SYNC' => 'true']);
            Artisan::call('config:clear');

            $request->session()->put('db_migration_complete', true);

            return redirect()->route('install.admin.step3')->with('success', 'Database setup completed successfully. Please create the administrator account.');
        } catch (Exception $e) {
            Log::error('DB migration setup failed: ' . $e->getMessage());
            return back()->with('error', $e->getMessage())->withInput();
        }
    }


    public function adminStore(Request $request)
    {
        ini_set('memory_limit', '-1');        // unlimited memory
        ini_set('max_execution_time', 600);   // 10 minutes
        // The middleware should prevent this, but the session check is a good backup
        if (! $request->session()->get('db_migration_complete')) {
            Log::warning('Attempt to create admin user before DB migration');
            return redirect()->back()->with('error', 'Please complete the database setup before creating an admin user.');
        }

        $data = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|unique:users,email', // Added unique check
            'password' => 'required|min:6|confirmed',
        ]);

        try {
            $dbDetails = [
                'DB_HOST' => env('DB_HOST'),
                'DB_PORT' => env('DB_PORT'),
                'DB_DATABASE' => env('DB_DATABASE'),
                'DB_USERNAME' => env('DB_USERNAME'),
                'DB_PASSWORD' => env('DB_PASSWORD'),
            ];

            config([
                'database.default' => 'mysql',
                'database.connections.mysql.host' => $dbDetails['DB_HOST'],
                'database.connections.mysql.port' => $dbDetails['DB_PORT'],
                'database.connections.mysql.database' => $dbDetails['DB_DATABASE'],
                'database.connections.mysql.username' => $dbDetails['DB_USERNAME'],
                'database.connections.mysql.password' => $dbDetails['DB_PASSWORD'],
            ]);

            // DB::connection()->getPdo();


            $currentTime = now();
            $user = DB::table('users')->insert([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => 1, // Assuming role_id 1 is Admin
                'can_login' => 1,
                'status' => 1,
                'created_at' => $currentTime,
                'updated_at' => $currentTime,
            ]);

            // Clear session flag
            $request->session()->forget('db_migration_complete');

            // Final ENV updates (move session/cache drivers to database)
            $this->setEnv([
                'SESSION_DRIVER' => 'database',
                'CACHE_STORE' => 'database',
                'APP_DB_SYNC' => 'true', // Re-confirm this is true
            ]);

            Artisan::call('config:clear');
            Artisan::call('cache:clear');

            Log::info('Migration and seeding completed and Admin user created successfully.');
            return redirect()->route('install.finish')->with('success', 'Admin user created successfully.');
        } catch (\PDOException $e) {
            return back()->withErrors(['db' => 'Database operation failed during admin creation: ' . $e->getMessage()])->withInput();
        } catch (Exception $e) {
            // Catch general errors
            return back()->withErrors(['admin' => 'Failed to create admin user: ' . $e->getMessage()])->withInput();
        }
    }



    public function finish()
    {
        $this->setEnv(['APP_INSTALLED' => 'true']);

        // Clear cached config & route
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');

        // Runtime config update
        config(['app.installed' => true]);
        putenv('APP_INSTALLED=true');

        $appUrl = url('/');
        Log::info("Installation finished. App URL: {$appUrl}");

        return redirect('/')->with('success', 'Installation completed');
        // return view('installer::installer.finish', compact('appUrl'));
    }

    protected function ensureStorageExists()
    {
        ini_set('memory_limit', '-1');        // unlimited memory
        ini_set('max_execution_time', 600);   // 10 minutes
        $paths = [
            storage_path('app/public/uploads'),
            storage_path('app/private'),
            storage_path('framework/cache'),
            storage_path('framework/sessions'),
            storage_path('framework/views'),
            storage_path('framework/testing'),
            storage_path('logs'),
            storage_path('pail'),
            base_path('bootstrap/cache'),
            public_path('storage'),
            public_path('storage/uploads'),
        ];

        foreach ($paths as $path) {
            if (! File::exists($path)) {
                File::makeDirectory($path, 0775, true);
            }
            $gitignore = $path . '/.gitignore';
            if (! File::exists($gitignore)) {
                File::put($gitignore, "*\n!.gitignore\n");
            }
            @chmod($path, 0775);
        }

        $logFile = storage_path('logs/laravel.log');
        if (! File::exists($logFile)) {
            File::put($logFile, '');
        }
        chmod($logFile, 0666);
    }

    private function setEnv(array $values)
    {
        ini_set('memory_limit', '-1');        // unlimited memory
        ini_set('max_execution_time', 600);   // 10 minutes

        $this->ensureEnv();
        $path = base_path('.env');
        $content = File::get($path);

        foreach ($values as $key => $value) {
            $cleanValue = trim($value, '"\'');
            $needsQuotes = preg_match('/[\s#\']/', $cleanValue);
            $formattedValue = $needsQuotes ? "\"{$cleanValue}\"" : $cleanValue;
            $pattern = "/^{$key}=.*$/m";
            $replacement = "{$key}={$formattedValue}";
            $content = preg_match($pattern, $content)
                ? preg_replace($pattern, $replacement, $content, 1)
                : $content . PHP_EOL . $replacement;
            Log::debug("Updated .env: {$key}={$formattedValue}");
        }

        File::put($path, $content);
        Log::info('.env updated successfully');
    }


    public function ensureEnv()
    {
        $envPath = base_path('.env');
        $envExamplePath = base_path('.env.example');

        if (! File::exists($envPath)) {
            if (File::exists($envExamplePath)) {
                File::copy($envExamplePath, $envPath);
                Log::info('.env created from .env.example');
            } else {
                Log::error('.env.example not found');
                throw new Exception('.env.example not found, please create one manually.');
            }
        } else {
            Log::info('.env already exists');
        }

        @chmod($envPath, 0664);
    }
}
