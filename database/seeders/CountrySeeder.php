<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Заполнение таблицы стран.
     */
    public function run(): void
    {
        Country::firstOrCreate(
            ['code' => 'BY'],
            [
                'code_3' => 'BLR',
                'name' => 'Беларусь',
                'name_en' => 'Belarus',
                'calling_code' => '+375',
                'currency' => 'BYN',
                'currency_symbol' => 'Br',
                'ioc' => 'BLR',
            ]
        );
    }
}
