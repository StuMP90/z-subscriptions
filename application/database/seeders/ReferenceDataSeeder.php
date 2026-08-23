<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ReferenceDataSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $ukId = DB::table('global_regions')->insertGetId([
            'name' => 'United Kingdom',
            'code' => 'UK',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $euId = DB::table('global_regions')->insertGetId([
            'name' => 'European Union',
            'code' => 'EU',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $usaId = DB::table('global_regions')->insertGetId([
            'name' => 'United States',
            'code' => 'USA',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $gbpId = DB::table('currencies')->insertGetId([
            'name' => 'Pound Sterling',
            'code' => 'GBP',
            'symbol' => '£',
            'decimal_places' => 2,
            'is_base_currency' => true,
            'conversion_rate' => 1,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $usdId = DB::table('currencies')->insertGetId([
            'name' => 'United States Dollar',
            'code' => 'USD',
            'symbol' => '$',
            'decimal_places' => 2,
            'is_base_currency' => false,
            'conversion_rate' => 1.27,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $eurId = DB::table('currencies')->insertGetId([
            'name' => 'Euro',
            'code' => 'EUR',
            'symbol' => '€',
            'decimal_places' => 2,
            'is_base_currency' => false,
            'conversion_rate' => 1.20,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $uk = DB::table('countries')->insertGetId([
            'global_region_id' => $ukId,
            'name' => 'United Kingdom',
            'iso2' => 'GB',
            'iso3' => 'GBR',
            'dial_code' => '+44',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $us = DB::table('countries')->insertGetId([
            'global_region_id' => $usaId,
            'name' => 'United States',
            'iso2' => 'US',
            'iso3' => 'USA',
            'dial_code' => '+1',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $de = DB::table('countries')->insertGetId([
            'global_region_id' => $euId,
            'name' => 'Germany',
            'iso2' => 'DE',
            'iso3' => 'DEU',
            'dial_code' => '+49',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $fr = DB::table('countries')->insertGetId([
            'global_region_id' => $euId,
            'name' => 'France',
            'iso2' => 'FR',
            'iso3' => 'FRA',
            'dial_code' => '+33',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('counties_states')->insert([
            ['country_id' => $uk, 'name' => 'London', 'code' => 'LDN', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['country_id' => $uk, 'name' => 'Greater Manchester', 'code' => 'MAN', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['country_id' => $us, 'name' => 'California', 'code' => 'CA', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['country_id' => $us, 'name' => 'New York', 'code' => 'NY', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['country_id' => $de, 'name' => 'Bavaria', 'code' => 'BY', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['country_id' => $de, 'name' => 'Berlin', 'code' => 'BE', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['country_id' => $fr, 'name' => 'Île-de-France', 'code' => 'IDF', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['country_id' => $fr, 'name' => 'Brittany', 'code' => 'BZH', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('subscription_frequencies')->insert([
            ['name' => 'Daily', 'slug' => 'daily', 'days' => 1, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Weekly', 'slug' => 'weekly', 'days' => 7, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Monthly', 'slug' => 'monthly', 'days' => 30, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Yearly', 'slug' => 'yearly', 'days' => 365, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        $shopId = DB::table('shops')->insertGetId([
            'global_region_id' => $ukId,
            'default_currency_id' => $gbpId,
            'name' => 'Z-Subscriptions',
            'slug' => 'z-subscriptions',
            'domain' => 'shop.zsubscriptions.local',
            'theme' => 'default',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('shop_domains')->insert([
            'shop_id' => $shopId,
            'domain' => 'shop.zsubscriptions.local',
            'is_primary' => true,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
