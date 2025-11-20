<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\TrustTransaction;

class TrustDemoSeeder extends Seeder
{
    public function run(): void
    {
        // Assign baseline trust scores explicitly (in case factory didn't include them yet)
        User::chunk(100, function ($users) {
            foreach ($users as $u) {
                if ($u->trust_score === null) {
                    $u->trust_score = 100;
                    $u->trust_tier = 'bronze';
                    $u->save();
                }
            }
        });

        // Add a few sample transactions for vendors to illustrate tiers
        $vendors = User::where('user_type', 'vendor')->get();
        foreach ($vendors as $vendor) {
            if (method_exists($vendor, 'adjustTrustScore')) {
                $vendor->adjustTrustScore(15, 'manual_boost', null, 'Demo boost for presentation');
                $vendor->adjustTrustScore(-5, 'late_delivery_penalty', null, 'Simulated late delivery');
            } else {
                // Fallback direct insert if methods unavailable
                TrustTransaction::create([
                    'user_id' => $vendor->id,
                    'amount' => 10,
                    'type' => 'manual_boost',
                    'reason' => 'Demo boost fallback',
                ]);
            }
        }
    }
}
