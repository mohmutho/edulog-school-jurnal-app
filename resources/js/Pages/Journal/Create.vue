<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Textarea } from '@/Components/ui/textarea';
import { ChevronLeft, Save } from 'lucide-vue-next';
import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/Components/ui/select';
import Swal from 'sweetalert2';

const props = defineProps({
    jadwal: Object,
    siswa: Array,
});

// Opsi Presensi dengan Warna Khusus untuk UX yang maksimal
const attendanceOptions = [
    { value: 'hadir', display: 'H', label: 'Hadir', color: 'peer-checked:bg-green-600 peer-checked:text-white peer-checked:border-green-600 text-green-700 bg-green-50 border-green-200' },
    { value: 'izin', display: 'I', label: 'Izin', color: 'peer-checked:bg-blue-600 peer-checked:text-white peer-checked:border-blue-600 text-blue-700 bg-blue-50 border-blue-200' },
    { value: 'sakit', display: 'S', label: 'Sakit', color: 'peer-checked:bg-yellow-500 peer-checked:text-white peer-checked:border-yellow-500 text-yellow-700 bg-yellow-50 border-yellow-200' },
    { value: 'alpa', display: 'A', label: 'Alpha', color: 'peer-checked:bg-red-600 peer-checked:text-white peer-checked:border-red-600 text-red-700 bg-red-50 border-red-200' },
];

// Inisialisasi Presensi Dinamis dari Database
// Membuat object default: { '1': 'H', '2': 'H', ... } berdasarkan ID siswa
const initialAttendances = {};
if (props.siswa) {
    props.siswa.forEach(student => {
        initialAttendances[student.id] = 'hadir'; 
    });
}

// State Form Inertia
const form = useForm({
    kegiatan: '',
    materi: '',
    catatan: '',
    attendances: initialAttendances // Bind langsung object yang sudah kita buat
});

// Membuat variabel classInfo secara dinamis dari database
const classInfo = computed(() => {
    // Format tanggal hari ini
    const now = new Date();
    const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    
    // Potong detik dari database (07:00:00 menjadi 07:00)
    const startTime = props.jadwal?.start_time ? props.jadwal.start_time.substring(0, 5) : '';
    const endTime = props.jadwal?.end_time ? props.jadwal.end_time.substring(0, 5) : '';

    return {
        subject: props.jadwal?.subject?.name || '-',
        className: props.jadwal?.classroom?.name || '-',
        date: now.toLocaleDateString('id-ID', dateOptions),
        time: `${startTime} - ${endTime}`
    };
});

const submitForm = () => {
    form.post(route('journal.store', props.jadwal.id), {
        // Ketika berhasil disimpan ke database
        onSuccess: () => {
            Swal.fire({
                title: 'Berhasil!',
                text: 'Presensi dan Jurnal Berhasil di Simpan.',
                icon: 'success',
                confirmButtonText: 'OK',
                confirmButtonColor: '#2563eb', // Warna biru serasi dengan tombol agan
                allowOutsideClick: false
            }).then((result) => {
                // Catatan: Karena di Controller kita mereturn redirect()->route('dashboard'),
                // Inertia akan otomatis memindahkan halaman ke Dashboard di latar belakang.
                // SweetAlert akan tetap muncul dengan cantik di atasnya!
            });
        },
        // Ketika ada error validasi atau sistem
        onError: (errors) => {
            // Mengambil pesan error pertama dari object errors, atau pesan default
            const errorMessage = Object.values(errors)[0] || 'Terjadi kesalahan saat menyimpan data.';
            
            Swal.fire({
                title: 'Gagal Menyimpan!',
                text: errorMessage,
                icon: 'error',
                confirmButtonText: 'OK',
                confirmButtonColor: '#ef4444' // Warna merah
            });
        }
    });
};
</script>

