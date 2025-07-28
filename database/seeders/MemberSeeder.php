<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Member; // Pastikan ini di-import
use Illuminate\Support\Facades\Schema; // Untuk truncate

class MemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Opsional: Kosongkan tabel sebelum seeding untuk menghindari duplikasi
        // Hanya lakukan ini jika Anda ingin tabel selalu bersih saat seeder dijalankan
        Schema::disableForeignKeyConstraints(); // Nonaktifkan foreign key checks sementara
        Member::truncate();
        Schema::enableForeignKeyConstraints(); // Aktifkan kembali

        $members = [
            [
                'name' => 'Egidius Dicky Narendra Baas',
                'role' => ['Team Leader', 'UI/UX Designer'], // Array of roles
                'task' => [
                    'Mengkoordinasikan tim dan arsitektur proyek',
                    'Mengembangkan antarmuka pengguna',
                    'Manajemen database'
                ],
                'image' => 'https://avatars.githubusercontent.com/u/162414603?v=4',
                'github' => 'https://github.com/egidiusdicky',
            ],
            [
                'name' => 'Rayan',
                'role' => ['Frontend Developer', 'Backend Developer'],
                'task' => [
                    'Mengembangkan API backend',
                    'Merancang pengalaman interaktif',
                    'Implementasi desain responsif'
                ],
                'image' => 'https://avatars.githubusercontent.com/u/87006289?v=4',
                'github' => 'https://github.com/rayanbersabal',
            ],
            [
                'name' => 'Garda Fitrananda',
                'role' => ['Frontend Developer'],
                'task' => [
                    'Implementasi desain visual',
                    'Memastikan responsivitas layout',
                    'Optimasi performa frontend'
                ],
                'image' => 'https://avatars.githubusercontent.com/u/202229964?v=4',
                'github' => 'https://github.com/gardafitrananda',
            ],
            [
                'name' => 'Sauzana',
                'role' => ['Frontend Developer'],
                'task' => [
                    'Manajemen state aplikasi',
                    'Integrasi dengan komponen backend',
                    'Debugging UI'
                ],
                'image' => 'https://avatars.githubusercontent.com/u/202231744?v=4',
                'github' => 'https://github.com/Sauzana1919',
            ],
            [
                'name' => 'Sandi Setiawan',
                'role' => ['Frontend Developer'],
                'task' => [
                    'Optimasi performa antarmuka pengguna',
                    'Mengembangkan fungsionalitas UI',
                    'Testing cross-browser compatibility'
                ],
                'image' => 'https://avatars.githubusercontent.com/u/193219383?v=4',
                'github' => 'https://github.com/SandiSetiawann',
            ],
            [
                'name' => 'Fahrudiansyah',
                'role' => ['Frontend Developer'],
                'task' => [
                    'Membangun komponen UI yang reusable',
                    'Menerapkan logika bisnis di frontend',
                    'Melakukan code review'
                ],
                'image' => 'https://avatars.githubusercontent.com/u/202230345?v=4',
                'github' => 'https://github.com/Fahrudiyansah',
            ],
        ];

        foreach ($members as $member) {
            Member::create($member);
        }

        $this->command->info('Members seeded successfully!');
    }
}
