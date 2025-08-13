<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UpdateAdminPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:update-password';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update admin password to password123';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $admin = User::where('email', 'admin@proman.com')->first();
        
        if (!$admin) {
            $this->error('Admin user not found!');
            return 1;
        }
        
        $admin->password = Hash::make('password123');
        $admin->save();
        
        $this->info('Admin password updated successfully!');
        $this->info('Email: admin@proman.com');
        $this->info('Password: password123');
        
        return 0;
    }
}
