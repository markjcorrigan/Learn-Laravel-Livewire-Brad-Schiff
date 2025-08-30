<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandAlias;

class CreateSuperAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a user or super admin (optional)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // ask if to proceed with creating a super admin
        if (!$this->confirm('Do you want to create a user? You can confirm this user will be a Super User at the end of running of this script', true)) {
            $this->info('User creation skipped.');
            return CommandAlias::SUCCESS;
        }

        $this->line('Create the first user for your new application');

        // ask for the user's name
        $username = $this->ask('What is the user\'s name?');

        // ask for the user's email
        $email = $this->ask('What is the user\'s email?');

        // ask for the user's password and verify it
        $password = null;
        while (true) {
            $password = $this->secret('What is the user\'s password?');
            $confirmPassword = $this->secret('Confirm the user\'s password?');

            if ($password === $confirmPassword) {
                break;
            }

            $this->error('Passwords do not match. Please try again.');
        }

        // ask if the user is a super admin
        $isSuperAdmin = $this->confirm('Is this user a super admin?');

        // create the user
        $user = User::query()->create([
            'username' => $username,
            'email' => $email,
            'password' => bcrypt($password),
            'locale' => 'en',
        ]);

        // assign the role
        if ($isSuperAdmin) {
            $user->assignRole('Super Admin');
        }

        // output the user
        $this->info('User or Super Admin created successfully. Now go and build something amazing!');
        return CommandAlias::SUCCESS;
    }
}
