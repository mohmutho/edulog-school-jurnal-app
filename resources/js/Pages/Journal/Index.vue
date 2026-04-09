<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Card, CardContent } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Eye, Pencil, SearchX, Download, Calendar, X } from 'lucide-vue-next';

const props = defineProps({
    journals: Object, // Sekarang berisi Object yang dikelompokkan per bulan
});

// --- STATE UNTUK MODAL EXPORT PDF ---
const showExportModal = ref(false);
const exportData = ref({
    start_date: '',
    end_date: ''
});

const handleExportPDF = () => {
    if (!exportData.value.start_date || !exportData.value.end_date) {
        alert("Mohon isi tanggal mulai dan tanggal akhir terlebih dahulu!");
        return;
    }

    if (exportData.value.start_date > exportData.value.end_date) {
        alert("Tanggal mulai tidak boleh melebihi tanggal akhir!");
        return;
    }

    // Buka tab baru untuk download PDF
    const url = route('jurnal.export', {
        start_date: exportData.value.start_date,
        end_date: exportData.value.end_date
    });
    
    window.open(url, '_blank');
    showExportModal.value = false; // Tutup modal
};

// --- HELPER FUNCTIONS ---
const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', { 
        weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' 
    });
};

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
        
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Riwayat Jurnal & Presensi</h1>
                <p class="text-sm text-slate-500">Arsip seluruh kegiatan belajar mengajar Anda.</p>
            </div>
            
            <Button @click="showExportModal = true" class="bg-indigo-600 hover:bg-indigo-700 w-full sm:w-auto shadow-sm">
                <Download class="w-4 h-4 mr-2" />
                Export Jurnal (PDF)
            </Button>
        </div>

        <div class="space-y-10">
            
            <div v-if="Object.keys(journals).length === 0" class="flex flex-col items-center justify-center py-20 text-center px-4 bg-white rounded-xl border border-slate-200">
                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mb-4">
                    <SearchX class="w-8 h-8 text-slate-400" />
                </div>
                <h3 class="text-lg font-bold text-slate-900">Belum Ada Jurnal</h3>
                <p class="text-slate-500 max-w-sm mt-1">Anda belum memiliki riwayat mengajar. Mulailah mengajar untuk mengisi jurnal.</p>
            </div>

            <div v-for="(entries, monthYear) in journals" :key="monthYear" class="animate-in fade-in duration-500">
                
                <div class="flex items-center gap-4 mb-5">
                    <div class="h-px bg-slate-300 flex-1"></div>
                    <h2 class="text-sm sm:text-base font-bold text-slate-700 bg-slate-100 px-5 py-1.5 rounded-full border border-slate-200 tracking-wide shadow-sm uppercase">
                        Bulan {{ monthYear }}
                    </h2>
                    <div class="h-px bg-slate-300 flex-1"></div>
                </div>

                <Card class="border-slate-200 shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-600">
                            <thead class="bg-slate-50 text-slate-700 font-semibold border-b border-slate-200">
                                <tr>
                                    <th class="px-4 py-3 sm:px-6 w-48">Tanggal</th>
                                    <th class="px-4 py-3 sm:px-6">Kelas & Mapel</th>
                                    <th class="px-4 py-3 sm:px-6 hidden md:table-cell">Kegiatan & Materi</th>
                                    <th class="px-4 py-3 sm:px-6 text-center">Kehadiran</th>
                                    <th class="px-4 py-3 sm:px-6 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr v-for="jurnal in entries" :key="jurnal.id" class="hover:bg-slate-50/50 transition-colors">
                                    
                                    <td class="px-4 py-4 sm:px-6 whitespace-nowrap">
                                        <span class="font-medium text-slate-900">{{ formatDate(jurnal.date) }}</span>
                                        <div v-if="jurnal.is_locked" class="mt-1">
                                            <span class="px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider text-teal-700 bg-teal-100 rounded border border-teal-200">
                                                Auto-Fill
                                            </span>
                                        </div>
                                    </td>
                                    
                                    <td class="px-4 py-4 sm:px-6">
                                        <p class="font-bold text-blue-600">{{ jurnal.schedule.subject.name }}</p>
                                        <p class="text-xs font-semibold text-slate-600 mt-0.5">Kelas {{ jurnal.schedule.classroom.name }}</p>
                                        <p class="text-[11px] text-slate-400 mt-0.5">{{ jurnal.schedule.start_time.substring(0,5) }} - {{ jurnal.schedule.end_time.substring(0,5) }} WIB</p>
                                    </td>
                                    
                                    <td class="px-4 py-4 sm:px-6 hidden md:table-cell max-w-xs">
                                        <p class="font-semibold text-slate-800 truncate" :title="jurnal.activity_type">{{ jurnal.activity_type }}</p>
                                        <p class="text-xs text-slate-500 truncate mt-0.5" :title="jurnal.description">{{ jurnal.description || 'Materi tidak diisi' }}</p>
                                    </td>
                                    
                                    <td class="px-4 py-4 sm:px-6 text-center">
                                        <div class="flex flex-col items-center justify-center gap-1.5">
                                            
                                            <span class="inline-flex items-center justify-center px-2.5 py-1 bg-emerald-50 text-emerald-700 font-bold text-xs rounded border border-emerald-200 shadow-sm w-full max-w-27.5">
                                                Hadir: {{ jurnal.hadir_count }} / {{ jurnal.total_students }}
                                            </span>
                                            
                                            <span v-if="jurnal.tidak_hadir_count > 0" class="text-[10px] font-bold text-rose-600 bg-rose-50 px-2 py-0.5 rounded border border-rose-100">
                                                {{ jurnal.tidak_hadir_count }} Tidak Hadir
                                            </span>
                                            <span v-else class="text-[10px] font-medium text-slate-400 italic">
                                                Nihil (Lengkap)
                                            </span>

                                        </div>
                                    </td>
                                    
                                    <td class="px-4 py-4 sm:px-6 text-right space-x-2 whitespace-nowrap">
                                        <Link :href="route('journal.show', jurnal.schedule_id)">
                                            <Button variant="outline" size="sm" class="h-8 px-2 text-blue-600 border-blue-200 hover:bg-blue-50 hover:text-blue-700">
                                                <Eye class="w-4 h-4 md:mr-1" /> <span class="hidden md:inline">Detail</span>
                                            </Button>
                                        </Link>
                                        
                                        <Link v-if="canEdit(jurnal.date)" :href="route('journal.edit', jurnal.id)">
                                            <Button variant="outline" size="sm" class="h-8 px-2 text-orange-600 border-orange-200 hover:bg-orange-50 hover:text-orange-700">
                                                <Pencil class="w-4 h-4 md:mr-1" /> <span class="hidden md:inline">Edit</span>
                                            </Button>
                                        </Link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </Card>
            </div>
        </div>

        <div v-if="showExportModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm transition-opacity">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden animate-in zoom-in-95 duration-200">
                
                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <h3 class="font-bold text-slate-800 flex items-center gap-2">
                        <Calendar class="w-5 h-5 text-indigo-600" /> Pilih Rentang Tanggal
                    </h3>
                    <button @click="showExportModal = false" class="text-slate-400 hover:text-slate-600 p-1 rounded-md hover:bg-slate-100 transition-colors">
                        <X class="w-5 h-5" />
                    </button>
                </div>

                <div class="p-6 space-y-5">
                    <p class="text-sm text-slate-500">
                        Pilih tanggal awal dan akhir jurnal yang ingin Anda unduh untuk keperluan E-Kinerja atau Arsip.
                    </p>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1 uppercase tracking-wider">Tanggal Mulai</label>
                            <input type="date" v-model="exportData.start_date" class="w-full border-slate-200 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1 uppercase tracking-wider">Tanggal Akhir</label>
                            <input type="date" v-model="exportData.end_date" class="w-full border-slate-200 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                    <Button variant="outline" @click="showExportModal = false">Batal</Button>
                    <Button @click="handleExportPDF" class="bg-indigo-600 hover:bg-indigo-700">
                        <Download class="w-4 h-4 mr-2" /> Unduh PDF
                    </Button>
                </div>
                
            </div>
        </div>

    </AuthenticatedLayout>
</template>