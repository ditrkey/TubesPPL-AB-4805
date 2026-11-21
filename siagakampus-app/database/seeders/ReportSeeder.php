<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\Report;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Buat Pengguna Utama
        $admin = User::firstOrCreate(
            ['email' => 'admin@siagakampus.telkomuniversity.ac.id'],
            [
                'name' => 'Aditya Rizky (Admin Operasional)',
                'password' => Hash::make('password'),
                'phone' => '081234567890',
                'role' => 'Admin',
            ]
        );

        $superadmin = User::firstOrCreate(
            ['email' => 'superadmin@siagakampus.telkomuniversity.ac.id'],
            [
                'name' => 'Hanna Raisa Farrasti (Superadmin)',
                'password' => Hash::make('password'),
                'phone' => '081222333444',
                'role' => 'Superadmin',
            ]
        );

        $warga1 = User::firstOrCreate(
            ['email' => 'nizam@student.telkomuniversity.ac.id'],
            [
                'name' => 'Muhammad Nizam',
                'password' => Hash::make('password'),
                'phone' => '081345678901',
                'role' => 'Warga Tel-U',
            ]
        );

        $warga2 = User::firstOrCreate(
            ['email' => 'yency@student.telkomuniversity.ac.id'],
            [
                'name' => 'Yency Wiradjaja',
                'password' => Hash::make('password'),
                'phone' => '081398765432',
                'role' => 'Warga Tel-U',
            ]
        );

        $warga3 = User::firstOrCreate(
            ['email' => 'feyruz@student.telkomuniversity.ac.id'],
            [
                'name' => 'Feyruz Challisa',
                'password' => Hash::make('password'),
                'phone' => '081277665544',
                'role' => 'Warga Tel-U',
            ]
        );

        // 2. Buat Zona Rawan Kampus Tel-U Bandung
        $driver = DB::connection()->getDriverName();
        $isMySql = in_array($driver, ['mysql', 'mariadb']);

        $zoneData = [
            [
                'name' => 'Zona Gerbang Depan (Main Gate)',
                'description' => 'Area titik temu saluran luar kampus dan jalan raya Jl. Telekomunikasi, rawan luapan saluran drainase primer.',
                'risk_level' => 'tinggi',
                'polygon' => 'POLYGON((107.6300 -6.9720, 107.6320 -6.9720, 107.6320 -6.9735, 107.6300 -6.9735, 107.6300 -6.9720))',
            ],
            [
                'name' => 'Zona Gedung Kuliah Umum (GKU)',
                'description' => 'Selasar antara GKU dan Gedung Panehan, potensi genangan air hujan saat intensitas curah hujan tinggi.',
                'risk_level' => 'sedang',
                'polygon' => 'POLYGON((107.6325 -6.9740, 107.6340 -6.9740, 107.6340 -6.9755, 107.6325 -6.9755, 107.6325 -6.9740))',
            ],
            [
                'name' => 'Zona Parkir Basement TULT',
                'description' => 'Area lereng parkir basement Gedung Kuliah Terpadu TULT dengan risiko rembesan air tanah.',
                'risk_level' => 'tinggi',
                'polygon' => 'POLYGON((107.6290 -6.9745, 107.6310 -6.9745, 107.6310 -6.9760, 107.6290 -6.9760, 107.6290 -6.9745))',
            ],
            [
                'name' => 'Zona Laboratorium & Danau Galau',
                'description' => 'Area resapan dan tampungan air danau buatan kampus.',
                'risk_level' => 'rendah',
                'polygon' => 'POLYGON((107.6330 -6.9715, 107.6355 -6.9715, 107.6355 -6.9730, 107.6330 -6.9730, 107.6330 -6.9715))',
            ],
        ];

        $createdZones = [];
        foreach ($zoneData as $z) {
            $boundary = $isMySql 
                ? DB::raw("ST_GeomFromText('{$z['polygon']}', 4326)")
                : $z['polygon'];

            $createdZones[] = Zone::firstOrCreate(
                ['name' => $z['name']],
                [
                    'description' => $z['description'],
                    'risk_level' => $z['risk_level'],
                    'boundary' => $boundary,
                ]
            );
        }

        // 3. Buat Laporan Contoh (Reports)
        $reportsSample = [
            [
                'user_id' => $warga1->id,
                'zone_id' => $createdZones[0]->id ?? null,
                'title' => 'Genangan Luapan Drainase di Depan Gerbang Utama',
                'category' => 'Banjir',
                'water_level' => 'High',
                'description' => 'Air meluap hingga setinggi betis orang dewasa (>40 cm) akibat hujan lebat 2 jam tanpa henti. Akses motor dan mobil menuju gerbang depan terhalang.',
                'photo_path' => 'https://images.unsplash.com/photo-1547683905-f686c993aae5?auto=format&fit=crop&w=800&q=80',
                'lat' => -6.9728,
                'lng' => 107.6312,
                'status' => 'Pending',
                'rejection_reason' => null,
            ],
            [
                'user_id' => $warga2->id,
                'zone_id' => $createdZones[1]->id ?? null,
                'title' => 'Sumbatan Selokan Selasar GKU Menuju Kantin',
                'category' => 'Drainase',
                'water_level' => 'Med',
                'description' => 'Terdapat timbunan dedaunan dan sampah di selokan pembuangan selasar GKU, menyebabkan air tergenang sekitar 25 cm dan licin bagi pejalan kaki.',
                'photo_path' => 'https://images.unsplash.com/photo-1517486808906-6ca8b3f04846?auto=format&fit=crop&w=800&q=80',
                'lat' => -6.9745,
                'lng' => 107.6332,
                'status' => 'Verified',
                'rejection_reason' => null,
            ],
            [
                'user_id' => $admin->id,
                'zone_id' => $createdZones[2]->id ?? null,
                'title' => 'Rembesan Air Masuk ke Akses Parkir Motor TULT',
                'category' => 'Akses Tertutup',
                'water_level' => 'Med',
                'description' => 'Laporan manual dari posko keamanan: tanjakan masuk parkir motor TULT licin dan tergenang 20 cm. Petugas K3L sedang menyiapkan pompa hisap.',
                'photo_path' => 'https://images.unsplash.com/photo-1563245372-f21724e3856d?auto=format&fit=crop&w=800&q=80',
                'lat' => -6.9752,
                'lng' => 107.6298,
                'status' => 'Handling',
                'rejection_reason' => null,
            ],
            [
                'user_id' => $warga3->id,
                'zone_id' => $createdZones[3]->id ?? null,
                'title' => 'Air Meluap di Jalur Jogging Tepi Danau Galau',
                'category' => 'Banjir',
                'water_level' => 'Low',
                'description' => 'Genangan air tipis sekitar 10 cm di jalur pejalan kaki tepi danau. Pompa otomatis danau sudah aktif dan air telah surut sepenuhnya.',
                'photo_path' => 'https://images.unsplash.com/photo-1515694346937-94d85e41e6f0?auto=format&fit=crop&w=800&q=80',
                'lat' => -6.9722,
                'lng' => 107.6341,
                'status' => 'Resolved',
                'rejection_reason' => null,
            ],
            [
                'user_id' => $warga1->id,
                'zone_id' => null,
                'title' => 'Laporan Genangan di Jl. Radio Palasari',
                'category' => 'Lainnya',
                'water_level' => 'Low',
                'description' => 'Genangan air di depan kos-kosan gang Radio Palasari.',
                'photo_path' => 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?auto=format&fit=crop&w=800&q=80',
                'lat' => -6.9680,
                'lng' => 107.6250,
                'status' => 'Rejected',
                'rejection_reason' => 'Lokasi berada di luar kawasan kampus Telkom University Bandung dan merupakan kewenangan dinas terkait pemda.',
            ],
        ];

        foreach ($reportsSample as $rep) {
            $location = $isMySql
                ? DB::raw("ST_SRID(Point({$rep['lng']}, {$rep['lat']}), 4326)")
                : "POINT({$rep['lng']} {$rep['lat']})";

            $newReport = Report::create([
                'user_id' => $rep['user_id'],
                'zone_id' => $rep['zone_id'],
                'title' => $rep['title'],
                'category' => $rep['category'],
                'water_level' => $rep['water_level'],
                'description' => $rep['description'],
                'photo_path' => $rep['photo_path'],
                'location' => $location,
                'status' => $rep['status'],
                'rejection_reason' => $rep['rejection_reason'],
            ]);

            // Catat log awal
            AuditLog::record(
                "Inisialisasi sistem: seeding data laporan (#{$newReport->code} - {$newReport->title})",
                $admin->id
            );
        }
    }
}

