<script setup>
import { ref, computed } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { 
    CalendarDays, Plus, Trash2, Clock, 
    User, BookOpen, AlertCircle, Info, Filter 
} from 'lucide-vue-next';

const props = defineProps({
    schedules: Array,
    classrooms: Array,
    subjects: Array,
    teachers: Array,
    activeYear: Object,
    filters: Object
});

// State untuk UI
const showAddModal = ref(false);
const filterClassroom = ref(props.filters.classroom_id || '');
const filterTeacher = ref(props.filters.user_id || ''); // Filter Guru

// Konvensi Hari (Tanpa Sabtu)
const days = [
    { id: 1, name: 'Senin' },
    { id: 2, name: 'Selasa' },
    { id: 3, name: 'Rabu' },
    { id: 4, name: 'Kamis' },
    { id: 5, name: 'Jumat' },
];

// Form Handling
const form = useForm({
    academic_year_id: props.activeYear?.id,
    classroom_id: filterClassroom.value,
    subject_id: '',
    user_id: '', // ID Guru
    day_of_week: '',
    start_time: '',
    end_time: '',
});

const submit = () => {
    form.post(route('kurikulum.schedules.store'), {
        onSuccess: () => {
            showAddModal.value = false;
            form.reset('subject_id', 'user_id', 'start_time', 'end_time');
        },
    });
};

const deleteSchedule = (id) => {
    if (confirm('Apakah Anda yakin ingin menghapus jadwal ini?')) {
        router.delete(route('kurikulum.schedules.destroy', id));
    }
};

// Filter Otomatis saat Dropdown Kelas/Guru berubah
const handleFilterChange = () => {
    router.get(route('kurikulum.schedules.index'), { 
        classroom_id: filterClassroom.value,
        user_id: filterTeacher.value
    }, {
        preserveState: true,
        replace: true
    });
};

// Fungsi Cerdas: Konversi Jam Mulai/Selesai ke "Jam Ke-X"
const getJamKe = (start, end) => {
    const timeMap = [
        { jam: 1, start: '07:00', end: '07:45' },
        { jam: 2, start: '07:45', end: '08:30' },
        { jam: 3, start: '08:30', end: '09:15' },
        { jam: 4, start: '09:15', end: '10:00' },
        { jam: 5, start: '10:15', end: '11:00' },
        { jam: 6, start: '11:00', end: '11:45' },
        { jam: 7, start: '12:30', end: '13:15' },
        { jam: 8, start: '13:15', end: '14:00' },
        { jam: 9, start: '14:00', end: '14:45' },
        { jam: 10, start: '14:45', end: '15:30' }
    ];

    const s = start.substring(0, 5);
    const e = end.substring(0, 5);

    const startJam = timeMap.find(t => t.start === s)?.jam;
    const endJam = timeMap.find(t => t.end === e)?.jam;

    if (startJam && endJam) {
        return startJam === endJam ? `Jam ${startJam}` : `Jam ${startJam} - ${endJam}`;
    }
    return 'Waktu Kustom';
};
</script>

