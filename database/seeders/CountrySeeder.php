<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Заполнение таблицы стран (справочник).
     * BY — в селекте телефона (is_for_phone_select = true).
     * Остальные — только для определения по префиксу номера (is_active и is_for_phone_select = false).
     */
    public function run(): void
    {
        $countries = [
            ['code' => 'BY', 'code_3' => 'BLR', 'name' => 'Беларусь', 'name_en' => 'Belarus', 'calling_code' => '+375', 'currency' => 'BYN', 'currency_symbol' => 'Br', 'ioc' => 'BLR', 'is_active' => true, 'is_for_phone_select' => true],
            ['code' => 'RU', 'code_3' => 'RUS', 'name' => 'Россия', 'name_en' => 'Russia', 'calling_code' => '+7', 'currency' => 'RUB', 'currency_symbol' => '₽', 'ioc' => 'RUS', 'is_active' => false, 'is_for_phone_select' => false],
            ['code' => 'UA', 'code_3' => 'UKR', 'name' => 'Украина', 'name_en' => 'Ukraine', 'calling_code' => '+380', 'currency' => 'UAH', 'currency_symbol' => '₴', 'ioc' => 'UKR', 'is_active' => false, 'is_for_phone_select' => false],
            ['code' => 'PL', 'code_3' => 'POL', 'name' => 'Польша', 'name_en' => 'Poland', 'calling_code' => '+48', 'currency' => 'PLN', 'currency_symbol' => 'zł', 'ioc' => 'POL', 'is_active' => false, 'is_for_phone_select' => false],
            ['code' => 'LT', 'code_3' => 'LTU', 'name' => 'Литва', 'name_en' => 'Lithuania', 'calling_code' => '+370', 'currency' => 'EUR', 'currency_symbol' => '€', 'ioc' => 'LTU', 'is_active' => false, 'is_for_phone_select' => false],
            ['code' => 'LV', 'code_3' => 'LVA', 'name' => 'Латвия', 'name_en' => 'Latvia', 'calling_code' => '+371', 'currency' => 'EUR', 'currency_symbol' => '€', 'ioc' => 'LVA', 'is_active' => false, 'is_for_phone_select' => false],
            ['code' => 'EE', 'code_3' => 'EST', 'name' => 'Эстония', 'name_en' => 'Estonia', 'calling_code' => '+372', 'currency' => 'EUR', 'currency_symbol' => '€', 'ioc' => 'EST', 'is_active' => false, 'is_for_phone_select' => false],
            ['code' => 'KZ', 'code_3' => 'KAZ', 'name' => 'Казахстан', 'name_en' => 'Kazakhstan', 'calling_code' => '+7', 'currency' => 'KZT', 'currency_symbol' => '₸', 'ioc' => 'KAZ', 'is_active' => false, 'is_for_phone_select' => false],
            ['code' => 'MD', 'code_3' => 'MDA', 'name' => 'Молдова', 'name_en' => 'Moldova', 'calling_code' => '+373', 'currency' => 'MDL', 'currency_symbol' => 'L', 'ioc' => 'MDA', 'is_active' => false, 'is_for_phone_select' => false],
            ['code' => 'GE', 'code_3' => 'GEO', 'name' => 'Грузия', 'name_en' => 'Georgia', 'calling_code' => '+995', 'currency' => 'GEL', 'currency_symbol' => '₾', 'ioc' => 'GEO', 'is_active' => false, 'is_for_phone_select' => false],
            ['code' => 'AM', 'code_3' => 'ARM', 'name' => 'Армения', 'name_en' => 'Armenia', 'calling_code' => '+374', 'currency' => 'AMD', 'currency_symbol' => '֏', 'ioc' => 'ARM', 'is_active' => false, 'is_for_phone_select' => false],
            ['code' => 'AZ', 'code_3' => 'AZE', 'name' => 'Азербайджан', 'name_en' => 'Azerbaijan', 'calling_code' => '+994', 'currency' => 'AZN', 'currency_symbol' => '₼', 'ioc' => 'AZE', 'is_active' => false, 'is_for_phone_select' => false],
            ['code' => 'DE', 'code_3' => 'DEU', 'name' => 'Германия', 'name_en' => 'Germany', 'calling_code' => '+49', 'currency' => 'EUR', 'currency_symbol' => '€', 'ioc' => 'DEU', 'is_active' => false, 'is_for_phone_select' => false],
            ['code' => 'FR', 'code_3' => 'FRA', 'name' => 'Франция', 'name_en' => 'France', 'calling_code' => '+33', 'currency' => 'EUR', 'currency_symbol' => '€', 'ioc' => 'FRA', 'is_active' => false, 'is_for_phone_select' => false],
            ['code' => 'IT', 'code_3' => 'ITA', 'name' => 'Италия', 'name_en' => 'Italy', 'calling_code' => '+39', 'currency' => 'EUR', 'currency_symbol' => '€', 'ioc' => 'ITA', 'is_active' => false, 'is_for_phone_select' => false],
            ['code' => 'ES', 'code_3' => 'ESP', 'name' => 'Испания', 'name_en' => 'Spain', 'calling_code' => '+34', 'currency' => 'EUR', 'currency_symbol' => '€', 'ioc' => 'ESP', 'is_active' => false, 'is_for_phone_select' => false],
            ['code' => 'GB', 'code_3' => 'GBR', 'name' => 'Великобритания', 'name_en' => 'United Kingdom', 'calling_code' => '+44', 'currency' => 'GBP', 'currency_symbol' => '£', 'ioc' => 'GBR', 'is_active' => false, 'is_for_phone_select' => false],
            ['code' => 'US', 'code_3' => 'USA', 'name' => 'США', 'name_en' => 'United States', 'calling_code' => '+1', 'currency' => 'USD', 'currency_symbol' => '$', 'ioc' => 'USA', 'is_active' => false, 'is_for_phone_select' => false],
            ['code' => 'TR', 'code_3' => 'TUR', 'name' => 'Турция', 'name_en' => 'Turkey', 'calling_code' => '+90', 'currency' => 'TRY', 'currency_symbol' => '₺', 'ioc' => 'TUR', 'is_active' => false, 'is_for_phone_select' => false],
            ['code' => 'CN', 'code_3' => 'CHN', 'name' => 'Китай', 'name_en' => 'China', 'calling_code' => '+86', 'currency' => 'CNY', 'currency_symbol' => '¥', 'ioc' => 'CHN', 'is_active' => false, 'is_for_phone_select' => false],
            ['code' => 'IN', 'code_3' => 'IND', 'name' => 'Индия', 'name_en' => 'India', 'calling_code' => '+91', 'currency' => 'INR', 'currency_symbol' => '₹', 'ioc' => 'IND', 'is_active' => false, 'is_for_phone_select' => false],
            ['code' => 'UZ', 'code_3' => 'UZB', 'name' => 'Узбекистан', 'name_en' => 'Uzbekistan', 'calling_code' => '+998', 'currency' => 'UZS', 'currency_symbol' => 'soʻm', 'ioc' => 'UZB', 'is_active' => false, 'is_for_phone_select' => false],
            ['code' => 'TM', 'code_3' => 'TKM', 'name' => 'Туркменистан', 'name_en' => 'Turkmenistan', 'calling_code' => '+993', 'currency' => 'TMT', 'currency_symbol' => 'm', 'ioc' => 'TKM', 'is_active' => false, 'is_for_phone_select' => false],
            ['code' => 'TJ', 'code_3' => 'TJK', 'name' => 'Таджикистан', 'name_en' => 'Tajikistan', 'calling_code' => '+992', 'currency' => 'TJS', 'currency_symbol' => 'SM', 'ioc' => 'TJK', 'is_active' => false, 'is_for_phone_select' => false],
            ['code' => 'KG', 'code_3' => 'KGZ', 'name' => 'Киргизия', 'name_en' => 'Kyrgyzstan', 'calling_code' => '+996', 'currency' => 'KGS', 'currency_symbol' => 'с', 'ioc' => 'KGZ', 'is_active' => false, 'is_for_phone_select' => false],
        ];

        foreach ($countries as $c) {
            Country::firstOrCreate(
                ['code' => $c['code']],
                [
                    'code_3' => $c['code_3'],
                    'name' => $c['name'],
                    'name_en' => $c['name_en'],
                    'calling_code' => $c['calling_code'],
                    'currency' => $c['currency'],
                    'currency_symbol' => $c['currency_symbol'],
                    'ioc' => $c['ioc'],
                    'is_active' => $c['is_active'],
                    'is_for_phone_select' => $c['is_for_phone_select'],
                ]
            );
        }
    }
}
