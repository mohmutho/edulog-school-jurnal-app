<script setup>
import { ref, watch, computed } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Users, MoveRight, Search, AlertTriangle, Filter, UserMinus } from 'lucide-vue-next';
import debounce from 'lodash/debounce';

const props = defineProps({
    students: Array,
    classrooms: Array,
    activeYear: Object,
    filters: Object
});

const searchQuery = ref(props.filters.search || '');
const filterClassroom = ref(props.filters.classroom_id);

const selectedStudents = ref([]);
const targetClassroom = ref('');
const targetStatus = ref('');

const currentClassroomName = computed(() => {
    if (filterClassroom.value === 'unassigned') return 'Siswa Tanpa Kelas';
    const selected = props.classrooms.find(c => c.id == filterClassroom.value);
    return selected ? `Data Siswa ${selected.name}` : 'Data Siswa';
});

const targetClassroomOptions = computed(() => {
    if (filterClassroom.value === 'unassigned') return props.classrooms;
    const currentClass = props.classrooms.find(c => c.id == filterClassroom.value);
    if (!currentClass) return props.classrooms;

    const prefixAngkatan = currentClass.name.split(/[- ]/)[0];
    return props.classrooms.filter(c => 
        c.name.startsWith(prefixAngkatan) && c.id != filterClassroom.value
    );
});

watch([searchQuery, filterClassroom], debounce(([newSearch, newClassroom], [oldSearch, oldClassroom]) => {
    if (newClassroom !== oldClassroom) {
        selectedStudents.value = [];
        targetClassroom.value = '';
        targetStatus.value = '';
    }
    router.get(
        route('kurikulum.students.index'),
        { search: newSearch, classroom_id: newClassroom },
        { preserveState: true, replace: true, preserveScroll: true }
    );
}, 300));

const toggleAll = (e) => {
    if (e.target.checked) {
        selectedStudents.value = props.students.map(s => s.id);
    } else {
        selectedStudents.value = [];
    }
};

const formAssign = useForm({
    student_ids: [],
    classroom_id: '',
    academic_year_id: props.activeYear?.id
});

// Form baru untuk mengubah status
const formStatus = useForm({
    student_ids: [],
    status: ''
});

const handleAssign = () => {
    if (!targetClassroom.value) return alert('Silakan pilih kelas tujuan terlebih dahulu!');
    if (!props.activeYear) return alert('Tahun ajaran aktif belum diatur.');
    
    formAssign.student_ids = selectedStudents.value;
    formAssign.classroom_id = targetClassroom.value;
    
    formAssign.post(route('kurikulum.students.assign'), {
        preserveScroll: true,
        onSuccess: () => {
            selectedStudents.value = [];
            targetClassroom.value = '';
        }
    });
};

const handleUpdateStatus = () => {
    if (!targetStatus.value) return alert('Silakan pilih status (Mutasi/Keluar) terlebih dahulu!');
    
    const konfirmasi = confirm(`Yakin ingin mengubah status ${selectedStudents.value.length} siswa menjadi ${targetStatus.value.toUpperCase()}? Data mereka akan disembunyikan dari halaman ini dan absen harian guru.`);
    
    if (konfirmasi) {
        formStatus.student_ids = selectedStudents.value;
        formStatus.status = targetStatus.value;
        
        formStatus.post(route('kurikulum.students.status'), {
            preserveScroll: true,
            onSuccess: () => {
                selectedStudents.value = [];
                targetStatus.value = '';
            }
        });
    }
};
</script>