<template>
    <Head title="Isi Jurnal & Presensi" />

    <AuthenticatedLayout>
        
        <div class="mb-6 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <Link :href="route('dashboard')">
                    <Button variant="outline" size="icon" class="rounded-full">
                        <ChevronLeft class="h-5 w-5" />
                    </Button>
                </Link>
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Isi Jurnal & Presensi</h1>
                    <p class="text-sm text-slate-500">{{ classInfo.subject }} • Kelas {{ classInfo.className }}</p>
                </div>
            </div>
            
            <div class="hidden md:block text-right">
                <p class="text-sm font-medium text-slate-900">{{ classInfo.date }}</p>
                <p class="text-xs text-slate-500">{{ classInfo.time }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            <div class="lg:col-span-7 space-y-6">
                <Card>
                    <CardHeader>
                        <div class="flex items-center justify-between">
                            <div>
                                <CardTitle>Presensi Siswa</CardTitle>
                                <CardDescription>Total: {{ siswa?.length || 0 }} Siswa</CardDescription>
                            </div>
                            <div class="flex space-x-2 text-xs font-medium text-slate-500">
                                <span>H: Hadir</span>
                                <span>I: Izin</span>
                                <span>S: Sakit</span>
                                <span>A: Alpha</span>
                            </div>
                        </div>
                    </CardHeader>
                    
                    <CardContent class="space-y-3 max-h-140 overflow-y-auto pr-4">
                        <div v-for="(student, index) in siswa" :key="student.id" class="flex flex-col sm:flex-row sm:items-center justify-between p-3 rounded-lg border border-slate-100 bg-slate-50/50 hover:bg-slate-50 transition-colors gap-3">
                            
                            <div class="flex items-center space-x-3">
                                <div class="w-8 text-center text-sm font-semibold text-slate-400">{{ index + 1 }}</div>
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">{{ student.name }}</p>
                                    <p class="text-xs text-slate-500">NIS: {{ student.nis }} • {{ student.gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                                </div>
                            </div>

                            <div class="flex space-x-2 ml-11 sm:ml-0">
                                <label v-for="opt in attendanceOptions" :key="opt.value" class="cursor-pointer">
                                    <input type="radio" :name="'attendance_' + student.id" :value="opt.value" v-model="form.attendances[student.id]" class="peer sr-only">
                                    <div :class="['w-9 h-9 flex items-center justify-center rounded-md text-sm font-bold border transition-all duration-200', opt.color]">
                                        {{ opt.display }}
                                    </div>
                                </label>
                            </div>

                        </div>
                        
                        <div v-if="siswa.length === 0" class="text-center py-8 text-slate-500">
                            Belum ada siswa terdaftar di kelas ini.
                        </div>
                    </CardContent>
                </Card>
            </div>

            <div class="lg:col-span-5 space-y-6">
                <Card>
                    <CardHeader>
                        <CardTitle>Jurnal Mengajar</CardTitle>
                        <CardDescription>Isi kegiatan dan materi hari ini.</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-5">
                        
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-slate-700">Jenis Kegiatan <span class="text-red-500">*</span></label>
                            <Select v-model="form.kegiatan">
                                <SelectTrigger class="w-full">
                                    <SelectValue placeholder="Pilih jenis kegiatan..." />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectGroup>
                                        <SelectItem value="Penjelasan Materi">Penjelasan Materi</SelectItem>
                                        <SelectItem value="Praktikum">Praktikum</SelectItem>
                                        <SelectItem value="Berdiskusi">Berdiskusi</SelectItem>
                                        <SelectItem value="Ulangan Harian">Ulangan Harian</SelectItem>
                                        <SelectItem value="Pembelajaran Mandiri">Pembelajaran Mandiri</SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-slate-700">Rincian Kegiatan <span class="text-slate-400 font-normal">(Opsional)</span></label>
                            <Textarea 
                                v-model="form.materi" 
                                placeholder="Contoh: Menjelaskan pengulangan For dan While..." 
                                class="min-h-20"
                            />
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-slate-700">Catatan Khusus <span class="text-slate-400 font-normal">(Opsional)</span></label>
                            <Textarea 
                                v-model="form.catatan" 
                                placeholder="Contoh: Proyektor mati, pertemuan depan butuh lab komputer." 
                                class="min-h-20"
                            />
                        </div>

                    </CardContent>
                </Card>
            </div>

        </div>

        <div class="mt-8 flex lg:justify-end mb-10">
            <Button @click="submitForm" class="w-full lg:w-auto bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 h-auto text-base font-semibold shadow-lg shadow-blue-500/30">
                <Save class="w-5 h-5 mr-2" />
                Simpan Jurnal & Presensi
            </Button>
        </div>

    </AuthenticatedLayout>
</template>