<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed, ref, onMounted, onBeforeUnmount } from 'vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';

const props = defineProps({
    auth: Object,
    jadwalMingguan: Array,
    userName: String,
    userJabatan: String 
});

const greetingName = computed(() => {
    const user = props.auth.user;
    const title = user.gender === 'L' ? 'Bapak' : (user.gender === 'P' ? 'Ibu' : '');
    return `${title} ${user.name}`.trim();
});

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

const getScheduleStatus = (timeString, isFilled, isAutoFilled, scheduleDay) => {
    const [start, end] = timeString.split(' - ');
    const now = new Date();
    
    let currentDay = now.getDay();
    currentDay = currentDay === 0 ? 7 : currentDay; 

    const currentTotalMinutes = now.getHours() * 60 + now.getMinutes();
    const [startH, startM] = start.split(':').map(Number);
    const startTotalMinutes = startH * 60 + startM;
    const [endH, endM] = end.split(':').map(Number);
    const endTotalMinutes = endH * 60 + endM;
    const batasAkhirMinutes = 18 * 60; // Jam 18:00

    let badgeLabel = '';
    let badgeColor = '';
    let actionCode = '';
    let isActiveCard = false;

    // A. JIKA JADWAL DI HARI MENDATANG
    if (scheduleDay > currentDay) {
        badgeLabel = 'Akan Datang';
        badgeColor = 'bg-slate-100 text-slate-500';
        actionCode = 'waiting'; 
    } 
    // B. JIKA JADWAL DI HARI LALU
    else if (scheduleDay < currentDay) {
        if (isFilled && isAutoFilled) {
            badgeLabel = 'Selesai (Auto-Fill)';
            badgeColor = 'bg-teal-100 text-teal-700';
        } else {
            badgeLabel = 'Selesai';
            badgeColor = 'bg-green-100 text-green-700';
        }
        actionCode = 'view';
    } 
    // C. JIKA JADWAL ADALAH HARI INI
    else {
        isActiveCard = currentTotalMinutes >= startTotalMinutes && currentTotalMinutes <= endTotalMinutes;

        // Label Warna Card Hari Ini
        if (currentTotalMinutes < startTotalMinutes) {
            badgeLabel = 'Belum Dimulai';
            badgeColor = 'bg-orange-100 text-orange-700';
        } else if (isActiveCard) {
            badgeLabel = 'Sedang Berjalan';
            badgeColor = 'bg-blue-100 text-blue-700';
        } else {
            if (isFilled && isAutoFilled) {
                badgeLabel = 'Selesai (Auto-Fill)';
                badgeColor = 'bg-teal-100 text-teal-700';
            } else {
                badgeLabel = 'Selesai';
                badgeColor = 'bg-green-100 text-green-700';
            }
        }

        // Logika Tombol Aksi Hari Ini
        if (isFilled) {
            actionCode = 'view'; // Berubah jadi Lihat Presensi
        } else if (currentTotalMinutes < startTotalMinutes) {
            actionCode = 'waiting'; // Belum Waktunya
        } else if (currentTotalMinutes < batasAkhirMinutes) {
            actionCode = 'fill'; // Bisa diisi (Isi Jurnal & Presensi)
        } else {
            // Jika lewat jam 18:00 dan backend kebetulan telat merespons
            actionCode = 'view'; 
        }
    }

    return { badgeLabel, badgeColor, actionCode, isActiveCard };
};

const dynamicSchedules = computed(() => {
    // Memancing re-render tiap detik saat currentTime berubah
    const trigger = currentTime.value; 
    
    if (!props.jadwalMingguan || !Array.isArray(props.jadwalMingguan)) return [];
    
    return props.jadwalMingguan.map(schedule => {
        const timeString = `${schedule.waktuMulai || '00:00'} - ${schedule.waktuSelesai || '00:00'}`;
        // PENTING: Sisipkan schedule.is_auto_filled ke dalam fungsi
        const statusInfo = getScheduleStatus(
            timeString, 
            schedule.is_journal_filled, 
            schedule.is_auto_filled, 
            schedule.hari_angka
        );
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
            <p class="text-slate-500 mt-2">Berikut adalah jadwal mengajar Anda untuk minggu ini.</p>
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
                        {{ schedule.hari }}, {{ schedule.time }} WIB
                    </div>
                    
                    <Button v-if="schedule.actionCode === 'fill'" as-child class="w-full bg-blue-600 hover:bg-blue-700">
                        <Link :href="route('journal.create', schedule.id)">
                            Isi Jurnal & Presensi
                        </Link>
                    </Button>
                    
                    <Button v-else-if="schedule.actionCode === 'view'" variant="outline" class="w-full text-green-700 border-green-200 hover:bg-green-50">
                        <Link :href="route('journal.show', schedule.id)">
                            Lihat Presensi
                        </Link>
                    </Button>
                    
                    <Button v-else variant="secondary" disabled class="w-full bg-slate-100 text-slate-400">
                        Belum Waktunya
                    </Button>

                </CardContent>
            </Card>
        </div>
    </AuthenticatedLayout>
</template>