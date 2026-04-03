<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Label } from '@/Components/ui/label';
import { Textarea } from '@/Components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select';
import { ChevronLeft, Save, AlertCircle } from 'lucide-vue-next';

const props = defineProps({
    jadwal: Object,
    jurnal: Object,
    presensi: Array,
});

// Format Info Kelas
const classInfo = computed(() => {
    const now = new Date(props.jurnal.date);
    const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    const startTime = props.jadwal?.start_time ? props.jadwal.start_time.substring(0, 5) : '';
    const endTime = props.jadwal?.end_time ? props.jadwal.end_time.substring(0, 5) : '';

    return {
        subject: props.jadwal?.subject?.name || '-',
        className: props.jadwal?.classroom?.name || '-',
        date: now.toLocaleDateString('id-ID', dateOptions),
        time: `${startTime} - ${endTime}`
    };
});

// 1. Siapkan data presensi lama untuk dimasukkan ke Form otomatis
const initialAttendances = {};
props.presensi.forEach(p => {
    initialAttendances[p.student_id] = p.status;
});

// 2. Inisialisasi Form Inertia dengan data lama (Pre-filled)
const form = useForm({
    activity_type: props.jurnal.activity_type,
    description: props.jurnal.description || '',
    notes: props.jurnal.notes || '',
    attendances: initialAttendances,
});

// 3. Fungsi Submit ke method PUT (Update)
const submitForm = () => {
    form.put(route('journal.update', props.jurnal.id));
};

const goBack = () => {
    window.history.back();
};

// Logika UI untuk mewarnai baris siswa sesuai status
const getRowClass = (status) => {
    switch (status) {
        case 'hadir': return 'bg-green-50/50 border-l-4 border-l-green-500';
        case 'izin': return 'bg-blue-50/50 border-l-4 border-l-blue-500';
        case 'sakit': return 'bg-yellow-50/50 border-l-4 border-l-yellow-500';
        case 'alpa': return 'bg-red-50/50 border-l-4 border-l-red-500';
        default: return 'bg-slate-50 border-l-4 border-l-slate-200';
    }
};
</script>

