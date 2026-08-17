<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $projects = [
            ['Portfolio v2', 'Personal', 'Website Portfolio', ['React', 'Tailwind'], 'https://avinto.my.id', 1, 5],
            ['AIPT UINSU', 'UIN Sumatera Utara', 'Sistem Informasi', ['Laravel', 'Bootstrap'], 'https://aipt.uinsu.ac.id', 2, 4],
            ['UIN SU Main', 'UIN Sumatera Utara', 'Website Institusi', ['WordPress'], 'https://uinsu.ac.id', 3, 4],
            ['Tracer Study', 'UIN Sumatera Utara', 'Sistem Informasi', ['Laravel', 'Bootstrap'], 'https://tracerstudy.uinsu.ac.id', 4, 1],
            ['Tracer Career', 'UIN Sumatera Utara', 'Sistem Informasi', ['Laravel'], 'https://tracerstudy.uinsu.ac.id/career', 5, 1],
            ['Repository', 'UIN Sumatera Utara', 'Sistem Informasi', ['Laravel', 'Bootstrap', 'Flutter'], 'https://repository1.uinsu.ac.id', 6, 5],
            ['APS UINSU', 'UIN Sumatera Utara', 'Sistem Informasi', ['Laravel', 'Bootstrap'], 'https://aps.uinsu.ac.id', 22, 5],
            ['KKN 16', 'KKN UINSU', 'Website Komunitas', ['Bootstrap'], 'https://kkn16uinsu.github.io', 7, 5],
            ['Tani Pematangkuing', 'Desa Pematang Kuing', 'Website Komunitas', ['Bootstrap'], 'https://tanipematangkuing.github.io', 8, 4],
            ['Novel Reader', 'Personal', 'Web Application', ['Bootstrap'], 'https://alvinalvito.github.io/novel', 9, 2],
            ['Jurusan', 'UIN Sumatera Utara', 'Sistem Informasi', ['Laravel', 'Bootstrap'], 'http://jurusan.avinto.my.id', 10, 3],
            ['Skripsi Tools', 'Personal', 'Web Application', ['React', 'Tailwind'], 'https://skripsi.avinto.my.id', 11, 3],
            ['MyUINSU', 'UIN Sumatera Utara', 'Mobile & Web', ['React', 'Tailwind', 'Flutter'], 'https://myuinsu.avinto.my.id', 12, 4],
            ['SiBima', 'UIN Sumatera Utara', 'Mobile & Web', ['React', 'Tailwind', 'Express.js', 'Flutter'], 'https://sibima.uinsu.ac.id', 13, 10],
            ['SiKerma', 'UIN Sumatera Utara', 'Sistem Informasi', ['Laravel', 'Bootstrap'], 'https://sikerma.uinsu.ac.id', 14, 4],
            ['Upah Kerja', 'Proyek Independen', 'Web Application', ['Laravel', 'Bootstrap'], 'http://upahkerja.avinto.my.id', 15, 2],
            ['Sembako', 'Proyek Independen', 'Web Application', ['Laravel', 'Bootstrap'], 'http://sembako.avinto.my.id', 16, 3],
            ['Diagnosa Penyakit', 'Proyek Independen', 'Sistem Pakar', ['Laravel', 'Bootstrap'], 'http://penyakit.avinto.my.id', 17, 3],
            ['Biology LMS', 'Proyek Independen', 'Learning Management System', ['Laravel', 'Bootstrap'], 'http://biology.avinto.my.id', 18, 3],
            ['Rekomendasi Saham', 'Proyek Independen', 'Sistem Pendukung Keputusan', ['Laravel', 'Bootstrap'], 'http://saham.avinto.my.id', 19, 4],
            ['SI Pental', 'UIN Sumatera Utara', 'Mobile & Web', ['Laravel', 'Bootstrap', 'Flutter'], 'http://si-pental.com', 20, 7],
            ['Rumah Jurnal', 'Proyek Independen', 'Web Application', ['Laravel', 'Bootstrap'], 'http://jurnal.avinto.my.id', 21, 4],
        ];

        foreach ($projects as $index => [$title, $client, $category, $technologies, $url, $imageNumber, $imageCount]) {
            $images = collect(range(1, $imageCount))->map(fn ($position) => '/assets/portfolio/projects/'.$imageNumber.($position > 1 ? '-'.$position : '').'.png')->all();
            Project::updateOrCreate(['slug' => Str::slug($title)], [
                'title' => $title, 'client' => $client, 'category' => $category,
                'summary' => $title.' adalah proyek '.$category.' yang dirancang dan dikembangkan oleh Avinto Project.',
                'description' => 'Proyek ini mencakup perancangan antarmuka, implementasi fitur, integrasi data, pengujian, dan penyempurnaan pengalaman pengguna sesuai kebutuhan klien.',
                'cover_image' => $images[0], 'images' => $images, 'technologies' => $technologies,
                'project_url' => $url, 'year' => $index < 10 ? 2024 : 2025, 'status' => 'completed',
                'is_featured' => in_array($imageNumber, [2, 6, 12, 13, 20, 22]), 'is_published' => true, 'sort_order' => $index + 1,
            ]);
        }
    }
}
