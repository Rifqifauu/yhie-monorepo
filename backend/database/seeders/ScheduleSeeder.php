<?php
 
namespace Database\Seeders;
 
use Illuminate\Database\Seeder;
use App\Models\Schedule;
use Faker\Factory as Faker;
 
class ScheduleSeeder extends Seeder
 {
     /**
      * Run the database seeds.
      */
     public function run(): void
     {
         $fakerId = Faker::create('id_ID');
         $fakerEn = Faker::create('en_US');
 
         $activities = [
             [
                 'title_id' => 'Pendaftaran Sertifikasi Hafiz Al-Qur\'an 30 Juz',
                 'title_en' => 'International Certification Registration for Hafiz 30 Juz',
                 'desc_id' => 'Pendaftaran resmi untuk sertifikasi hafalan Al-Qur\'an 30 juz berstandar internasional (IAO) periode kuartal ini.',
                 'desc_en' => 'Official registration for the international standard (IAO) Quran memorization certification (30 Juz) for this quarterly period.',
             ],
             [
                 'title_id' => 'Kajian Bulanan & Tadabbur Alam di Wisata Alam Desa Tejasari',
                 'title_en' => 'Monthly Islamic Lecture & Nature Study at Desa Tejasari',
                 'desc_id' => 'Kajian keagamaan dan tadabbur alam bersama para asatidz dan penghafal Al-Qur\'an di kawasan asri Desa Tejasari.',
                 'desc_en' => 'Religious study and nature exploration with scholars and Quran memorizers in the scenic area of Tejasari Village.',
             ],
             [
                 'title_id' => 'Ujian Sertifikasi Hafalan Tanpa Stres (Stress-Free Exam) Tahap I',
                 'title_en' => 'Stress-Free Quran Memorization Exam Phase I',
                 'desc_id' => 'Ujian sertifikasi tahfidz dengan metode santai dan rileks di alam terbuka demi menjaga fokus dan ketenangan mental peserta.',
                 'desc_en' => 'Quran memorization certification exam utilizing a relaxed method in the open air to preserve focus and mental peace.',
             ],
             [
                 'title_id' => 'Workshop Persiapan Mental & Kelancaran Ujian Tahfidz',
                 'title_en' => 'Psychological & Memorization Prep Workshop for Tahfidz Exams',
                 'desc_id' => 'Pembekalan psikologis dan teknik memperkuat memori hafalan sebelum mengikuti sertifikasi tahfidz internasional.',
                 'desc_en' => 'Mental preparation and memory enhancement techniques training prior to undergoing international tahfidz certification.',
             ],
             [
                 'title_id' => 'Sosialisasi Kurikulum Sertifikasi Internasional IAO & YHIE',
                 'title_en' => 'Socialization of IAO & YHIE International Certification Standards',
                 'desc_id' => 'Penjelasan mendalam mengenai kriteria penilaian sertifikasi tahfidz berstandar internasional yang diakui oleh IAO.',
                 'desc_en' => 'In-depth explanation of the international standard evaluation criteria for tahfidz certification recognized by IAO.',
             ],
             [
                 'title_id' => 'Gathering Ukhuwah & Jejaring Eksklusif Para Hafiz Indonesia',
                 'title_en' => 'Ukhuwah Gathering & Exclusive Network of Indonesian Hafiz',
                 'desc_id' => 'Pertemuan khusus alumni dan peserta program YHIE untuk mempererat tali silaturahim dan membangun kolaborasi positif.',
                 'desc_en' => 'A special gathering for YHIE program alumni and participants to strengthen community bonds and positive collaborations.',
             ],
             [
                 'title_id' => 'Bimbingan Intensif Tahfidz Al-Qur\'an Akhir Pekan (Weekend Tahfidz Camp)',
                 'title_en' => 'Intensive Quran Memorization Weekend Camp',
                 'desc_id' => 'Program bimbingan intensif dan setoran hafalan hafiz quran secara tatap muka di lokasi tadabbur alam Desa Tejasari.',
                 'desc_en' => 'Weekend intensive mentoring and memorization deposits program face-to-face at the nature retreat location in Tejasari Village.',
             ],
             [
                 'title_id' => 'Ujian Sertifikasi Hafalan Tanpa Stres (Stress-Free Exam) Tahap II',
                 'title_en' => 'Stress-Free Quran Memorization Exam Phase II',
                 'desc_id' => 'Kelanjutan ujian sertifikasi tahfidz di area wisata alam terbuka dengan fokus kenyamanan dan rileksasi peserta.',
                 'desc_en' => 'Continuation of the tahfidz certification exam in open nature spots, focusing on comfort and relaxation.',
             ],
             [
                 'title_id' => 'Seminar Al-Qur\'an dan Kelestarian Alam Desa Tejasari',
                 'title_en' => 'Quran and Environmental Sustainability Seminar',
                 'desc_id' => 'Membedah ayat-ayat tentang lingkungan hidup dan implikasinya pada kelestarian alam dan pariwisata pedesaan.',
                 'desc_en' => 'Discussing Quranic verses about the environment and their implications on rural conservation and nature tourism.',
             ],
             [
                 'title_id' => 'Wisata Religi & Penyerahan Sertifikat Hafiz Internasional',
                 'title_en' => 'Religious Tour & International Hafiz Certificate Awarding Ceremony',
                 'desc_id' => 'Acara puncak kelulusan, penyerahan sertifikat terakreditasi IAO, serta wisuda tahfidz di alam terbuka Desa Tejasari.',
                 'desc_en' => 'Graduation ceremony, presenting IAO-accredited certificates, and tahfidz graduation in the outdoor nature reserve of Tejasari Village.',
             ]
         ];
 
         for ($i = 1; $i <= 30; $i++) {
             // Pilih aktivitas secara bergantian
             $activity = $activities[($i - 1) % count($activities)];
             
             // Tambahkan nomor seri angkatan/batch
             $batchNumber = (int) (($i - 1) / count($activities) + 1);
             $titleId = $activity['title_id'] . ' - Angkatan ' . $batchNumber;
             $titleEn = $activity['title_en'] . ' - Batch ' . $batchNumber;
 
             // Tentukan tanggal mulai secara acak dalam kurun waktu 3 bulan ke depan
             $startDate = $fakerId->dateTimeBetween('now', '+3 months');
             
             // Tanggal selesai adalah 1 sampai 5 hari setelah tanggal mulai
             $endDate = (clone $startDate)->modify('+' . rand(1, 5) . ' days');
 
             Schedule::create([
                 'title_id'       => $titleId,
                 'title_en'       => $titleEn,
                 'description_id' => $activity['desc_id'] . ' ' . $fakerId->paragraph(1),
                 'description_en' => $activity['desc_en'] . ' ' . $fakerEn->paragraph(1),
                 'start_date'     => $startDate,
                 'end_date'       => $endDate,
             ]);
         }
     }
 }
