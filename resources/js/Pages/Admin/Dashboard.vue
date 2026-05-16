<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Badge } from '@/Components/ui/badge';
import { 
    Users, 
    BookOpen, 
    Library, 
    GraduationCap,
    AlertCircle,
    CheckCircle2,
    UploadCloud,
    LayoutGrid,
    CalendarDays,
    BarChart3
} from 'lucide-vue-next';

const props = defineProps({
    total_siswa: Number,
    total_kelas: Number,
    total_guru: Number,
    total_mapel: Number,
    reset_requests: Number,
    student_stats: Object,
    active_year: Object
});
</script>

<template>
    <Head title="Dashboard Kurikulum" />

    <AuthenticatedLayout>
        <div class="space-y-6">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-slate-900">Dashboard Admin Kurikulum</h2>
                    <p class="text-slate-500 mt-1">Berikut adalah ringkasan data akademik Anda saat ini.</p>
                </div>
                
                <!-- Active Year Badge / Warning -->
                <div>
                    <div v-if="active_year" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 text-blue-800 rounded-lg border border-blue-200 shadow-sm">
                        <CalendarDays class="w-5 h-5 text-blue-600" />
                        <span class="font-medium text-sm">
                            📅 Tahun Ajaran Aktif: {{ active_year.name }} - {{ active_year.semester === 'odd' ? 'Ganjil' : (active_year.semester === 'even' ? 'Genap' : active_year.semester) }}
                        </span>
                    </div>
                    <div v-else class="inline-flex items-center gap-2 px-4 py-2 bg-red-50 text-red-800 rounded-lg border border-red-200 shadow-sm">
                        <AlertCircle class="w-5 h-5 text-red-600" />
                        <span class="font-medium text-sm">
                            ⚠️ Belum ada Tahun Ajaran yang diaktifkan. Silakan atur di menu Master Data.
                        </span>
                    </div>
                </div>
            </div>

            <!-- Baris Atas: 4 Kartu Statistik -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Siswa</CardTitle>
                        <Users class="h-4 w-4 text-blue-600" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ total_siswa }}</div>
                        <p class="text-xs text-muted-foreground mt-1">Siswa terdaftar</p>
                    </CardContent>
                </Card>
                
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Kelas</CardTitle>
                        <LayoutGrid class="h-4 w-4 text-emerald-600" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ total_kelas }}</div>
                        <p class="text-xs text-muted-foreground mt-1">Rombongan belajar</p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Guru</CardTitle>
                        <GraduationCap class="h-4 w-4 text-amber-600" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ total_guru }}</div>
                        <p class="text-xs text-muted-foreground mt-1">Tenaga pengajar aktif</p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Mata Pelajaran</CardTitle>
                        <BookOpen class="h-4 w-4 text-purple-600" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ total_mapel }}</div>
                        <p class="text-xs text-muted-foreground mt-1">Mata pelajaran tersedia</p>
                    </CardContent>
                </Card>
            </div>

            <!-- Baris Bawah -->
            <div class="grid gap-4 grid-cols-1 lg:grid-cols-12">
                
                <!-- Kolom Kiri: 60% (col-span-8) -->
                <div class="lg:col-span-8 space-y-4">
                    <!-- Pintasan Cepat -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Aksi Cepat</CardTitle>
                            <CardDescription>Pintasan untuk mengelola data akademik utama.</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div class="flex flex-wrap gap-3">
                                <Link :href="route('kurikulum.students.index')">
                                    <Button variant="outline" class="flex items-center gap-2">
                                        <UploadCloud class="w-4 h-4" />
                                        Import Data Siswa
                                    </Button>
                                </Link>
                                <Button variant="outline" class="flex items-center gap-2">
                                    <Library class="w-4 h-4" />
                                    Manajemen Kelas
                                </Button>
                                <Link :href="route('kurikulum.schedules.index')">
                                    <Button variant="outline" class="flex items-center gap-2">
                                        <CalendarDays class="w-4 h-4" />
                                        Plotting Jadwal
                                    </Button>
                                </Link>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Grafik Jurnal Mengajar -->
                    <Card class="relative overflow-hidden">
                        <CardHeader>
                            <div class="flex items-center justify-between">
                                <div>
                                    <CardTitle>Statistik Jurnal Mengajar</CardTitle>
                                    <CardDescription>Aktivitas pengisian jurnal oleh guru minggu ini.</CardDescription>
                                </div>
                                <Badge variant="secondary" class="bg-amber-100 text-amber-800 border-amber-200">
                                    <AlertCircle class="w-3 h-3 mr-1" />
                                    BETA
                                </Badge>
                            </div>
                        </CardHeader>
                        <CardContent>
                            <div class="h-64 w-full bg-slate-50 border border-dashed border-slate-200 rounded-lg flex items-end justify-center gap-4 p-4 relative">
                                <!-- Warning text -->
                                <div class="absolute inset-0 flex items-center justify-center bg-white/50 backdrop-blur-[1px] z-10">
                                    <div class="bg-amber-50 border border-amber-200 text-amber-800 px-4 py-3 rounded-lg shadow-sm max-w-md text-center">
                                        <AlertCircle class="w-6 h-6 mx-auto mb-2 text-amber-600" />
                                        <p class="font-medium text-sm">⚠️ TODO: Fitur Statistik Jurnal Mengajar ini menggunakan data statis dan masih dalam tahap pengembangan.</p>
                                    </div>
                                </div>
                                
                                <!-- Static dummy chart bars -->
                                <div class="w-12 bg-blue-200 rounded-t-md h-[40%]"></div>
                                <div class="w-12 bg-blue-300 rounded-t-md h-[60%]"></div>
                                <div class="w-12 bg-blue-400 rounded-t-md h-[30%]"></div>
                                <div class="w-12 bg-blue-500 rounded-t-md h-[80%]"></div>
                                <div class="w-12 bg-blue-600 rounded-t-md h-[50%]"></div>
                                <div class="w-12 bg-blue-700 rounded-t-md h-[90%]"></div>
                                <div class="w-12 bg-blue-800 rounded-t-md h-[70%]"></div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Kolom Kanan: 40% (col-span-4) -->
                <div class="lg:col-span-4 space-y-4">
                    
                    <!-- Widget Reset Password -->
                    <div :class="[
                        'rounded-xl border p-4 shadow-sm transition-all',
                        reset_requests > 0 
                            ? 'bg-red-50 border-red-200 text-red-900' 
                            : 'bg-emerald-50 border-emerald-200 text-emerald-900'
                    ]">
                        <div class="flex items-start gap-4">
                            <div :class="[
                                'p-2 rounded-full mt-0.5',
                                reset_requests > 0 ? 'bg-red-100' : 'bg-emerald-100'
                            ]">
                                <AlertCircle v-if="reset_requests > 0" class="w-5 h-5 text-red-600" />
                                <CheckCircle2 v-else class="w-5 h-5 text-emerald-600" />
                            </div>
                            <div>
                                <h3 class="font-semibold mb-1">
                                    Status Keamanan Akun
                                </h3>
                                <p v-if="reset_requests > 0" class="text-sm">
                                    Terdapat <strong class="text-red-700 text-base">{{ reset_requests }}</strong> Guru meminta reset password. Segera periksa pada halaman Master Data.
                                </p>
                                <p v-else class="text-sm">
                                    Semua akun aman. Tidak ada permintaan reset password saat ini.
                                </p>
                                
                                <Link v-if="reset_requests > 0" :href="route('kurikulum.users-reset.index')">
                                    <Button size="sm" variant="outline" class="mt-3 bg-white hover:bg-red-50 border-red-200 text-red-700">
                                        Tinjau Permintaan
                                    </Button>
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Widget Status Siswa -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Status Siswa</CardTitle>
                            <CardDescription>Distribusi status pendaftaran siswa.</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                                        <span class="text-sm font-medium">Siswa Aktif</span>
                                    </div>
                                    <span class="font-bold">{{ student_stats?.aktif || 0 }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <div class="w-3 h-3 rounded-full bg-amber-500"></div>
                                        <span class="text-sm font-medium">Siswa Mutasi</span>
                                    </div>
                                    <span class="font-bold">{{ student_stats?.mutasi || 0 }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <div class="w-3 h-3 rounded-full bg-red-500"></div>
                                        <span class="text-sm font-medium">Siswa Keluar</span>
                                    </div>
                                    <span class="font-bold">{{ student_stats?.keluar || 0 }}</span>
                                </div>
                            </div>
                            
                            <div class="mt-6 h-2 w-full bg-slate-100 rounded-full overflow-hidden flex">
                                <div class="bg-emerald-500 h-full" :style="`width: ${((student_stats?.aktif || 0) / Math.max(total_siswa, 1)) * 100}%`"></div>
                                <div class="bg-amber-500 h-full" :style="`width: ${((student_stats?.mutasi || 0) / Math.max(total_siswa, 1)) * 100}%`"></div>
                                <div class="bg-red-500 h-full" :style="`width: ${((student_stats?.keluar || 0) / Math.max(total_siswa, 1)) * 100}%`"></div>
                            </div>
                        </CardContent>
                    </Card>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
