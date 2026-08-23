<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class FullReferenceDataSeeder extends Seeder
{
    use WithoutModelEvents;

    private array $euCodes = [
        'AT', 'BE', 'BG', 'HR', 'CY', 'CZ', 'DK', 'EE', 'FI', 'FR', 'DE', 'GR',
        'HU', 'IE', 'IT', 'LV', 'LT', 'LU', 'MT', 'NL', 'PL', 'PT', 'RO', 'SK',
        'SI', 'ES', 'SE',
    ];

    public function run(): void
    {
        $countriesPath = base_path('vendor/stefangabos/world_countries/data/countries/en/world.json');
        $subdivisionsPath = base_path('vendor/stefangabos/world_countries/data/subdivisions/subdivisions.json');

        if (! File::exists($countriesPath) || ! File::exists($subdivisionsPath)) {
            $this->command->error('Required country data files are missing.');
            return;
        }

        $uk = DB::table('global_regions')->where('code', 'UK')->first();
        $eu = DB::table('global_regions')->where('code', 'EU')->first();
        $usa = DB::table('global_regions')->where('code', 'USA')->first();

        $rowId = DB::table('global_regions')->insertGetId([
            'name' => 'Rest of World',
            'code' => 'ROW',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $regionMap = [
            'GB' => $uk?->id,
            'US' => $usa?->id,
        ];

        foreach ($this->euCodes as $code) {
            $regionMap[$code] = $eu?->id;
        }

        $countries = collect(json_decode(File::get($countriesPath), true));
        $countryRows = [];

        foreach ($countries as $country) {
            $alpha2 = strtoupper($country['alpha2']);
            $countryRows[] = [
                'global_region_id' => $regionMap[$alpha2] ?? $rowId,
                'name' => $country['name'],
                'iso2' => $alpha2,
                'iso3' => strtoupper($country['alpha3'] ?? ''),
                'dial_code' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('counties_states')->delete();
        DB::table('countries')->delete();
        DB::table('countries')->insert($countryRows);

        $countryIds = DB::table('countries')->pluck('id', 'iso2');

        $subdivisions = collect(json_decode(File::get($subdivisionsPath), true));
        $stateRows = [];

        foreach ($subdivisions as $subdivision) {
            $iso2 = strtoupper($subdivision['country']);

            if (! $countryIds->has($iso2)) {
                continue;
            }

            $stateRows[] = [
                'country_id' => $countryIds[$iso2],
                'name' => $subdivision['name'],
                'code' => $subdivision['code'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        foreach (array_chunk($stateRows, 500) as $chunk) {
            DB::table('counties_states')->insert($chunk);
        }
    }
}