<template>
    <Head title="Plotting Jadwal" />

    <AuthenticatedLayout>
        <div class="mb-8 flex flex-col xl:flex-row xl:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Plotting Jadwal Pelajaran</h1>
                <p class="text-sm text-slate-500" v-if="activeYear">
                    Tahun Ajaran: {{ activeYear.name }} - {{ activeYear.semester }}
                </p>
            </div>
            
            <div class="flex flex-col sm:flex-row items-center gap-3">
                <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-lg p-1.5 shadow-sm w-full sm:w-auto">
                    <Filter class="w-4 h-4 ml-2 text-slate-400" />
                    
                    <select v-model="filterTeacher" @change="handleFilterChange" class="border-none text-sm font-semibold text-slate-700 bg-transparent focus:ring-0 cursor-pointer">
                        <option value="">Semua Guru</option>
                        <option v-for="teacher in teachers" :key="teacher.id" :value="teacher.id">{{ teacher.name }}</option>
                    </select>
                    
                    <div class="h-5 w-px bg-slate-300 mx-1"></div>

                    <select v-model="filterClassroom" @change="handleFilterChange" class="border-none text-sm font-semibold text-slate-700 bg-transparent focus:ring-0 cursor-pointer">
                        <option value="">Semua Kelas</option>
                        <option v-for="cls in classrooms" :key="cls.id" :value="cls.id">{{ cls.name }}</option>
                    </select>
                </div>

                <Button @click="showAddModal = !showAddModal" class="bg-indigo-600 hover:bg-indigo-700 w-full sm:w-auto">
                    <Plus class="w-4 h-4 mr-2" /> Tambah Jadwal
                </Button>
            </div>
        </div>

        <Card v-if="showAddModal" class="mb-8 border-indigo-200 bg-indigo-50/30">
            <CardHeader>
                <CardTitle class="text-md flex items-center">
                    <CalendarDays class="w-5 h-5 mr-2 text-indigo-600" /> Input Jadwal Baru
                </CardTitle>
            </CardHeader>
            <CardContent>
                <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-600 uppercase">Kelas</label>
                        <select v-model="form.classroom_id" class="w-full rounded-md border-slate-200 text-sm">
                            <option value="">-- Pilih Kelas --</option>
                            <option v-for="cls in classrooms" :key="cls.id" :value="cls.id">{{ cls.name }}</option>
                        </select>
                        <div v-if="form.errors.classroom_id" class="text-xs text-rose-600 font-medium">{{ form.errors.classroom_id }}</div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-600 uppercase">Guru Pengajar</label>
                        <select v-model="form.user_id" class="w-full rounded-md border-slate-200 text-sm">
                            <option value="">-- Pilih Guru --</option>
                            <option v-for="teacher in teachers" :key="teacher.id" :value="teacher.id">{{ teacher.name }}</option>
                        </select>
                        <div v-if="form.errors.user_id" class="text-xs text-rose-600 font-medium">{{ form.errors.user_id }}</div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-600 uppercase">Mata Pelajaran</label>
                        <select v-model="form.subject_id" class="w-full rounded-md border-slate-200 text-sm">
                            <option value="">-- Pilih Mapel --</option>
                            <option v-for="sub in subjects" :key="sub.id" :value="sub.id">{{ sub.name }}</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-3 gap-2 col-span-1 md:col-span-1">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-600 uppercase">Hari</label>
                            <select v-model="form.day_of_week" class="w-full rounded-md border-slate-200 text-sm p-2">
                                <option v-for="day in days" :key="day.id" :value="day.id">{{ day.name }}</option>
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-600 uppercase">Mulai</label>
                            <input type="time" v-model="form.start_time" class="w-full rounded-md border-slate-200 text-sm p-2" />
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-600 uppercase">Selesai</label>
                            <input type="time" v-model="form.end_time" class="w-full rounded-md border-slate-200 text-sm p-2" />
                        </div>
                    </div>

                    <div class="flex items-end pb-1">
                        <Button type="submit" :disabled="form.processing" class="w-full bg-indigo-600">
                            Simpan Jadwal
                        </Button>
                    </div>
                </form>
            </CardContent>
        </Card>

        <div class="space-y-6">
            <div v-for="day in days" :key="day.id">
                <div class="flex items-center mb-3">
                    <div class="bg-indigo-600 text-white px-4 py-1 rounded-full text-xs font-bold tracking-widest uppercase">
                        {{ day.name }}
                    </div>
                    <div class="ml-4 h-px bg-slate-200 grow"></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    <template v-for="schedule in schedules" :key="schedule.id">
                        <Card v-if="schedule.day_of_week == day.id" class="hover:shadow-md transition-all border-l-4 border-l-indigo-500 overflow-hidden group relative">
                            <CardContent class="p-4 relative">
                                <button @click="deleteSchedule(schedule.id)" class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 p-1.5 text-rose-400 hover:text-rose-600 transition-all z-10 bg-white/80 rounded-full">
                                    <Trash2 class="w-4 h-4" />
                                </button>

                                <div class="absolute top-0 right-0 bg-indigo-100 text-indigo-800 text-[10px] font-bold px-2 py-1 rounded-bl-lg z-0">
                                    {{ getJamKe(schedule.start_time, schedule.end_time) }}
                                </div>

                                <div class="flex items-start gap-3 mt-1">
                                    <div class="bg-slate-50 p-2 rounded-lg text-slate-400">
                                        <Clock class="w-5 h-5" />
                                    </div>
                                    <div class="grow">
                                        <div class="text-sm font-extrabold text-indigo-900 leading-tight">
                                            {{ schedule.start_time.substring(0,5) }} - {{ schedule.end_time.substring(0,5) }}
                                        </div>
                                        <div class="text-xs text-slate-500 font-medium mb-2 uppercase tracking-tighter">
                                            Kelas {{ schedule.classroom.name }}
                                        </div>
                                        
                                        <div class="flex items-center text-sm font-semibold text-slate-800 mb-1 line-clamp-1">
                                            <BookOpen class="w-3.5 h-3.5 mr-1.5 text-slate-400 shrink-0" />
                                            {{ schedule.subject.name }}
                                        </div>
                                        <div class="flex items-center text-xs text-slate-600 line-clamp-1">
                                            <User class="w-3.5 h-3.5 mr-1.5 text-slate-400 shrink-0" />
                                            {{ schedule.user.name }}
                                        </div>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </template>
                    
                    <div v-if="!schedules.some(s => s.day_of_week == day.id)" class="col-span-full py-4 text-center border-2 border-dashed border-slate-100 rounded-xl">
                        <p class="text-xs text-slate-400 font-medium">Belum ada jadwal di hari ini</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-12 p-4 bg-amber-50 rounded-xl border border-amber-100 flex items-start gap-3">
            <Info class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" />
            <div>
                <p class="text-sm font-bold text-amber-900 mb-1">Panduan Jam Pelajaran SMAN 1 Rembang</p>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-x-6 gap-y-1 text-xs text-amber-700 font-medium leading-relaxed">
                    <span>Jam 1: 07.00 - 07.45</span>
                    <span>Jam 2: 07.45 - 08.30</span>
                    <span>Jam 3: 08.30 - 09.15</span>
                    <span>Jam 4: 09.15 - 10.00</span>
                    <span class="font-bold text-indigo-700 italic">Istirahat 1: 10.00 - 10.15</span>
                    <span>Jam 5: 10.15 - 11.00</span>
                    <span>Jam 6: 11.00 - 11.45</span>
                    <span class="font-bold text-indigo-700 italic">Istirahat 2: 11.45 - 12.30</span>
                    <span>Jam 7: 12.30 - 13.15</span>
                    <span>Jam 8: 13.15 - 14.00</span>
                    <span>Jam 9: 14.00 - 14.45</span>
                    <span>Jam 10: 14.45 - 15.30</span>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>