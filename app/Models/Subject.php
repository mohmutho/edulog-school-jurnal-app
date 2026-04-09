<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subject extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $appends = ['abbreviation'];

    public function getAbbreviationAttribute()
    {
        $kamusSingkatan = [
            // Kategori 1: Standar (Bisa 3 huruf, tapi kita definisikan agar konsisten)
            'Fisika' => 'Fis',
            'Biologi' => 'Bio',
            'Kimia' => 'Kim',
            'Geografi' => 'Geo',
            'Sosiologi' => 'Sos',
            'Informatika' => 'Inf',
            'Sejarah' => 'Sej',
            'Matematika' => 'Mat',
            'Ekonomi' => 'Eko',

            // Kategori 2: Singkatan Khusus
            'Matematika Tingkat Lanjut' => 'MTL',
            'Bimbingan Konseling' => 'BK',
            'Pendidikan Agama' => 'PAg',
            'Koding dan Kecerdasan Artifisial' => 'KKA', // (Saya perbaiki ketikan "Keerdasan" ya Pak)
            'Bahasa Inggris' => 'Ingr',
            'Bahasa Indonesia' => 'B Ind',
            'Bahasa Jawa' => 'B Jw',
            'Bahasa Inggris Tingkat Lanjut' => 'BITL',
            'Prakarya dan Kewirausahaan' => 'PKWU',
            'Seni Budaya' => 'SB',
            'Pendidikan Pancasila' => 'PP',
            'Pendidikan Pancasila dan Kewarganegaraan' => 'PPKn',
            'Pendidikan Jasmani, Olahraga, dan Kesehatan' => 'PJOK',
        ];

        // Jika nama mapel ada di kamus, keluarkan singkatannya.
        // Jika tidak ada, otomatis ambil 3 huruf pertama dan jadikan Huruf Kapital Depannya.
        return $kamusSingkatan[$this->name] ?? ucfirst(substr($this->name, 0, 3));
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}
