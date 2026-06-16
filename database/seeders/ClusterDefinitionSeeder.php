<?php

namespace Database\Seeders;

use App\Models\ClusterDefinition;
use Illuminate\Database\Seeder;

class ClusterDefinitionSeeder extends Seeder
{
    public function run(): void
    {
        $clusters = [
            ['label' => 'Champion', 'description' => 'Recency, frequency, dan monetary tertinggi', 'color_hex' => '#1D9E75', 'icon' => 'trophy', 'action_suggestion' => 'Pertahankan, jadikan brand ambassador'],
            ['label' => 'Loyal', 'description' => 'Frequency dan monetary tinggi, recency sedang', 'color_hex' => '#3B82F6', 'icon' => 'heart', 'action_suggestion' => 'Reward, upsell produk premium'],
            ['label' => 'Potential', 'description' => 'Recency tinggi, frequency dan monetary rendah', 'color_hex' => '#F59E0B', 'icon' => 'star', 'action_suggestion' => 'Nurture, dorong transaksi kedua'],
            ['label' => 'At Risk', 'description' => 'Recency menurun, dulunya aktif', 'color_hex' => '#EF4444', 'icon' => 'exclamation-triangle', 'action_suggestion' => 'Promo reaktivasi via WhatsApp'],
            ['label' => 'Lost', 'description' => 'Recency sangat lama, semua skor rendah', 'color_hex' => '#6B7280', 'icon' => 'x-circle', 'action_suggestion' => 'Win-back campaign atau biarkan'],
        ];

        foreach ($clusters as $data) {
            ClusterDefinition::firstOrCreate(['label' => $data['label']], $data);
        }
    }
}
