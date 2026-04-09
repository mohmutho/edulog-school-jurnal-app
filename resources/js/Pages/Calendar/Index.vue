<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Card, CardContent } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { CalendarDays, Download, FileX } from 'lucide-vue-next';

const props = defineProps({
    calendar: Object
});
</script>

<template>
    <Head title="Kalender Pendidikan" />

    <AuthenticatedLayout>
        <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Kalender Akademik</h1>
                <p class="text-slate-500 mt-1">Jadwal kegiatan akademik dan hari libur sekolah.</p>
            </div>
            
            <div v-if="calendar" class="shrink-0">
                <a :href="calendar.file_url" download target="_blank">
                    <Button class="bg-blue-600 hover:bg-blue-700 shadow-sm w-full md:w-auto">
                        <Download class="w-4 h-4 mr-2" />
                        Download File
                    </Button>
                </a>
            </div>
        </div>

        <Card class="border-t-4 border-t-blue-500 overflow-hidden min-h-125">
            <CardContent class="p-6 h-full flex flex-col">
                
                <div v-if="!calendar" class="flex-1 flex flex-col items-center justify-center text-center py-20">
                    <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mb-4">
                        <FileX class="w-10 h-10 text-slate-400" />
                    </div>
                    <h2 class="text-2xl font-bold text-slate-700">Belum Ada Kalender</h2>
                    <p class="text-slate-500 mt-2 max-w-md">
                        Admin Kurikulum belum mengunggah Kalender Pendidikan untuk tahun ajaran ini. Silakan cek kembali nanti.
                    </p>
                </div>

                <div v-else class="flex-1 flex flex-col">
                    <div class="mb-6 border-b pb-4">
                        <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                            <CalendarDays class="w-5 h-5 text-blue-600" />
                            {{ calendar.title }}
                        </h2>
                    </div>

                    <div v-if="calendar.file_type === 'pdf'" class="w-full flex-1 min-h-150 rounded-lg border border-slate-200 overflow-hidden bg-slate-100">
                        <iframe 
                            :src="calendar.file_url" 
                            class="w-full h-full min-h-150" 
                            frameborder="0"
                            title="PDF Viewer"
                        ></iframe>
                    </div>

                    <div v-else-if="calendar.file_type === 'image'" class="w-full flex items-center justify-center bg-slate-50 rounded-lg border border-slate-200 p-4">
                        <img 
                            :src="calendar.file_url" 
                            alt="Kalender Pendidikan" 
                            class="max-w-full h-auto rounded shadow-sm"
                        >
                    </div>

                </div>

            </CardContent>
        </Card>
    </AuthenticatedLayout>
</template>