<template>
    <Head title="Mutasi Siswa Harian" />

    <AuthenticatedLayout>
        <div class="mb-8 flex flex-col gap-2">
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Mutasi & Pindah Kelas</h1>
            <div v-if="activeYear" class="inline-flex items-center px-3 py-1.5 rounded-md text-sm font-medium bg-indigo-50 text-indigo-700 w-fit border border-indigo-100">
                Tahun Ajaran Aktif: <span class="font-bold ml-1">{{ activeYear.name }} ({{ activeYear.semester }})</span>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6">
            <Card class="border-slate-200 shadow-sm">
                <CardHeader class="bg-slate-50/50 border-b border-slate-100 pb-4 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <CardTitle class="text-lg flex items-center text-slate-800 whitespace-nowrap">
                        <Users class="w-5 h-5 mr-2 text-indigo-600" /> Data Siswa
                    </CardTitle>
                    
                    <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                        <div class="relative w-full sm:w-48">
                            <Filter class="absolute left-3 top-2.5 h-4 w-4 text-slate-400" />
                            <select v-model="filterClassroom" class="pl-9 w-full border-slate-200 rounded-lg text-sm font-bold text-indigo-900 focus:ring-indigo-500 bg-white">
                                <option v-for="cls in classrooms" :key="cls.id" :value="cls.id">{{ cls.name }}</option>
                                <option value="unassigned" class="text-rose-600">-- Belum Ada Kelas --</option>
                            </select>
                        </div>
                        <div class="relative w-full sm:w-64">
                            <Search class="absolute left-3 top-2.5 h-4 w-4 text-slate-400" />
                            <input v-model="searchQuery" type="text" placeholder="Cari dalam kelas ini..." class="pl-9 w-full border-slate-200 rounded-lg text-sm focus:ring-indigo-500">
                        </div>
                    </div>
                </CardHeader>
                
                <CardContent class="pt-6">
                    <div class="mb-6 border-l-4 border-indigo-600 pl-4 py-1">
                        <h1 class="text-xl font-extrabold text-slate-800 uppercase tracking-tight">
                            {{ currentClassroomName }}
                        </h1>
                        <p class="text-xs text-slate-500 italic">
                            Menampilkan daftar siswa yang terdaftar pada kelas ini di semester aktif.
                        </p>
                    </div>

                    <div v-if="selectedStudents.length > 0" class="bg-indigo-50 border border-indigo-100 p-4 rounded-xl mb-6 flex flex-col xl:flex-row items-start xl:items-center justify-between gap-5 shadow-sm transition-all animate-in fade-in slide-in-from-top-2">
                        <div class="text-sm font-medium flex items-center w-full xl:w-auto text-indigo-900">
                            <span class="bg-indigo-600 text-white px-2.5 py-1 rounded-md mr-3 font-bold shadow-sm">{{ selectedStudents.length }}</span> 
                            <span class="whitespace-nowrap tracking-wide font-semibold">Siswa Terpilih</span>
                        </div>
                        
                        <div class="flex flex-col sm:flex-row items-center gap-4 w-full xl:w-auto">
                            
                            <div class="flex flex-col sm:flex-row items-center gap-2 w-full sm:w-auto sm:border-r border-indigo-200 pb-4 sm:pb-0 sm:pr-5 border-b sm:border-b-0">
                                <select v-model="targetClassroom" class="text-sm text-slate-700 border-indigo-200 bg-white rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 w-full sm:w-48 shadow-sm">
                                    <option value="" disabled>-- Pilih Kelas Tujuan --</option>
                                    <option v-for="cls in targetClassroomOptions" :key="cls.id" :value="cls.id">{{ cls.name }}</option>
                                </select>
                                <Button @click="handleAssign" :disabled="formAssign.processing" class="bg-indigo-600 hover:bg-indigo-700 text-white w-full sm:w-auto shrink-0 font-medium shadow-sm transition-colors">
                                    <MoveRight class="w-4 h-4 mr-2" /> Pindah Kelas
                                </Button>
                            </div>

                            <div class="flex flex-col sm:flex-row items-center gap-2 w-full sm:w-auto">
                                <select v-model="targetStatus" class="text-sm text-slate-700 border-rose-200 bg-white rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500 w-full sm:w-40 shadow-sm">
                                    <option value="" disabled>-- Ubah Status --</option>
                                    <option value="mutasi" class="font-medium text-amber-700">Mutasi (Pindah)</option>
                                    <option value="keluar" class="font-medium text-rose-700">Keluar / Berhenti</option>
                                </select>
                                <Button @click="handleUpdateStatus" :disabled="formStatus.processing" class="bg-white border border-rose-200 text-rose-600 hover:bg-rose-50 hover:text-rose-700 w-full sm:w-auto shrink-0 font-medium shadow-sm transition-colors">
                                    <UserMinus class="w-4 h-4 mr-2" /> Terapkan Status
                                </Button>
                            </div>

                        </div>
                    </div>
                    
                    <div class="overflow-x-auto border border-slate-200 rounded-lg mb-4">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-slate-50 border-b border-slate-200 text-slate-600">
                                <tr>
                                    <th class="px-4 py-3 w-10 text-center">
                                        <input type="checkbox" @change="toggleAll" :checked="selectedStudents.length === students.length && students.length > 0" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                                    </th>
                                    <th class="px-6 py-3 font-semibold">NISN</th>
                                    <th class="px-6 py-3 font-semibold">Nama Siswa</th>
                                    <th class="px-6 py-3 font-semibold">Status Saat Ini</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="student in students" :key="student.id" class="border-b border-slate-100 hover:bg-slate-50/80 transition-colors">
                                    <td class="px-4 py-3 text-center">
                                        <input type="checkbox" v-model="selectedStudents" :value="student.id" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                                    </td>
                                    <td class="px-6 py-3 font-mono text-slate-500">{{ student.nisn ?? '-' }}</td>
                                    <td class="px-6 py-3 font-medium text-slate-900">{{ student.name }}</td>
                                    <td class="px-6 py-3">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-100 text-emerald-800">
                                            {{ student.status.toUpperCase() }}
                                        </span>
                                    </td>
                                </tr>
                                <tr v-if="students.length === 0">
                                    <td colspan="4" class="px-6 py-12 text-center text-slate-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <AlertTriangle class="w-8 h-8 mb-3 text-slate-300" />
                                            <span>Tidak ada data siswa aktif di kelas ini.</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AuthenticatedLayout>
</template>