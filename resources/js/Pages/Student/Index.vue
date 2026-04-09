<script setup>
import { ref, watch, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Card, CardContent } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select';
import { Search, Users, GraduationCap } from 'lucide-vue-next';

const props = defineProps({
    students: Object,
    classrooms: Array,
    filters: Object,
});

// State untuk filter dan search
const search = ref(props.filters.search || '');
const selectedClass = ref(props.filters.classroom_id || 'all');

// Total siswa dari paginasi
const totalStudents = computed(() => props.students.total);

// Watcher: Tembak request ke backend secara reaktif setiap ada ketikan atau perubahan dropdown
watch([search, selectedClass], ([newSearch, newClass]) => {
    router.get(route('student.index'), {
        search: newSearch,
        classroom_id: newClass === 'all' ? null : newClass
    }, {
        preserveState: true,
        replace: true
    });
});
</script>

<template>
    <Head title="Data Siswa" />

    <AuthenticatedLayout>
        <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Data Siswa</h1>
                <p class="text-slate-500 mt-1">Daftar siswa di seluruh kelas yang Anda ampu.</p>
            </div>
            
            <div class="flex items-center gap-4 bg-blue-50 px-4 py-3 rounded-xl border border-blue-100">
                <Users class="w-6 h-6 text-blue-600" />
                <div>
                    <p class="text-[10px] sm:text-xs text-blue-600 font-bold uppercase tracking-wider">Total Siswa</p>
                    <p class="text-lg sm:text-xl font-black text-blue-900">{{ totalStudents }} Orang</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 mb-6">
            <div class="md:col-span-8 relative">
                <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
                <Input v-model="search" placeholder="Cari nama atau NISN siswa..." class="pl-10 bg-white" />
            </div>
            <div class="md:col-span-4">
                <Select v-model="selectedClass">
                    <SelectTrigger class="bg-white">
                        <SelectValue placeholder="Semua Kelas" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="all">Semua Kelas</SelectItem>
                        <SelectItem v-for="cls in classrooms" :key="cls.id" :value="cls.id.toString()">
                            Kelas {{ cls.name }}
                        </SelectItem>
                    </SelectContent>
                </Select>
            </div>
        </div>

        <Card class="border-t-4 border-t-blue-500 overflow-hidden">
            <CardContent class="p-0">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left whitespace-nowrap">
                        <thead class="bg-slate-50 text-slate-600 font-semibold border-b">
                            <tr>
                                <th class="px-6 py-4 w-16 text-center">No</th>
                                <th class="px-6 py-4">Nama Lengkap</th>
                                <th class="px-6 py-4">NISN</th>
                                <th class="px-6 py-4">Kelas</th>
                                <th class="px-6 py-4">L/P</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-if="students.data.length === 0">
                                <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <Users class="w-12 h-12 text-slate-300 mb-3" />
                                        <p class="text-lg font-medium text-slate-900">Siswa tidak ditemukan</p>
                                        <p class="text-sm mt-1">Coba sesuaikan kata kunci pencarian atau filter kelas.</p>
                                    </div>
                                </td>
                            </tr>
                            
                            <tr v-for="(student, index) in students.data" :key="student.id" class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-6 py-4 text-center text-slate-400">
                                    {{ (students.current_page - 1) * students.per_page + index + 1 }}
                                </td>
                                <td class="px-6 py-4 font-bold text-slate-900">
                                    {{ student.name }}
                                </td>
                                <td class="px-6 py-4 font-mono text-slate-600">
                                    {{ student.nisn || '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    <span v-if="student.classrooms && student.classrooms.length > 0" 
                                        class="px-2.5 py-1 rounded-md bg-blue-50 text-blue-700 font-bold text-xs border border-blue-100">
                                        {{ student.classrooms[0].name }}
                                    </span>
                                    <span v-else class="text-xs text-slate-400 italic">Belum ada kelas</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span :class="student.gender === 'L' ? 'text-blue-600 bg-blue-50 border-blue-200' : 'text-pink-600 bg-pink-50 border-pink-200'" class="px-2.5 py-1 rounded-full text-xs font-bold border">
                                        {{ student.gender === 'L' ? 'Laki-laki' : 'Perempuan' }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="students.links && students.data.length > 0" class="border-t px-6 py-4 bg-slate-50/50 flex items-center justify-end gap-1 overflow-x-auto">
                    <template v-for="(link, index) in students.links" :key="index">
                        <Link 
                            v-if="link.url"
                            :href="link.url"
                            class="px-3 py-1.5 text-sm rounded-md border transition-colors whitespace-nowrap"
                            :class="link.active ? 'bg-blue-600 text-white border-blue-600 shadow-sm' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-100'"
                            v-html="link.label"
                        ></Link>
                        <span 
                            v-else 
                            class="px-3 py-1.5 text-sm rounded-md border bg-slate-50 text-slate-400 border-slate-200 cursor-not-allowed whitespace-nowrap"
                            v-html="link.label"
                        ></span>
                    </template>
                </div>
            </CardContent>
        </Card>
    </AuthenticatedLayout>
</template>