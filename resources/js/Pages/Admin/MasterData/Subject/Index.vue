<script setup>
import { ref, computed } from 'vue';
import { Head, useForm, router, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { BookOpen, Search, UploadCloud, Download, Plus, Edit, Trash2, Users } from 'lucide-vue-next';
import debounce from 'lodash/debounce';

const props = defineProps({
    subjects: Object,
    teachers: Array,
});

const searchQuery = ref('');

// Import Modal
const showImportModal = ref(false);
const formImport = useForm({
    file: null,
});

const handleImport = () => {
    formImport.post(route('kurikulum.subjects.import'), {
        preserveScroll: true,
        onSuccess: () => {
            showImportModal.value = false;
            formImport.reset();
        }
    });
};

const downloadTemplate = () => {
    const url = new URL(route('kurikulum.subjects.template'));
    url.protocol = window.location.protocol;
    window.location.href = url.toString();
};

// Form Modal (Tambah/Edit)
const showFormModal = ref(false);
const isEditing = ref(false);
const editingId = ref(null);

const formSubject = useForm({
    code: '',
    name: '',
    is_active: true,
});

const openCreateModal = () => {
    isEditing.value = false;
    formSubject.reset();
    showFormModal.value = true;
};

const openEditModal = (subject) => {
    isEditing.value = true;
    editingId.value = subject.id;
    formSubject.code = subject.code;
    formSubject.name = subject.name;
    formSubject.is_active = subject.is_active === 1 || subject.is_active === true;
    showFormModal.value = true;
};

const handleSave = () => {
    if (isEditing.value) {
        formSubject.put(route('kurikulum.subjects.update', editingId.value), {
            preserveScroll: true,
            onSuccess: () => showFormModal.value = false,
        });
    } else {
        formSubject.post(route('kurikulum.subjects.store'), {
            preserveScroll: true,
            onSuccess: () => showFormModal.value = false,
        });
    }
};

const handleDelete = (id) => {
    if (confirm('Yakin ingin menghapus Mata Pelajaran ini?')) {
        router.delete(route('kurikulum.subjects.destroy', id), {
            preserveScroll: true,
        });
    }
};

// Assign Teachers Modal
const showAssignModal = ref(false);
const assignSubjectId = ref(null);
const currentSubjectName = ref('');

const formAssign = useForm({
    teacher_ids: [],
});

const openAssignModal = (subject) => {
    assignSubjectId.value = subject.id;
    currentSubjectName.value = subject.name;
    formAssign.teacher_ids = subject.users ? subject.users.map(u => u.id) : [];
    showAssignModal.value = true;
};

const handleAssignSave = () => {
    formAssign.post(route('kurikulum.subjects.assign', assignSubjectId.value), {
        preserveScroll: true,
        onSuccess: () => showAssignModal.value = false,
    });
};

</script>

<template>
    <Head title="Master Data Mata Pelajaran" />

    <AuthenticatedLayout>
        <div class="mb-8 flex flex-col gap-2">
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Master Data Mata Pelajaran</h1>
            <p class="text-slate-500">Kelola daftar mata pelajaran dan relasi guru pengampu.</p>
        </div>

        <div class="grid grid-cols-1 gap-6">
            <Card class="border-slate-200 shadow-sm">
                <CardHeader class="bg-slate-50/50 border-b border-slate-100 pb-4 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <CardTitle class="text-lg flex items-center text-slate-800 whitespace-nowrap">
                        <BookOpen class="w-5 h-5 mr-2 text-indigo-600" /> Data Mata Pelajaran
                    </CardTitle>
                    
                    <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto items-center">
                        <Button @click="showImportModal = true" variant="outline" class="w-full sm:w-auto font-medium shadow-sm transition-colors whitespace-nowrap text-indigo-600 border-indigo-200 hover:bg-indigo-50">
                            <UploadCloud class="w-4 h-4 mr-2" /> Import Excel
                        </Button>
                        <Button @click="openCreateModal" class="bg-indigo-600 hover:bg-indigo-700 text-white w-full sm:w-auto font-medium shadow-sm transition-colors whitespace-nowrap">
                            <Plus class="w-4 h-4 mr-2" /> Tambah Mapel
                        </Button>
                    </div>
                </CardHeader>
                
                <CardContent class="pt-6">
                    <div class="overflow-x-auto border border-slate-200 rounded-lg mb-4">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-slate-50 border-b border-slate-200 text-slate-600">
                                <tr>
                                    <th class="px-6 py-3 font-semibold w-16 text-center">No</th>
                                    <th class="px-6 py-3 font-semibold">Kode Mapel</th>
                                    <th class="px-6 py-3 font-semibold">Nama Mata Pelajaran</th>
                                    <th class="px-6 py-3 font-semibold">Guru Pengampu</th>
                                    <th class="px-6 py-3 font-semibold text-center w-48">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(subject, index) in subjects.data" :key="subject.id" class="border-b border-slate-100 hover:bg-slate-50/80 transition-colors">
                                    <td class="px-6 py-3 text-center text-slate-500">{{ (subjects.current_page - 1) * subjects.per_page + index + 1 }}</td>
                                    <td class="px-6 py-3 font-mono text-indigo-700 font-medium">{{ subject.code ?? '-' }}</td>
                                    <td class="px-6 py-3 font-medium text-slate-900">{{ subject.name }}</td>
                                    <td class="px-6 py-3">
                                        <div class="flex flex-wrap gap-1">
                                            <span v-for="user in subject.users.slice(0, 3)" :key="user.id" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                                {{ user.name }}
                                            </span>
                                            <span v-if="subject.users.length > 3" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-50 text-slate-500 border border-slate-200">
                                                +{{ subject.users.length - 3 }} lainnya
                                            </span>
                                            <span v-if="!subject.users || subject.users.length === 0" class="text-xs text-slate-400 italic">
                                                Belum ada guru
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-3">
                                        <div class="flex items-center justify-center gap-2">
                                            <Button @click="openAssignModal(subject)" variant="outline" size="sm" class="h-8 border-indigo-200 text-indigo-700 hover:bg-indigo-50" title="Atur Guru Pengampu">
                                                <Users class="w-4 h-4" />
                                            </Button>
                                            <Button @click="openEditModal(subject)" variant="outline" size="sm" class="h-8 border-slate-200 text-slate-600 hover:bg-slate-50">
                                                <Edit class="w-4 h-4" />
                                            </Button>
                                            <Button @click="handleDelete(subject.id)" variant="outline" size="sm" class="h-8 border-rose-200 text-rose-600 hover:bg-rose-50 hover:text-rose-700">
                                                <Trash2 class="w-4 h-4" />
                                            </Button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="subjects.data.length === 0">
                                    <td colspan="5" class="px-6 py-8 text-center text-slate-500">
                                        Tidak ada data mata pelajaran.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div v-if="subjects.links && subjects.links.length > 3" class="flex justify-center mt-4">
                        <div class="flex flex-wrap gap-1">
                            <template v-for="(link, i) in subjects.links" :key="i">
                                <Link 
                                    v-if="link.url"
                                    :href="link.url" 
                                    v-html="link.label"
                                    class="px-3 py-1 text-sm border rounded-md transition-colors"
                                    :class="link.active ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-slate-600 border-slate-300 hover:bg-slate-50'"
                                />
                                <span 
                                    v-else 
                                    v-html="link.label"
                                    class="px-3 py-1 text-sm border border-transparent text-slate-400"
                                ></span>
                            </template>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- Import Modal -->
        <div v-if="showImportModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 overflow-hidden border border-slate-200">
                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/80">
                    <h3 class="font-bold text-slate-800 flex items-center">
                        <UploadCloud class="w-5 h-5 mr-2 text-indigo-600" />
                        Import Data Mata Pelajaran
                    </h3>
                    <button @click="showImportModal = false; formImport.reset()" class="text-slate-400 hover:text-slate-600 focus:outline-none transition-colors">
                        <span class="text-2xl leading-none">&times;</span>
                    </button>
                </div>
                <div class="p-6">
                    <div class="mb-5 bg-indigo-50 border border-indigo-100 rounded-lg p-4">
                        <p class="text-sm text-indigo-800 mb-3 leading-relaxed">
                            Unduh template Excel untuk melihat format kolom yang dibutuhkan. Isi data dengan benar, lalu unggah file tersebut.
                        </p>
                        <button type="button" @click.prevent="downloadTemplate" class="inline-flex items-center text-sm font-semibold text-indigo-700 hover:text-indigo-900 bg-white border border-indigo-200 px-3 py-2 rounded-md shadow-sm transition-all hover:shadow focus:outline-none">
                            <Download class="w-4 h-4 mr-2" /> Download Format Excel
                        </button>
                    </div>
                    
                    <form @submit.prevent="handleImport">
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">File Excel (.xlsx, .xls, .csv)</label>
                            <input type="file" @input="formImport.file = $event.target.files[0]" accept=".xlsx,.xls,.csv" class="block w-full text-sm text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 border border-slate-200 rounded-md file:cursor-pointer transition-all">
                            <div v-if="formImport.errors.file" class="text-rose-500 text-xs mt-2 font-medium">{{ formImport.errors.file }}</div>
                        </div>
                        
                        <div class="flex justify-end gap-3 pt-2">
                            <Button type="button" variant="outline" @click="showImportModal = false; formImport.reset()" class="border-slate-200 text-slate-600 hover:bg-slate-50">
                                Batal
                            </Button>
                            <Button type="submit" :disabled="formImport.processing || !formImport.file" class="bg-indigo-600 hover:bg-indigo-700 text-white min-w-[120px]">
                                <span v-if="formImport.processing" class="flex items-center justify-center">
                                    <span class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></span> Proses...
                                </span>
                                <span v-else>Import Data</span>
                            </Button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Form Modal (Tambah/Edit) -->
        <div v-if="showFormModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 overflow-hidden border border-slate-200">
                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/80">
                    <h3 class="font-bold text-slate-800 flex items-center">
                        <BookOpen class="w-5 h-5 mr-2 text-indigo-600" />
                        {{ isEditing ? 'Edit Mata Pelajaran' : 'Tambah Mata Pelajaran' }}
                    </h3>
                    <button @click="showFormModal = false; formSubject.reset()" class="text-slate-400 hover:text-slate-600 focus:outline-none transition-colors">
                        <span class="text-2xl leading-none">&times;</span>
                    </button>
                </div>
                <div class="p-6">
                    <form @submit.prevent="handleSave">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Kode Mapel</label>
                                <input type="text" v-model="formSubject.code" class="w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Contoh: BIND">
                                <p v-if="formSubject.errors.code" class="text-rose-500 text-xs mt-1">{{ formSubject.errors.code }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Nama Mata Pelajaran</label>
                                <input type="text" v-model="formSubject.name" class="w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Contoh: Bahasa Indonesia">
                                <p v-if="formSubject.errors.name" class="text-rose-500 text-xs mt-1">{{ formSubject.errors.name }}</p>
                            </div>

                            <div class="flex items-center">
                                <input type="checkbox" id="is_active" v-model="formSubject.is_active" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                <label for="is_active" class="ml-2 block text-sm text-slate-700">Status Aktif</label>
                            </div>
                        </div>
                        
                        <div class="flex justify-end gap-3 pt-6 mt-2 border-t border-slate-100">
                            <Button type="button" variant="outline" @click="showFormModal = false; formSubject.reset()" class="border-slate-200 text-slate-600 hover:bg-slate-50">
                                Batal
                            </Button>
                            <Button type="submit" :disabled="formSubject.processing" class="bg-indigo-600 hover:bg-indigo-700 text-white min-w-[100px]">
                                <span v-if="formSubject.processing">Menyimpan...</span>
                                <span v-else>Simpan</span>
                            </Button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Assign Teachers Modal -->
        <div v-if="showAssignModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl mx-4 overflow-hidden border border-slate-200 max-h-[90vh] flex flex-col">
                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/80 shrink-0">
                    <h3 class="font-bold text-slate-800 flex items-center">
                        <Users class="w-5 h-5 mr-2 text-indigo-600" />
                        Atur Guru Pengampu: {{ currentSubjectName }}
                    </h3>
                    <button @click="showAssignModal = false; formAssign.reset()" class="text-slate-400 hover:text-slate-600 focus:outline-none transition-colors">
                        <span class="text-2xl leading-none">&times;</span>
                    </button>
                </div>
                <div class="p-6 overflow-y-auto flex-1">
                    <div class="bg-indigo-50 border border-indigo-100 rounded-lg p-4 mb-4">
                        <p class="text-sm text-indigo-800 leading-relaxed">
                            Pilih guru-guru yang akan mengampu mata pelajaran ini. Anda juga dapat menggunakan fitur Import Excel Guru (di menu lain) untuk mengatur relasi ini secara massal.
                        </p>
                    </div>

                    <form @submit.prevent="handleAssignSave">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 max-h-[40vh] overflow-y-auto pr-2">
                            <label v-for="teacher in teachers" :key="teacher.id" class="flex items-center p-3 border border-slate-200 rounded-lg hover:bg-slate-50 cursor-pointer transition-colors">
                                <input type="checkbox" v-model="formAssign.teacher_ids" :value="teacher.id" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 w-4 h-4">
                                <span class="ml-3 text-sm font-medium text-slate-700">{{ teacher.name }}</span>
                            </label>
                            
                            <div v-if="teachers.length === 0" class="col-span-2 text-center py-4 text-slate-500 text-sm">
                                Tidak ada data guru.
                            </div>
                        </div>
                        
                        <div class="flex justify-end gap-3 pt-6 mt-4 border-t border-slate-100">
                            <Button type="button" variant="outline" @click="showAssignModal = false; formAssign.reset()" class="border-slate-200 text-slate-600 hover:bg-slate-50">
                                Batal
                            </Button>
                            <Button type="submit" :disabled="formAssign.processing" class="bg-indigo-600 hover:bg-indigo-700 text-white min-w-[100px]">
                                <span v-if="formAssign.processing">Menyimpan...</span>
                                <span v-else>Simpan Relasi</span>
                            </Button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>
