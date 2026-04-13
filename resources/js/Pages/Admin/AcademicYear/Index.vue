<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { CalendarDays, CheckCircle, PlusCircle, AlertCircle } from 'lucide-vue-next';

// Menerima data dari Controller
const props = defineProps({
    academicYears: Array,
});

// Form untuk tambah Tahun Ajaran
const form = useForm({
    name: '',
    semester: 'ganjil',
});

const submitForm = () => {
    form.post(route('kurikulum.academic-years.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};

// Fungsi untuk mengaktifkan Tahun Ajaran
const activateYear = (id) => {
    if(confirm('Yakin ingin mengaktifkan tahun ajaran ini? Semua data operasional akan beralih ke tahun ajaran ini.')) {
        useForm({}).patch(route('kurikulum.academic-years.activate', id), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Manajemen Tahun Ajaran" />

    <AuthenticatedLayout>
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Tahun Ajaran</h1>
                <p class="text-sm text-slate-500">Kelola siklus tahun ajaran dan semester aktif.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
            
            <Card class="border-slate-200 shadow-sm lg:col-span-1">
                <CardHeader class="bg-slate-50/50 border-b border-slate-100 pb-4">
                    <CardTitle class="text-lg flex items-center text-slate-800">
                        <PlusCircle class="w-5 h-5 mr-2 text-indigo-600" /> Tambah Baru
                    </CardTitle>
                </CardHeader>
                <CardContent class="pt-6">
                    <form @submit.prevent="submitForm" class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1 uppercase tracking-wider">Tahun Ajaran</label>
                            <input type="text" v-model="form.name" placeholder="Contoh: 2026/2027" class="w-full border-slate-200 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1 uppercase tracking-wider">Semester</label>
                            <select v-model="form.semester" class="w-full border-slate-200 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="ganjil">Ganjil</option>
                                <option value="genap">Genap</option>
                            </select>
                        </div>

                        <Button type="submit" :disabled="form.processing" class="w-full bg-indigo-600 hover:bg-indigo-700 mt-2">
                            Simpan Data
                        </Button>
                    </form>
                </CardContent>
            </Card>

            <Card class="border-slate-200 shadow-sm lg:col-span-2">
                <CardHeader class="bg-slate-50/50 border-b border-slate-100 pb-4">
                    <CardTitle class="text-lg flex items-center text-slate-800">
                        <CalendarDays class="w-5 h-5 mr-2 text-slate-600" /> Daftar Tahun Ajaran
                    </CardTitle>
                </CardHeader>
                <CardContent class="pt-0 px-0">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-slate-50 border-b border-slate-200 text-slate-500">
                                <tr>
                                    <th class="px-6 py-4 font-semibold">Tahun Ajaran</th>
                                    <th class="px-6 py-4 font-semibold text-center">Semester</th>
                                    <th class="px-6 py-4 font-semibold text-center">Status</th>
                                    <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="year in academicYears" :key="year.id" class="border-b border-slate-100 hover:bg-slate-50/50 transition-colors">
                                    <td class="px-6 py-4 font-medium text-slate-900">{{ year.name }}</td>
                                    <td class="px-6 py-4 text-center capitalize">{{ year.semester }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <span v-if="year.is_active" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                            <CheckCircle class="w-3 h-3 mr-1" /> Aktif
                                        </span>
                                        <span v-else class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600">
                                            Non-Aktif
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <Button v-if="!year.is_active" @click="activateYear(year.id)" variant="outline" size="sm" class="text-indigo-600 border-indigo-200 hover:bg-indigo-50">
                                            Jadikan Aktif
                                        </Button>
                                    </td>
                                </tr>
                                <tr v-if="academicYears.length === 0">
                                    <td colspan="4" class="px-6 py-8 text-center text-slate-500">
                                        <AlertCircle class="w-8 h-8 mx-auto mb-2 text-slate-300" />
                                        Belum ada data tahun ajaran.
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