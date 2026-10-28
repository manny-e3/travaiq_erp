<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PricingRatesTransportationSeeder extends Seeder
{
    public function run()
    {
        $rates = [
            // Airport Transfer
            [
                'key' => 'airport_base_distance',
                'label' => 'Base Distance Threshold (Miles)',
                'value' => 20.00,
                'category' => 'airport',
            ],
            [
                'key' => 'airport_extra_mile_rate',
                'label' => 'Extra Mile Charge ($)',
                'value' => 5.00,
                'category' => 'airport',
            ],
            [
                'key' => 'airport_gate_fee',
                'label' => 'Airport / Gate Fee ($)',
                'value' => 15.00,
                'category' => 'airport',
            ],

            // Hourly Service
            [
                'key' => 'hourly_minimum_hours',
                'label' => 'Minimum Booking Duration (Hours)',
                'value' => 2.00,
                'category' => 'hourly',
            ],
            [
                'key' => 'hourly_extra_hour_discount',
                'label' => 'Extra Hours Discount (%)',
                'value' => 40.00,
                'category' => 'hourly',
            ],
            [
                'key' => 'hourly_miles_per_hour',
                'label' => 'Miles Included Per Hour',
                'value' => 25.00,
                'category' => 'hourly',
            ],

            // Extra Surcharges
            [
                'key' => 'luggage_surcharge_per_bag',
                'label' => 'Charge Per Extra Bag ($)',
                'value' => 4.00,
                'category' => 'surcharges',
            ],
            [
                'key' => 'luggage_free_limit',
                'label' => 'Included Bags Limit',
                'value' => 2.00,
                'category' => 'surcharges',
            ],
            [
                'key' => 'passenger_surcharge_per_person',
                'label' => 'Charge Per Extra Passenger ($)',
                'value' => 3.00,
                'category' => 'surcharges',
            ],
            [
                'key' => 'passenger_free_limit',
                'label' => 'Included Passengers Limit',
                'value' => 2.00,
                'category' => 'surcharges',
            ],

            // Peak Hour Surcharges
            [
                'key' => 'peak_surcharge_enabled',
                'label' => 'Enable Peak-Hour Surcharges (1=Yes, 0=No)',
                'value' => 1.00,
                'category' => 'peak_hour',
            ],
            [
                'key' => 'peak_start_time',
                'label' => 'Peak Start Hour (24h style, e.g. 17 for 5 PM)',
                'value' => 17.00,
                'category' => 'peak_hour',
            ],
            [
                'key' => 'peak_end_time',
                'label' => 'Peak End Hour (24h style, e.g. 20 for 8 PM)',
                'value' => 20.00,
                'category' => 'peak_hour',
            ],
            [
                'key' => 'peak_surcharge_is_percent',
                'label' => 'Surcharge is Percentage (1=Yes, 0=Flat)',
                'value' => 1.00,
                'category' => 'peak_hour',
            ],
            [
                'key' => 'peak_surcharge_value',
                'label' => 'Surcharge Value (Percent or Flat Amount)',
                'value' => 15.00,
                'category' => 'peak_hour',
            ],
        ];

        // Clear existing editorial/publishing rates to avoid confusion
        DB::table('pricing_rates')->whereIn('category', ['general', 'editorial', 'design', 'printing'])->delete();

        foreach ($rates as $rate) {
            DB::table('pricing_rates')->updateOrInsert(['key' => $rate['key']], $rate);
        }
    }
}
