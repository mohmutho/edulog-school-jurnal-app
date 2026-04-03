<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select';
import { Eye, Pencil, CalendarDays, SearchX } from 'lucide-vue-next';

const props = defineProps({
    journals: Object, // Berisi data pagination dari Laravel
    filters: Object,  // Berisi parameter filter saat ini
});

// --- LOGIKA FILTER BULAN ---
const selectedMonth = ref(props.filters.month || 'all');

const months = [
    { value: 'all', label: 'Semua Bulan' },
    { value: '1', label: 'Januari' },
    { value: '2', label: 'Februari' },
    { value: '3', label: 'Maret' },
    { value: '4', label: 'April' },
    { value: '5', label: 'Mei' },
    { value: '6', label: 'Juni' },
    { value: '7', label: 'Juli' },
    { value: '8', label: 'Agustus' },
    { value: '9', label: 'September' },
    { value: '10', label: 'Oktober' },
    { value: '11', label: 'November' },
    { value: '12', label: 'Desember' },
];

// Reaktif: Jika dropdown bulan diubah, otomatis tembak request ke backend
watch(selectedMonth, (value) => {
    router.get(route('journal.index'), 
        { month: value === 'all' ? null : value }, 
        { preserveState: true, replace: true }
    );
});

// --- HELPER FUNCTIONS ---
// Format Tanggal ke Bahasa Indonesia
const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', { 
        weekday: 'long', 
        day: 'numeric', 
        month: 'long', 
        year: 'numeric' 
    });
};

// Logika "Gembok Mingguan" (Sama seperti di halaman Show)
const canEdit = (dateString) => {
    const today = new Date();
    const day = today.getDay();
    const diffToMonday = day === 0 ? -6 : 1 - day; 
    
    const startOfWeek = new Date(today);
    startOfWeek.setDate(today.getDate() + diffToMonday);
    startOfWeek.setHours(0, 0, 0, 0);

    const endOfWeek = new Date(startOfWeek);
    endOfWeek.setDate(startOfWeek.getDate() + 6);
    endOfWeek.setHours(23, 59, 59, 999);

    const journalDate = new Date(dateString);
    return journalDate >= startOfWeek && journalDate <= endOfWeek;
};
</script>

<template>
    <Head title="Riwayat Jurnal & Presensi" />

    <AuthenticatedLayout>
        
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Riwayat Jurnal & Presensi</h1>
                <p class="text-sm text-slate-500">Arsip seluruh kegiatan belajar mengajar Anda.</p>
            </div>
            
            <div class="flex items-center gap-2">
                <CalendarDays class="w-5 h-5 text-slate-400" />
                <Select v-model="selectedMonth">
                    <SelectTrigger class="w-45 bg-white">
                        <SelectValue placeholder="Pilih Bulan" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem v-for="month in months" :key="month.value" :value="month.value">
                            {{ month.label }}
                        </SelectItem>
                    </SelectContent>
                </Select>
            </div>
        </div>

        <Card>
            <CardContent class="p-0 sm:p-6">
                
                <div v-if="journals.data.length === 0" class="flex flex-col items-center justify-center py-16 text-center px-4">
                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mb-4">
                        <SearchX class="w-8 h-8 text-slate-400" />
                    </div>
                    <h3 class="text-lg font-bold text-slate-900">Belum Ada Jurnal</h3>
                    <p class="text-slate-500 max-w-sm mt-1">Tidak ada catatan jurnal untuk filter bulan ini. Silakan pilih bulan lain atau mulai mengajar untuk mengisi jurnal.</p>
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-50 text-slate-700 font-semibold border-b">
                            <tr>
                                <th class="px-4 py-3 sm:px-6">Tanggal</th>
                                <th class="px-4 py-3 sm:px-6">Kelas & Mapel</th>
                                <th class="px-4 py-3 sm:px-6 hidden md:table-cell">Kegiatan</th>
                                <th class="px-4 py-3 sm:px-6">Status</th>
                                <th class="px-4 py-3 sm:px-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="jurnal in journals.data" :key="jurnal.id" class="hover:bg-slate-50/50 transition-colors">
                                
                                <td class="px-4 py-4 sm:px-6 whitespace-nowrap">
                                    <span class="font-medium text-slate-900">{{ formatDate(jurnal.date) }}</span>
                                </td>
                                
                                <td class="px-4 py-4 sm:px-6">
                                    <p class="font-bold text-slate-800">{{ jurnal.schedule.subject.name }}</p>
                                    <p class="text-xs text-slate-500">Kelas {{ jurnal.schedule.classroom.name }}</p>
                                </td>
                                
                                <td class="px-4 py-4 sm:px-6 hidden md:table-cell max-w-xs truncate">
                                    {{ jurnal.activity_type }}
                                </td>
                                
                                <td class="px-4 py-4 sm:px-6">
                                    <span v-if="jurnal.is_locked" class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-teal-700 bg-teal-100 rounded-full border border-teal-200">
                                        Auto-Fill
                                    </span>
                                    <span v-else class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-green-700 bg-green-100 rounded-full border border-green-200">
                                        Selesai
                                    </span>
                                </td>
                                
                                <td class="px-4 py-4 sm:px-6 text-right space-x-2 whitespace-nowrap">
                                    <Link :href="route('journal.show', jurnal.schedule_id)">
                                        <Button variant="outline" size="sm" class="h-8 px-2 text-blue-600 border-blue-200 hover:bg-blue-50 hover:text-blue-700">
                                            <Eye class="w-4 h-4 mr-1 hidden sm:block" /> Detail
                                        </Button>
                                    </Link>
                                    
                                    <Link v-if="canEdit(jurnal.date)" :href="route('journal.edit', jurnal.id)">
                                        <Button variant="outline" size="sm" class="h-8 px-2 text-orange-600 border-orange-200 hover:bg-orange-50 hover:text-orange-700">
                                            <Pencil class="w-4 h-4 mr-1 hidden sm:block" /> Edit
                                        </Button>
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </CardContent>
            
            <div v-if="journals.links && journals.data.length > 0" class="border-t px-6 py-4 bg-slate-50/50 rounded-b-xl flex items-center justify-between sm:justify-end gap-2 overflow-x-auto">
                <template v-for="(link, index) in journals.links" :key="index">
                    <Link 
                        v-if="link.url"
                        :href="link.url"
                        class="px-3 py-1 text-sm rounded-md border transition-colors whitespace-nowrap"
                        :class="link.active ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-100'"
                        v-html="link.label"
                    ></Link>
                    <span 
                        v-else 
                        class="px-3 py-1 text-sm rounded-md border bg-slate-50 text-slate-400 border-slate-200 cursor-not-allowed whitespace-nowrap"
                        v-html="link.label"
                    ></span>
                </template>
            </div>
        </Card>

    </AuthenticatedLayout>
</template>