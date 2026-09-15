<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        // 101. Cien Años (Manifestation 1)
        Item::firstOrCreate(['barcode' => 'CAS-C1'], ['manifestation_id' => 1, 'home_branch_id' => 3, 'current_branch_id' => 3, 'shelf_location' => 'ESTANTE-C01', 'status' => 'available']);
        Item::firstOrCreate(['barcode' => 'CAS-C2'], ['manifestation_id' => 1, 'home_branch_id' => 3, 'current_branch_id' => 3, 'shelf_location' => 'ESTANTE-C02', 'status' => 'available']);
        Item::firstOrCreate(['barcode' => 'CAS-N1'], ['manifestation_id' => 1, 'home_branch_id' => 1, 'current_branch_id' => 1, 'shelf_location' => 'ESTANTE-A04', 'status' => 'available']);

        // 102. Rayuela (Manifestation 2)
        Item::firstOrCreate(['barcode' => 'RAY-S1'], ['manifestation_id' => 2, 'home_branch_id' => 2, 'current_branch_id' => 2, 'shelf_location' => 'ESTANTE-B01', 'status' => 'available']);
        Item::firstOrCreate(['barcode' => 'RAY-N1'], ['manifestation_id' => 2, 'home_branch_id' => 1, 'current_branch_id' => 1, 'shelf_location' => 'ESTANTE-A05', 'status' => 'available']);

        // 103. Ficciones (Manifestation 3)
        Item::firstOrCreate(['barcode' => 'FIC-N1'], ['manifestation_id' => 3, 'home_branch_id' => 1, 'current_branch_id' => 1, 'shelf_location' => 'ESTANTE-A06', 'status' => 'available']);
        Item::firstOrCreate(['barcode' => 'FIC-T1'], ['manifestation_id' => 3, 'home_branch_id' => 4, 'current_branch_id' => 4, 'shelf_location' => 'ESTANTE-D01', 'status' => 'available']);

        // 104. La Casa de los Espíritus (Manifestation 4 - Zero Stock Test Case)
        Item::firstOrCreate(['barcode' => 'CASA-C1'], ['manifestation_id' => 4, 'home_branch_id' => 3, 'current_branch_id' => 3, 'shelf_location' => 'ESTANTE-C03', 'status' => 'loaned']);

        // 105. 1984 (Manifestations 5 & 6)
        Item::firstOrCreate(['barcode' => '1984-S1'], ['manifestation_id' => 5, 'home_branch_id' => 2, 'current_branch_id' => 2, 'shelf_location' => 'ESTANTE-B02', 'status' => 'available']);
        Item::firstOrCreate(['barcode' => '1984-ENG-1'], ['manifestation_id' => 6, 'home_branch_id' => 1, 'current_branch_id' => 1, 'shelf_location' => 'ESTANTE-A07', 'status' => 'available']);

        // 106. Don Quijote (Manifestation 7)
        Item::firstOrCreate(['barcode' => 'QUIJ-N1'], ['manifestation_id' => 7, 'home_branch_id' => 1, 'current_branch_id' => 1, 'shelf_location' => 'ESTANTE-A08', 'status' => 'available']);
        Item::firstOrCreate(['barcode' => 'QUIJ-T1'], ['manifestation_id' => 7, 'home_branch_id' => 4, 'current_branch_id' => 4, 'shelf_location' => 'ESTANTE-D02', 'status' => 'available']);

        // 107. El Señor de los Anillos (Manifestations 8 & 9)
        Item::firstOrCreate(['barcode' => 'LOTR-N1'], ['manifestation_id' => 8, 'home_branch_id' => 1, 'current_branch_id' => 1, 'shelf_location' => 'ESTANTE-A01', 'status' => 'available']);
        Item::firstOrCreate(['barcode' => 'LOTR-S1'], ['manifestation_id' => 8, 'home_branch_id' => 2, 'current_branch_id' => 2, 'shelf_location' => 'ESTANTE-B03', 'status' => 'available']);
        Item::firstOrCreate(['barcode' => 'LOTR-ENG-1'], ['manifestation_id' => 9, 'home_branch_id' => 1, 'current_branch_id' => 1, 'shelf_location' => 'ESTANTE-A02', 'status' => 'available']);

        // 108. La Ciudad y los Perros (Manifestation 10)
        Item::firstOrCreate(['barcode' => 'CIU-N1'], ['manifestation_id' => 10, 'home_branch_id' => 1, 'current_branch_id' => 1, 'shelf_location' => 'ESTANTE-A09', 'status' => 'available']);
        Item::firstOrCreate(['barcode' => 'CIU-S1'], ['manifestation_id' => 10, 'home_branch_id' => 2, 'current_branch_id' => 2, 'shelf_location' => 'ESTANTE-B04', 'status' => 'available']);

        // 109. Flora Huayaquilensis (Manifestation 11 - Rare Botanical Collection)
        Item::firstOrCreate(['barcode' => 'HUAY-N1'], ['manifestation_id' => 11, 'home_branch_id' => 1, 'current_branch_id' => 1, 'shelf_location' => 'RARA-BOT-01', 'status' => 'available']);
        Item::firstOrCreate(['barcode' => 'HUAY-C1'], ['manifestation_id' => 11, 'home_branch_id' => 3, 'current_branch_id' => 1, 'shelf_location' => 'RARA-BOT-02', 'status' => 'in_transit']);

        // 110. El Origen de las Especies (Manifestation 12)
        Item::firstOrCreate(['barcode' => 'DAR-N1'], ['manifestation_id' => 12, 'home_branch_id' => 1, 'current_branch_id' => 1, 'shelf_location' => 'CIENC-A01', 'status' => 'available']);
        Item::firstOrCreate(['barcode' => 'DAR-S1'], ['manifestation_id' => 12, 'home_branch_id' => 2, 'current_branch_id' => 2, 'shelf_location' => 'CIENC-B01', 'status' => 'available']);

        // 111. Cosmos (Manifestation 13)
        Item::firstOrCreate(['barcode' => 'COS-N1'], ['manifestation_id' => 13, 'home_branch_id' => 1, 'current_branch_id' => 1, 'shelf_location' => 'CIENC-A02', 'status' => 'available']);
        Item::firstOrCreate(['barcode' => 'COS-T1'], ['manifestation_id' => 13, 'home_branch_id' => 4, 'current_branch_id' => 4, 'shelf_location' => 'CIENC-D01', 'status' => 'available']);

        // 112. Sapiens (Manifestation 14)
        Item::firstOrCreate(['barcode' => 'SAP-C1'], ['manifestation_id' => 14, 'home_branch_id' => 3, 'current_branch_id' => 3, 'shelf_location' => 'ENS-C01', 'status' => 'available']);
        Item::firstOrCreate(['barcode' => 'SAP-N1'], ['manifestation_id' => 14, 'home_branch_id' => 1, 'current_branch_id' => 1, 'shelf_location' => 'ENS-A01', 'status' => 'available']);

        // 113. El Laberinto de la Soledad (Manifestation 15)
        Item::firstOrCreate(['barcode' => 'LAB-N1'], ['manifestation_id' => 15, 'home_branch_id' => 1, 'current_branch_id' => 1, 'shelf_location' => 'ENS-A02', 'status' => 'available']);
        Item::firstOrCreate(['barcode' => 'LAB-S1'], ['manifestation_id' => 15, 'home_branch_id' => 2, 'current_branch_id' => 2, 'shelf_location' => 'ENS-B01', 'status' => 'available']);
    }
}
