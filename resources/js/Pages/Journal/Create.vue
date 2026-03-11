<script setup>
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Textarea } from '@/Components/ui/textarea';
import { ChevronLeft, Save } from 'lucide-vue-next';

// Dummy Data Kelas (Nanti dari backend)
const classInfo = {
    subject: 'Informatika',
    className: 'XI-8',
    time: '09:15 - 11:45 WIB',
    date: 'Rabu, 11 Maret 2026'
};

// Dummy Data Siswa
const students = [
    { id: 1, nis: '1001', name: 'Ahmad Budi Santoso', gender: 'L' },
    { id: 2, nis: '1002', name: 'Bagus Prakoso', gender: 'L' },
    { id: 3, nis: '1003', name: 'Dian Sastrowardoyo', gender: 'P' },
    { id: 4, nis: '1004', name: 'Eko Patrio', gender: 'L' },
    { id: 5, nis: '1005', name: 'Siti Aminah', gender: 'P' },
];

// Opsi Presensi dengan Warna Khusus untuk UX yang maksimal
const attendanceOptions = [
    { code: 'H', label: 'Hadir', color: 'peer-checked:bg-green-600 peer-checked:text-white peer-checked:border-green-600 text-green-700 bg-green-50 border-green-200' },
    { code: 'I', label: 'Izin', color: 'peer-checked:bg-blue-600 peer-checked:text-white peer-checked:border-blue-600 text-blue-700 bg-blue-50 border-blue-200' },
    { code: 'S', label: 'Sakit', color: 'peer-checked:bg-yellow-500 peer-checked:text-white peer-checked:border-yellow-500 text-yellow-700 bg-yellow-50 border-yellow-200' },
    { code: 'A', label: 'Alpha', color: 'peer-checked:bg-red-600 peer-checked:text-white peer-checked:border-red-600 text-red-700 bg-red-50 border-red-200' },
];

// State Form
const form = ref({
    materi: '',
    catatan: '',
    attendances: {}
});

// Otomatis set semua siswa menjadi 'Hadir' (H) saat halaman dimuat
students.forEach(student => {
    form.value.attendances[student.id] = 'H';
});

const submitForm = () => {
    // Simulasi submit data
    console.log('Data yang akan dikirim ke Backend:', form.value);
    alert('Simulasi: Jurnal & Presensi berhasil disimpan!');
};
</script>

<template>
    <Head title="Isi Jurnal & Presensi" />

    <AuthenticatedLayout>
        
        <div class="mb-6 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <Link href="/dashboard">
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

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            
            <div class="lg:col-span-5 space-y-6">
                <Card>
                    <CardHeader>
                        <CardTitle>Jurnal Mengajar</CardTitle>
                        <CardDescription>Isi materi yang diajarkan hari ini.</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-slate-700">Materi Pembelajaran <span class="text-red-500">*</span></label>
                            <Textarea 
                                v-model="form.materi" 
                                placeholder="Contoh: Pengenalan HTML & CSS Dasar..." 
                                class="min-h-30"
                            />
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-slate-700">Catatan Khusus (Opsional)</label>
                            <Textarea 
                                v-model="form.catatan" 
                                placeholder="Contoh: Proyektor di kelas redup, butuh perbaikan." 
                                class="min-h-20"
                            />
                        </div>
                    </CardContent>
                </Card>
            </div>

            <div class="lg:col-span-7">
                <Card class="h-full flex flex-col">
                    <CardHeader>
                        <div class="flex items-center justify-between">
                            <div>
                                <CardTitle>Presensi Siswa</CardTitle>
                                <CardDescription>Total: {{ students.length }} Siswa</CardDescription>
                            </div>
                            <div class="flex space-x-2 text-xs font-medium text-slate-500">
                                <span>H: Hadir</span>
                                <span>I: Izin</span>
                                <span>S: Sakit</span>
                                <span>A: Alpha</span>
                            </div>
                        </div>
                    </CardHeader>
                    
                    <CardContent class="flex-1 overflow-y-auto pr-2 space-y-3">
                        <div v-for="(student, index) in students" :key="student.id" class="flex flex-col sm:flex-row sm:items-center justify-between p-3 rounded-lg border border-slate-100 bg-slate-50/50 hover:bg-slate-50 transition-colors gap-3">
                            
                            <div class="flex items-center space-x-3">
                                <div class="w-8 text-center text-sm font-semibold text-slate-400">{{ index + 1 }}</div>
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">{{ student.name }}</p>
                                    <p class="text-xs text-slate-500">NIS: {{ student.nis }} • {{ student.gender }}</p>
                                </div>
                            </div>

                            <div class="flex space-x-2 ml-11 sm:ml-0">
                                <label v-for="opt in attendanceOptions" :key="opt.code" class="cursor-pointer">
                                    <input 
                                        type="radio" 
                                        :name="'attendance_' + student.id" 
                                        :value="opt.code" 
                                        v-model="form.attendances[student.id]" 
                                        class="peer sr-only"
                                    >
                                    <div :class="['w-9 h-9 flex items-center justify-center rounded-md text-sm font-bold border transition-all duration-200', opt.color]">
                                        {{ opt.code }}
                                    </div>
                                </label>
                            </div>

                        </div>
                    </CardContent>
                </Card>
            </div>

        </div>

        <div class="mt-6 flex justify-end">
            <Button @click="submitForm" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-2.5 h-auto text-base font-semibold shadow-lg shadow-blue-500/30">
                <Save class="w-5 h-5 mr-2" />
                Simpan Jurnal & Presensi
            </Button>
        </div>

    </AuthenticatedLayout>
</template>