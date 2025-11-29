<?php

namespace Database\Seeders;

use App\Models\Campaign;
use App\Models\User;
use Illuminate\Database\Seeder;

class CampaignSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@acme.com')->first();
        $campaignManager = User::where('email', 'campaigns@acme.com')->first();
        $employee = User::where('email', 'john@acme.com')->first();

        // Create sample campaigns
        $campaigns = [
            [
                'title' => 'Help Build Schools in Rural Areas',
                'description' => 'Join us in building educational infrastructure in underserved rural communities. Your contribution will help provide quality education facilities, including classrooms, libraries, and computer labs for children who lack access to proper educational resources. Every dollar counts towards creating a brighter future for these children.',
                'cause_category' => Campaign::CATEGORY_EDUCATION,
                'target_amount' => 50000.00,
                'current_amount' => 15750.00,
                'start_date' => now(),
                'end_date' => now()->addMonths(3),
                'status' => Campaign::STATUS_ACTIVE,
                'created_by' => $employee->id,
                'approved_by' => $campaignManager->id,
                'approved_at' => now(),
                'is_featured' => true,
            ],
            [
                'title' => 'Clean Water Initiative',
                'description' => 'Help provide clean, safe drinking water to communities in need. This initiative focuses on installing water filtration systems and wells in areas where access to clean water is limited. Your support will directly impact the health and well-being of hundreds of families.',
                'cause_category' => Campaign::CATEGORY_HEALTH,
                'target_amount' => 25000.00,
                'current_amount' => 8200.00,
                'start_date' => now(),
                'end_date' => now()->addMonths(2),
                'status' => Campaign::STATUS_ACTIVE,
                'created_by' => $employee->id,
                'approved_by' => $admin->id,
                'approved_at' => now(),
                'is_featured' => false,
            ],
            [
                'title' => 'Emergency Relief Fund for Natural Disasters',
                'description' => 'Create a rapid response fund for communities affected by natural disasters. This fund will provide immediate assistance including food, shelter, medical supplies, and emergency aid to those impacted by hurricanes, floods, earthquakes, and other natural calamities.',
                'cause_category' => Campaign::CATEGORY_EMERGENCY,
                'target_amount' => 75000.00,
                'current_amount' => 32150.00,
                'start_date' => now()->subDays(15),
                'end_date' => now()->addMonths(6),
                'status' => Campaign::STATUS_ACTIVE,
                'created_by' => $campaignManager->id,
                'approved_by' => $admin->id,
                'approved_at' => now()->subDays(14),
                'is_featured' => true,
            ],
            [
                'title' => 'Community Garden Project',
                'description' => 'Support local food security by helping establish community gardens in urban areas. This project will create sustainable food sources, promote environmental awareness, and bring communities together through shared gardening activities.',
                'cause_category' => Campaign::CATEGORY_COMMUNITY,
                'target_amount' => 15000.00,
                'current_amount' => 5600.00,
                'start_date' => now()->addDays(7),
                'end_date' => now()->addMonths(4),
                'status' => Campaign::STATUS_ACTIVE,
                'created_by' => $employee->id,
                'approved_by' => $campaignManager->id,
                'approved_at' => now(),
                'is_featured' => false,
            ],
            [
                'title' => 'Environmental Conservation Program',
                'description' => 'Contribute to environmental protection through reforestation, wildlife conservation, and pollution cleanup initiatives. This comprehensive program aims to restore natural habitats and promote sustainable environmental practices in our local communities.',
                'cause_category' => Campaign::CATEGORY_ENVIRONMENT,
                'target_amount' => 40000.00,
                'current_amount' => 12300.00,
                'start_date' => now(),
                'end_date' => now()->addMonths(5),
                'status' => Campaign::STATUS_ACTIVE,
                'created_by' => $campaignManager->id,
                'approved_by' => $admin->id,
                'approved_at' => now(),
                'is_featured' => false,
            ],
        ];

        foreach ($campaigns as $campaignData) {
            Campaign::create($campaignData);
        }

        $this->command->info('Sample campaigns created successfully!');
    }
}
