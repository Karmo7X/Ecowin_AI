<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateFilamentUser extends Command
{
    protected $signature = 'make:filament-user-custom';
    protected $description = 'Create a new Filament user with a phone number';

    public function handle()
    {
        $name = $this->ask('Name');
        $email = $this->ask('Email address');
        $password = $this->secret('Password');
        $phone = $this->ask('Phone number'); // طلب إدخال الهاتف

        // إنشاء المستخدم مع تعيين الدور مباشرة باستخدام enum
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'phone' => $phone, // إدخال الهاتف في قاعدة البيانات
            'role' => 'admin', // تحديد الدور مباشرة في عمود role
        ]);

        $this->info('Filament user created successfully with the "admin" role!');
    }
}