<template>
    <Head title="Edit Jurnal Kelas" />

    <AuthenticatedLayout>
        
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center space-x-4">
                <Button @click="goBack" type="button" variant="outline" size="icon" class="rounded-full hover:bg-slate-100">
                    <ChevronLeft class="h-5 w-5" />
                </Button>
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Edit Jurnal Kelas</h1>
                    <p class="text-sm text-slate-500">{{ classInfo.subject }} • Kelas {{ classInfo.className }}</p>
                </div>
            </div>
            
            <div class="text-left sm:text-right bg-orange-50 px-4 py-2 rounded-lg border border-orange-100 flex items-center gap-3">
                <AlertCircle class="w-5 h-5 text-orange-500 hidden sm:block" />
                <div>
                    <p class="text-sm font-medium text-orange-800">Mode Koreksi</p>
                    <p class="text-xs text-orange-600">{{ classInfo.date }} ({{ classInfo.time }})</p>
                </div>
            </div>
        </div>

        <form @submit.prevent="submitForm">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                
                <div class="lg:col-span-7 space-y-6">
                    <Card class="border-t-4 border-t-orange-400">
                        <CardHeader>
                            <CardTitle>Koreksi Presensi Siswa</CardTitle>
                            <CardDescription>Sesuaikan kembali kehadiran siswa jika ada perubahan.</CardDescription>
                        </CardHeader>
                        
                        <CardContent class="space-y-3 max-h-150 overflow-y-auto pr-4">
                            <div v-for="(item, index) in presensi" :key="item.student.id" 
                                 :class="['flex flex-col sm:flex-row sm:items-center justify-between p-3 rounded-r-lg border-y border-r transition-colors gap-3', getRowClass(form.attendances[item.student.id])]">
                                
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 text-center text-sm font-semibold text-slate-400">{{ index + 1 }}</div>
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900">{{ item.student.name }}</p>
                                        <p class="text-xs text-slate-500">NIS: {{ item.student.nis }}</p>
                                    </div>
                                </div>

                                <div class="flex space-x-2 ml-11 sm:ml-0 bg-white p-1 rounded-md shadow-sm border">
                                    <Label class="cursor-pointer flex items-center px-3 py-1.5 rounded transition-colors" 
                                           :class="form.attendances[item.student.id] === 'hadir' ? 'bg-green-100 text-green-800 font-bold' : 'hover:bg-slate-100 text-slate-600'">
                                        <input type="radio" :name="`attendance_${item.student.id}`" value="hadir" v-model="form.attendances[item.student.id]" class="sr-only"> Hadir
                                    </Label>
                                    <Label class="cursor-pointer flex items-center px-3 py-1.5 rounded transition-colors" 
                                           :class="form.attendances[item.student.id] === 'izin' ? 'bg-blue-100 text-blue-800 font-bold' : 'hover:bg-slate-100 text-slate-600'">
                                        <input type="radio" :name="`attendance_${item.student.id}`" value="izin" v-model="form.attendances[item.student.id]" class="sr-only"> Izin
                                    </Label>
                                    <Label class="cursor-pointer flex items-center px-3 py-1.5 rounded transition-colors" 
                                           :class="form.attendances[item.student.id] === 'sakit' ? 'bg-yellow-100 text-yellow-800 font-bold' : 'hover:bg-slate-100 text-slate-600'">
                                        <input type="radio" :name="`attendance_${item.student.id}`" value="sakit" v-model="form.attendances[item.student.id]" class="sr-only"> Sakit
                                    </Label>
                                    <Label class="cursor-pointer flex items-center px-3 py-1.5 rounded transition-colors" 
                                           :class="form.attendances[item.student.id] === 'alpa' ? 'bg-red-100 text-red-800 font-bold' : 'hover:bg-slate-100 text-slate-600'">
                                        <input type="radio" :name="`attendance_${item.student.id}`" value="alpa" v-model="form.attendances[item.student.id]" class="sr-only"> Alpa
                                    </Label>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <div class="lg:col-span-5 space-y-6">
                    <Card>
                        <CardHeader>
                            <CardTitle>Revisi Jurnal Mengajar</CardTitle>
                            <CardDescription>Perbarui laporan materi jika ada yang terlewat.</CardDescription>
                        </CardHeader>
                        <CardContent class="space-y-6">
                            
                            <div class="space-y-2">
                                <Label for="activity_type">Jenis Kegiatan <span class="text-red-500">*</span></Label>
                                <Select v-model="form.activity_type" required>
                                    <SelectTrigger class="w-full bg-slate-50">
                                        <SelectValue placeholder="Pilih Jenis Kegiatan" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="Penjelasan Materi">Penjelasan Materi</SelectItem>
                                        <SelectItem value="Praktek / Praktikum">Praktek / Praktikum</SelectItem>
                                        <SelectItem value="Ulangan / Ujian">Ulangan / Ujian</SelectItem>
                                        <SelectItem value="Diskusi Kelompok">Diskusi Kelompok</SelectItem>
                                        <SelectItem value="Pembelajaran Mandiri">Pembelajaran Mandiri</SelectItem>
                                        <SelectItem value="Lainnya">Lainnya</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>

                            <div class="space-y-2">
                                <Label for="description">Rincian Materi (Opsional)</Label>
                                <Textarea 
                                    id="description" 
                                    v-model="form.description"
                                    placeholder="Contoh: Mengulas kembali materi sebelumnya terkait..." 
                                    class="min-h-24 bg-slate-50"
                                />
                            </div>

                            <div class="space-y-2">
                                <Label for="notes">Catatan Khusus (Opsional)</Label>
                                <Textarea 
                                    id="notes" 
                                    v-model="form.notes"
                                    placeholder="Contoh: Siswa A sangat aktif, atau proyektor rusak." 
                                    class="min-h-24 bg-orange-50/30 border-orange-100"
                                />
                            </div>

                        </CardContent>
                    </Card>

                    <Button 
                        type="submit" 
                        class="w-full py-6 text-base font-bold bg-orange-600 hover:bg-orange-700 cursor-pointer disabled:cursor-not-allowed shadow-lg shadow-orange-500/30 transition-all gap-2"
                        :disabled="form.processing"
                    >
                        <Save class="w-5 h-5" />
                        {{ form.processing ? 'Menyimpan Perubahan...' : 'Simpan Perubahan' }}
                    </Button>
                </div>

            </div>
        </form>

    </AuthenticatedLayout>
</template>