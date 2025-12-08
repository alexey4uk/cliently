<?php

namespace Database\Seeders;

use App\Models\Client;
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
            'email' => 'a@a.ru',
            'phone' => '+375292909641',
            'password' => Hash::make('lm57iqxz'),
        ]);

        $user->businesses()->create([
            'name' => 'ИП Иванов',
            'slug' => fake()->slug(),
            'short_description' => 'Парикмахер'
        ]);

        Client::factory(30)->create();
    }
}
