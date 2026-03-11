<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { computed, ref, onMounted, onBeforeUnmount } from 'vue';

// Import komponen Shadcn yang sudah diinstall
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';

// 1. Menangkap data 'auth' dari routes/web.php
const props = defineProps({
    auth: Object
});

// 2. Membuat logika sapaan dinamis berdasarkan gender dari backend
const greetingName = computed(() => {
    const user = props.auth.user;
    const title = user.gender === 'L' ? 'Bapak' : (user.gender === 'P' ? 'Ibu' : '');
    return `${title} ${user.name}`.trim();
});

// LOGIKA JAM, MENIT, DETIK
const currentTime = ref('');
let timeTimer = null;

const updateTime = () => {
    const now = new Date();
    // Menggunakan padStart agar format selalu 2 digit (contoh: 09:05:02)
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');
    
    currentTime.value = `${hours}:${minutes}:${seconds}`;
};

onMounted(() => {
    updateTime();
    timeTimer = setInterval(updateTime, 1000); // Berdetak setiap 1 detik
});

onBeforeUnmount(() => {
    if (timeTimer) clearInterval(timeTimer);
});

//Data dummy jadwal hari ini (nanti diganti dengan data dari backend)
const todaySchedules = [
    { 
        id: 1, 
        subject: 'Informatika', 
        classroom: 'XI-7', 
        time: '07:00 - 09:15', 
        status: 'Sedang Berjalan',
        isActive: true
    },
    { 
        id: 2, 
        subject: 'Informatika', 
        classroom: 'XI-8', 
        time: '09:15 - 11:45', 
        status: 'Belum Dimulai',
        isActive: false
    }
];
</script>

<template>
    <Head title="Dashboard Guru" />

    <AuthenticatedLayout>
        
        <div class="mb-8">
            <div class="flex items-center justify-between gap-4">
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 line-clamp-1">
                    Selamat datang, {{ greetingName }}!
                </h1>
                <div class="text-2xl sm:text-3xl font-mono font-bold text-blue-600 tracking-wider shrink-0">
                    {{ currentTime }}
                </div>
            </div>
            <p class="text-slate-500 mt-2">Berikut adalah jadwal mengajar Anda untuk hari ini. Silakan pilih kelas yang sedang aktif.</p>
        </div>

        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            <Card v-for="schedule in todaySchedules" :key="schedule.id" :class="[schedule.isActive ? 'border-blue-500 shadow-md ring-1 ring-blue-500' : 'border-slate-200']">
                <CardHeader class="pb-3">
                    <div class="flex justify-between items-start">
                        <CardTitle class="text-xl">{{ schedule.subject }}</CardTitle>
                        <span :class="[schedule.isActive ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-600', 'px-2.5 py-0.5 rounded-full text-xs font-semibold']">
                            {{ schedule.status }}
                        </span>
                    </div>
                    <CardDescription class="text-lg font-medium text-slate-700 mt-1">
                        Kelas: {{ schedule.classroom }}
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="flex items-center text-sm text-slate-500 mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ schedule.time }} WIB
                    </div>
                    
                    <Button :variant="schedule.isActive ? 'default' : 'outline'" class="w-full" :class="schedule.isActive ? 'bg-blue-600 hover:bg-blue-700' : ''">
                        {{ schedule.isActive ? 'Isi Jurnal & Presensi' : 'Lihat Detail' }}
                    </Button>
                </CardContent>
            </Card>
        </div>

    </AuthenticatedLayout>
</template>
