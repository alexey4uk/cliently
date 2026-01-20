<?php

namespace Database\Seeders;

use App\Models\Client;
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
        // Используем firstOrCreate для безопасного создания (не создаст дубликат)
        $user = User::firstOrCreate(
            ['email' => 'a@a.ru'],
            [
                'name' => 'Иван',
                'phone' => '+375292909641',
                'password' => Hash::make('lm57iqxz'),
            ]
        );

        // Назначаем роль админа
        $user->assignRole('admin');

        // $user->businesses()->create([
        //     'name' => 'ИП Иванов',
        //     'slug' => fake()->slug(),
        //     'short_description' => 'Парикмахер'
        // ]);

        // Client::factory(30)->create();
    }
}
