<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SlaPolicy;

class SlaPolicySeeder extends Seeder
{
    public function run(): void
    {
        $policies = [
            // Standard Tier
            [
                'name' => 'Standard Tier - Low Priority',
                'customer_tier' => 'Standard',
                'category' => null,
                'priority' => 'Low',
                'response_time_hours' => 24.0,
                'resolution_time_hours' => 48.0,
                'is_active' => true,
            ],
            [
                'name' => 'Standard Tier - Medium Priority',
                'customer_tier' => 'Standard',
                'category' => null,
                'priority' => 'Medium',
                'response_time_hours' => 8.0,
                'resolution_time_hours' => 24.0,
                'is_active' => true,
            ],
            [
                'name' => 'Standard Tier - High Priority',
                'customer_tier' => 'Standard',
                'category' => null,
                'priority' => 'High',
                'response_time_hours' => 4.0,
                'resolution_time_hours' => 12.0,
                'is_active' => true,
            ],
            [
                'name' => 'Standard Tier - Urgent Priority',
                'customer_tier' => 'Standard',
                'category' => null,
                'priority' => 'Urgent',
                'response_time_hours' => 2.0,
                'resolution_time_hours' => 2.0,
                'is_active' => true,
            ],
            // Gold Tier
            [
                'name' => 'Gold Tier - Low Priority',
                'customer_tier' => 'Gold',
                'category' => null,
                'priority' => 'Low',
                'response_time_hours' => 12.0,
                'resolution_time_hours' => 24.0,
                'is_active' => true,
            ],
            [
                'name' => 'Gold Tier - Medium Priority',
                'customer_tier' => 'Gold',
                'category' => null,
                'priority' => 'Medium',
                'response_time_hours' => 4.0,
                'resolution_time_hours' => 12.0,
                'is_active' => true,
            ],
            [
                'name' => 'Gold Tier - High Priority',
                'customer_tier' => 'Gold',
                'category' => null,
                'priority' => 'High',
                'response_time_hours' => 2.0,
                'resolution_time_hours' => 6.0,
                'is_active' => true,
            ],
            [
                'name' => 'Gold Tier - Urgent Priority',
                'customer_tier' => 'Gold',
                'category' => null,
                'priority' => 'Urgent',
                'response_time_hours' => 1.0,
                'resolution_time_hours' => 3.0,
                'is_active' => true,
            ],
            // Platinum Tier
            [
                'name' => 'Platinum Tier - Low Priority',
                'customer_tier' => 'Platinum',
                'category' => null,
                'priority' => 'Low',
                'response_time_hours' => 4.0,
                'resolution_time_hours' => 8.0,
                'is_active' => true,
            ],
            [
                'name' => 'Platinum Tier - Medium Priority',
                'customer_tier' => 'Platinum',
                'category' => null,
                'priority' => 'Medium',
                'response_time_hours' => 2.0,
                'resolution_time_hours' => 4.0,
                'is_active' => true,
            ],
            [
                'name' => 'Platinum Tier - High Priority',
                'customer_tier' => 'Platinum',
                'category' => null,
                'priority' => 'High',
                'response_time_hours' => 1.0,
                'resolution_time_hours' => 2.0,
                'is_active' => true,
            ],
            [
                'name' => 'Platinum Tier - Urgent Priority',
                'customer_tier' => 'Platinum',
                'category' => null,
                'priority' => 'Urgent',
                'response_time_hours' => 0.25, // 15 mins
                'resolution_time_hours' => 2.0,
                'is_active' => true,
            ],
        ];

        foreach ($policies as $policyData) {
            SlaPolicy::updateOrCreate(
                [
                    'customer_tier' => $policyData['customer_tier'],
                    'category' => $policyData['category'],
                    'priority' => $policyData['priority'],
                ],
                $policyData
            );
        }
    }
}
