<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Pegawai;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Master Client with 3 PICs
        Client::updateOrCreate(
            ['email' => 'client@itpi.test'],
            [
                'company' => 'PT Maju Bersama',
                'name' => 'PT Maju Bersama',
                'nickname' => 'AF',
                'email' => 'client@itpi.test',
                'phone' => '(021) 5890-1234',
                'address' => 'Gedung Menara ITPI Lt. 12, Jl. H.R. Rasuna Said Kav. B-4, Jakarta Selatan',
                'active' => true,
                // PIC Marketing
                'pic_marketing_name' => 'Andi Wijaya',
                'pic_marketing_phone' => '081234567890',
                'pic_marketing_email' => 'andi.wijaya@itpi.co.id',
                'pic_marketing_desc' => 'Head of Business Development & Client Relations',
                // PIC IT / Programmer
                'pic_it_name' => 'Budi Santoso',
                'pic_it_phone' => '081298765432',
                'pic_it_email' => 'budi.santoso@itpi.co.id',
                'pic_it_desc' => 'Technical Lead & System Architect PIC',
                // PIC Procurement
                'pic_procurement_name' => 'Citra Lestari',
                'pic_procurement_phone' => '081311223344',
                'pic_procurement_email' => 'citra.lestari@itpi.co.id',
                'pic_procurement_desc' => 'Senior Procurement Specialist & Contract Admin',
            ]
        );

        Client::updateOrCreate(
            ['company' => 'PT Bank Nusantara Raya'],
            [
                'name' => 'PT Bank Nusantara Raya',
                'nickname' => 'BNR',
                'email' => 'contact@banknusantara.co.id',
                'phone' => '(021) 2999-8800',
                'address' => 'Nusantara Tower Lt. 25, Sudirman CBD, Jakarta Pusat',
                'active' => true,
                // PIC Marketing
                'pic_marketing_name' => 'Dian Sastro',
                'pic_marketing_phone' => '081122334455',
                'pic_marketing_email' => 'dian.marketing@banknusantara.co.id',
                'pic_marketing_desc' => 'Product Marketing Manager',
                // PIC IT
                'pic_it_name' => 'Eko Prasetyo',
                'pic_it_phone' => '081199887766',
                'pic_it_email' => 'eko.it@banknusantara.co.id',
                'pic_it_desc' => 'AVP Core Banking Application Lead',
                // PIC Procurement
                'pic_procurement_name' => 'Fiona Anggita',
                'pic_procurement_phone' => '081344556677',
                'pic_procurement_email' => 'fiona.procurement@banknusantara.co.id',
                'pic_procurement_desc' => 'IT Procurement Manager',
            ]
        );

        // 2. Seed Master Pegawai
        $pegawais = [
            [
                'nip' => 'ITP-2023-001',
                'name' => 'Rizky Firmansyah, S.Kom',
                'position' => 'Project Manager (PM)',
                'address' => 'Jl. Tebet Barat Raya No. 15, Jakarta Selatan',
                'is_active' => true,
            ],
            [
                'nip' => 'ITP-2023-014',
                'name' => 'Fajar Nugraha',
                'position' => 'Senior Developer',
                'address' => 'Komplek Permata Buana Blok C3, Jakarta Barat',
                'is_active' => true,
            ],
            [
                'nip' => 'ITP-2023-022',
                'name' => 'Dewi Anggraini',
                'position' => 'QA / Software Tester',
                'address' => 'Jl. Kemang Timur No. 8, Jakarta Selatan',
                'is_active' => true,
            ],
            [
                'nip' => 'ITP-2024-005',
                'name' => 'Bambang Pamungkas',
                'position' => 'System Analyst',
                'address' => 'Jl. Fatmawati Raya No. 40, Jakarta Selatan',
                'is_active' => true,
            ],
        ];

        foreach ($pegawais as $peg) {
            Pegawai::updateOrCreate(['nip' => $peg['nip']], $peg);
        }
    }
}
