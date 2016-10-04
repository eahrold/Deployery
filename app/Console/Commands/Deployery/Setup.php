<?php

namespace App\Console\Commands\Deployery;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Inspiring;

class Setup extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deployery:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup deployery.';

    /**
     * Filesystem object
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    private $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->makeEnvFile();

        // Key Setup keys
        $this->call('jwt:secret', ['--force' => true]);
        $this->setEnvValueForKey($this->generateRandomKey(), 'APP_KEY');

        // Database setup
        $this->info('Setting up database...');
        $dbName = $this->ask('Enter a database name', env('DB_DATABASE', 'deployery'));
        $this->call('deployery:database', ['database' => $dbName]);

        // User Setup
        $this->createSuperUser();

        // Pusher Setup
        $this->setupPusher();

        // Other Setup
        $this->runSystemCommands();

        $this->info("You're all ready to go.");
        $this->info("Next update your Apache/Nginx and configure supervisord");
        $this->line(static::$line);
    }

    /**
     * Run commands that require system()
     *
     * @return void
     */
    private function runSystemCommands()
    {
        if (function_exists('system')) {
            $this->runNPM();
            $this->gulp();
        }
        $this->line(static::$line);
    }

    /**
     * Setup pushser keys in env file
     *
     * @return void
     */
    private function setupPusher()
    {
        $setupNow = $this->ask('Do you want to setup the pusher account now? [Y/N]', "Y");

        if (!starts_with(strtolower($setupNow), 'y')) {
            $this->info('You can update this later in the .env file, but it IS required');
            $this->line(static::$line);
            return;
        }

        $pusher_key = $this->ask('Enter your Pusher Key', env('PUSHER_KEY'));
        $pusher_secret = $this->ask('Enter your Pusher Secret', env('PUSHER_SECRET'));
        $pusher_app_id = $this->ask('Enter your Pusher App ID', env('PUSHER_APP_ID'));

        if ($pusher_key and $pusher_secret and $pusher_app_id) {
            $this->info('Updating Pusher Keys');

            $this->setEnvValueForKey($pusher_key, 'PUSHER_KEY');
            $this->setEnvValueForKey($pusher_secret, 'PUSHER_SECRET');
            $this->setEnvValueForKey($pusher_app_id, 'PUSHER_APP_ID');
        }
        $this->line(static::$line);
    }

    /**
     * Run NPM
     *
     *
     * @return void
     */
    private function runNPM()
    {
        $this->info('Installing node packages');
        system('npm install');
    }

    /**
     * Run Gulp
     *
     * @return void
     */
    private function gulp()
    {
        $this->info('Running gulp');
        system('gulp --production');
    }

    /**
     * Set the application key in the environment file.
     *
     * @param  string  $key
     * @param string $value
     * @return void
     */
    protected function setEnvValueForKey($value, $key)
    {
        file_put_contents(
            $this->laravel->environmentFilePath(),
            preg_replace(
                "/.*\b".$key."\b.*\n/ui",
                "{$key}={$value}\n",
                file_get_contents($this->laravel->environmentFilePath())
            )
        );
    }

    /**
     * Create a super user.
     *
     * Heavily borrowed from TypiCMS
     * https://github.com/TypiCMS/Core/blob/master/src/Commands/Install.php
     *
     * @return void
     */
    private function createSuperUser()
    {
        $this->info('Creating a Super User...');

        $username = $this->ask('Enter your username');
        $email = $this->ask('Enter your email address');
        $firstname = $this->ask('Enter your first name');
        $lastname = $this->ask('Enter your last name');
        $password = $this->secret('Enter a password');

        try {
            \User::create([
                'username'   => $username,
                'email'      => $email,
                'password'   => bcrypt($password),
                'first_name' => $firstname,
                'last_name'  => $lastname,
            ]);

            $this->info('Superuser created.');
        } catch (\Exception $e) {
            $this->error('User could not be created. A duplicate entery may already exists');
        }
    }

    /**
     * Generate a random key for the application.
     *
     * @return string
     */
    protected function generateRandomKey()
    {
        return 'base64:'.base64_encode(random_bytes(
            $this->laravel['config']['app.cipher'] == 'AES-128-CBC' ? 16 : 32
        ));
    }

    /**
     * Create an .env file from the .env.example file
     *
     * @return void
     */
    private function makeEnvFile()
    {
        $dest = $this->laravel->environmentFilePath();
        $source = "{$dest}.example";
        $this->info("Copying {$source} to {$dest}.");

        if (!file_exists($dest)) {
            copy($source, $dest);
        } else {
            $this->info('File already exists, skipping...');
        }
    }

    protected static $line =
    "------------------------------------------------------------------------------------------";
}
