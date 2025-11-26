<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::query()->create([
            'first_name' => 'Иван',
            'last_name' => 'Иванов',
            'email' => 'a@a.a',
            'phone' => '+375292909641',
            'password' => Hash::make('lm57iqxz'),
        ]);
    }
}
