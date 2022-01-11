<?php

namespace App\Console\Commands;

use App\LoginModule\Keys;
use Illuminate\Console\Command;

class KeysCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lm:keys';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the encryption keys for login module tokens';

    /**
     * Execute the console command.
     *
     * @param  RSA  $rsa
     * @return mixed
     */
    public function handle()
    {
        Keys::generate();
        $this->info('Login module encryption keys generated successfully.');
        return 0;
    }
}