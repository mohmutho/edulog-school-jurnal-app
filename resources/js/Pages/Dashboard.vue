<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed, ref, onMounted, onBeforeUnmount } from 'vue';

// Import komponen Shadcn yang sudah diinstall
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';

// Menangkap data 'auth' dari routes/web.php
const props = defineProps({
    auth: Object,
    jadwalHariIni: Array,
    userName: String,
    userJabatan: String 
});

// Membuat logika sapaan dinamis berdasarkan gender dari backend
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
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');
    
    currentTime.value = `${hours}:${minutes}:${seconds}`;
};

onMounted(() => {
    updateTime();
    timeTimer = setInterval(updateTime, 1000);
});

onBeforeUnmount(() => {
    if (timeTimer) clearInterval(timeTimer);
});

// Logika untuk menentukan status berdasarkan waktu saat ini
const getScheduleStatus = (timeString) => {
    const [start, end] = timeString.split(' - ');
    
    const now = new Date();
    const currentTotalMinutes = now.getHours() * 60 + now.getMinutes();

    const [startH, startM] = start.split(':').map(Number);
    const startTotalMinutes = startH * 60 + startM;

    const [endH, endM] = end.split(':').map(Number);
    const endTotalMinutes = endH * 60 + endM;

    // Batas akhir waktu pengisian (Jam 18:00)
    const batasAkhirMinutes = 18 * 60;

    let badgeLabel = '';
    let badgeColor = '';

    // A. Logika Label Status (Sesuai jam kelas sebenarnya)
    if (currentTotalMinutes < startTotalMinutes) {
        badgeLabel = 'Belum Dimulai';
        badgeColor = 'bg-slate-100 text-slate-600';
    } else if (currentTotalMinutes >= startTotalMinutes && currentTotalMinutes <= endTotalMinutes) {
        badgeLabel = 'Sedang Berjalan';
        badgeColor = 'bg-blue-100 text-blue-700';
    } else {
        badgeLabel = 'Selesai';
        badgeColor = 'bg-green-100 text-green-700';
    }

    // B. Logika Tombol Aksi (Batas kelonggaran s/d jam 18:00)
    let actionCode = '';
    if (currentTotalMinutes < startTotalMinutes) {
        actionCode = 'waiting'; // Sebelum kelas mulai -> Disabled
    } else if (currentTotalMinutes >= startTotalMinutes && currentTotalMinutes < batasAkhirMinutes) {
        actionCode = 'fill'; // Sedang jalan ATAU sudah selesai tapi belum jam 18:00 -> Isi Jurnal
    } else {
        actionCode = 'view'; // Lewat jam 18:00 -> Hanya Lihat Presensi
    }

    return { 
        badgeLabel, 
        badgeColor, 
        actionCode,
        isActiveCard: currentTotalMinutes >= startTotalMinutes && currentTotalMinutes <= endTotalMinutes
    };
};

// Jadwal Dinamis yang akan bereaksi setiap detik
const dynamicSchedules = computed(() => {
    const trigger = currentTime.value; 

    // JARING PENGAMAN: Jika data dari Laravel belum siap atau kosong, kembalikan array kosong
    if (!props.jadwalHariIni || !Array.isArray(props.jadwalHariIni)) {
        return [];
    }
    
    return props.jadwalHariIni.map(schedule => {
        const mulai = schedule.waktuMulai || '00:00';
        const selesai = schedule.waktuSelesai || '00:00';
        const timeString = `${mulai} - ${selesai}`;
        const statusInfo = getScheduleStatus(timeString);
        return {
            id: schedule.id,
            subject: schedule.mataPelajaran,
            classroom: schedule.kelas,
            time: timeString,
            hari: schedule.hari,
            badgeLabel: statusInfo.badgeLabel,
            badgeColor: statusInfo.badgeColor,
            actionCode: statusInfo.actionCode,
            isActiveCard: statusInfo.isActiveCard
        };
    });
});
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
            <Card v-for="schedule in dynamicSchedules" :key="schedule.id" :class="[schedule.isActiveCard ? 'border-blue-500 shadow-md ring-1 ring-blue-500' : 'border-slate-200']">
                <CardHeader class="pb-3">
                    <div class="flex justify-between items-start">
                        <CardTitle class="text-xl">{{ schedule.subject }}</CardTitle>
                        
                        <span :class="schedule.badgeColor" class="px-2.5 py-0.5 rounded-full text-xs font-semibold">
                            {{ schedule.badgeLabel }}
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
                    
                    <Button v-if="schedule.actionCode === 'fill'" as-child class="w-full bg-blue-600 hover:bg-blue-700">
                        <Link href="/jurnal/create">
                            Isi Jurnal & Presensi
                        </Link>
                    </Button>
                    
                    <Button v-else-if="schedule.actionCode === 'view'" variant="outline" class="w-full text-green-700 border-green-200 hover:bg-green-50">
                        Lihat Presensi
                    </Button>
                    
                    <Button v-else variant="secondary" disabled class="w-full bg-slate-100 text-slate-400">
                        Belum Waktunya
                    </Button>

                </CardContent>
            </Card>
        </div>
    </AuthenticatedLayout>
</template>
