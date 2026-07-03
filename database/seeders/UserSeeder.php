<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $freePlan = SubscriptionPlan::where('name', 'Free')->first();
        $proPlan = SubscriptionPlan::where('name', 'Pro')->first();

        User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@docspe.com',
            'mobile' => '1234567890',
            'password' => Hash::make('password'),
            'subscription_plan_id' => $proPlan?->id,
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        User::create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'mobile' => '0987654321',
            'password' => Hash::make('password'),
            'subscription_plan_id' => $freePlan?->id,
            'email_verified_at' => now(),
            'is_active' => true,
        ]);
    }
}
