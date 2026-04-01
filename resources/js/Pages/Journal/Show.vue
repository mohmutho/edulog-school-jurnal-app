<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { ChevronLeft, CheckCircle2, Pencil } from 'lucide-vue-next';

const props = defineProps({
    jadwal: Object,
    jurnal: Object,
    presensi: Array,
});

// Format Header Kelas Dinamis
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

// Logika Gembok Mingguan di sisi UI
const canEdit = computed(() => {
    const today = new Date();
    // JS getDay(): 0 = Minggu, 1 = Senin. Kita cari selisih ke hari Senin.
    const day = today.getDay();
    const diffToMonday = day === 0 ? -6 : 1 - day; 
    
    // Set awal minggu (Senin 00:00:00)
    const startOfWeek = new Date(today);
    startOfWeek.setDate(today.getDate() + diffToMonday);
    startOfWeek.setHours(0, 0, 0, 0);

    // Set akhir minggu (Minggu 23:59:59)
    const endOfWeek = new Date(startOfWeek);
    endOfWeek.setDate(startOfWeek.getDate() + 6);
    endOfWeek.setHours(23, 59, 59, 999);

    const journalDate = new Date(props.jurnal.date);
    
    // Tombol hanya muncul jika tanggal jurnal berada di dalam minggu ini
    return journalDate >= startOfWeek && journalDate <= endOfWeek;
});

// Fungsi untuk mewarnai Badge Absensi sesuai status di database
const getStatusBadge = (status) => {
    switch(status) {
        case 'hadir': return 'bg-green-100 text-green-700 border-green-200';
        case 'izin': return 'bg-blue-100 text-blue-700 border-blue-200';
        case 'sakit': return 'bg-yellow-100 text-yellow-700 border-yellow-200';
        case 'alpa': return 'bg-red-100 text-red-700 border-red-200';
        default: return 'bg-slate-100 text-slate-700 border-slate-200';
    }
};

// Fungsi mengubah tulisan ke huruf kapital depan (hadir -> Hadir)
const capitalize = (str) => {
    return str.charAt(0).toUpperCase() + str.slice(1);
};

</script>

<template>
    <Head title="Detail Jurnal Kelas" />

    <AuthenticatedLayout>
        <div class="mb-6 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <Link href="/dashboard">
                    <Button variant="outline" size="icon" class="rounded-full cursor-pointer">
                        <ChevronLeft class="h-5 w-5" />
                    </Button>
                </Link>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Detail Jurnal Kelas</h1>
                        <Link v-if="canEdit" :href="route('journal.edit', jurnal.id)">
                            <Button variant="outline" size="sm" class="flex items-center gap-2 border-orange-200 text-orange-600 hover:bg-orange-50 cursor-pointer">
                                <Pencil class="w-4 h-4" />
                                Edit Jurnal
                            </Button>
                        </Link>
                        <CheckCircle2 class="w-5 h-5 text-teal-500" />
                    </div>
                    <p class="text-sm text-slate-500">{{ classInfo.subject }} • Kelas {{ classInfo.className }}</p>
                </div>
            </div>
            
            <div class="hidden md:block text-right">
                <p class="text-sm font-medium text-slate-900">{{ classInfo.date }}</p>
                <p class="text-xs text-slate-500">{{ classInfo.time }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start mb-10">
            
            <div class="lg:col-span-7 space-y-6">
                <Card>
                    <CardHeader>
                        <CardTitle>Rekap Presensi</CardTitle>
                        <CardDescription>Total: {{ presensi.length }} Siswa terdata.</CardDescription>
                    </CardHeader>
                    
                    <CardContent class="space-y-3 max-h-140 overflow-y-auto pr-4">
                        <div v-for="(item, index) in presensi" :key="item.id" class="flex flex-col sm:flex-row sm:items-center justify-between p-3 rounded-lg border border-slate-100 bg-slate-50/50 hover:bg-slate-50 transition-colors gap-3">
                            
                            <div class="flex items-center space-x-3">
                                <div class="w-8 text-center text-sm font-semibold text-slate-400">{{ index + 1 }}</div>
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">{{ item.student?.name }}</p>
                                    <p class="text-xs text-slate-500">NIS: {{ item.student?.nis }}</p>
                                </div>
                            </div>

                            <div class="flex space-x-2 ml-11 sm:ml-0">
                                <span :class="['px-3 py-1 text-xs font-bold rounded-full border', getStatusBadge(item.status)]">
                                    {{ capitalize(item.status) }}
                                </span>
                            </div>

                        </div>
                    </CardContent>
                </Card>
            </div>

            <div class="lg:col-span-5 space-y-6">
                <Card>
                    <CardHeader>
                        <div class="flex items-center justify-between">
                            <div>
                                <CardTitle>Jurnal Mengajar</CardTitle>
                                <CardDescription>Laporan kegiatan yang telah diisi.</CardDescription>
                            </div>
                            <span v-if="jurnal.is_locked" class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-teal-700 bg-teal-100 rounded-full">
                                Auto-Fill Sistem
                            </span>
                        </div>
                    </CardHeader>
                    <CardContent class="space-y-6">
                        
                        <div>
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Jenis Kegiatan</h4>
                            <p class="text-sm font-medium text-slate-800 bg-slate-100 p-3 rounded-md">{{ jurnal.activity_type }}</p>
                        </div>

                        <div>
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Rincian Materi</h4>
                            <p class="text-sm text-slate-700 bg-slate-50 border border-slate-100 p-3 rounded-md min-h-20 whitespace-pre-line">
                                {{ jurnal.description || 'Tidak ada rincian materi.' }}
                            </p>
                        </div>

                        <div>
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Catatan Khusus</h4>
                            <p class="text-sm text-slate-700 bg-orange-50/50 border border-orange-100 p-3 rounded-md min-h-20 whitespace-pre-line">
                                {{ jurnal.notes || 'Tidak ada catatan khusus.' }}
                            </p>
                        </div>

                    </CardContent>
                </Card>
            </div>

        </div>
    </AuthenticatedLayout>
</template>