<script setup>
import { ref, watch } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select';
import { Search, Clock, MapPin, BookOpen, User, Download, CalendarDays, MessageCircle, Phone } from 'lucide-vue-next';

const props = defineProps({
    searchResults: Array,
    classrooms: Array,
    filters: Object,
});

const page = usePage();
const currentUser = page.props.auth.user;

const searchDay = ref(props.filters.search_day ? props.filters.search_day.toString() : 'all');
const searchClass = ref(props.filters.search_class ? props.filters.search_class.toString() : 'all');

const daysList = [
    { id: '1', name: 'Senin' }, { id: '2', name: 'Selasa' },
    { id: '3', name: 'Rabu' }, { id: '4', name: 'Kamis' },
    { id: '5', name: 'Jumat' }
];

watch([searchDay, searchClass], ([newDay, newClass]) => {
    router.get(route('schedule.index'), {
        search_day: newDay === 'all' ? null : newDay,
        search_class: newClass === 'all' ? null : newClass,
    }, {
        preserveState: true,
        replace: true
    });
});

const handleExport = () => {
    window.open(route('schedule.export'), '_blank');
};

const formatWhatsAppLink = (targetNumber, targetName, scheduleDetails) => {
    if (!targetNumber) return '#';
    let cleanNumber = targetNumber.replace(/\D/g, '');
    if (cleanNumber.startsWith('0')) {
        cleanNumber = '62' + cleanNumber.substring(1);
    }
    
    // Template Pesan yang sangat sopan dan spesifik
    const message = encodeURIComponent(
        `Halo Bapak/Ibu ${targetName},\n\nSaya ${currentUser.name}. Mohon maaf mengganggu waktunya.\n\nTerkait jadwal Bapak/Ibu mengajar ${scheduleDetails.subject} di kelas ${scheduleDetails.classroom} pada hari ${scheduleDetails.day}, jam ${scheduleDetails.time}, apakah sekiranya kita bisa bertukar jadwal mengajar?\n\nTerima kasih banyak sebelumnya.`
    );
    
    return `https://wa.me/${cleanNumber}?text=${message}`;
};
</script>

<template>
    <Head title="Pencarian Jadwal" />

    <AuthenticatedLayout>
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Pencarian Jadwal</h1>
                <p class="text-slate-500 mt-1">Cari jadwal mengajar rekan guru berdasarkan hari atau kelas.</p>
            </div>
            
            <Button @click="handleExport" class="bg-indigo-600 hover:bg-indigo-700 w-full sm:w-auto shadow-sm">
                <Download class="w-4 h-4 mr-2" />
                Cetak Jadwal Pribadi (PDF)
            </Button>
        </div>

        <Card class="mb-8 border-t-4 border-t-blue-500 shadow-sm">
            <CardContent class="p-6">
                <div class="flex flex-col md:flex-row gap-6">
                    <div class="flex-1">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 flex items-center gap-2">
                            <CalendarDays class="w-4 h-4 text-blue-500" /> Pilih Hari
                        </label>
                        <Select v-model="searchDay">
                            <SelectTrigger class="bg-slate-50 border-slate-200">
                                <SelectValue placeholder="Semua Hari" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="all">Semua Hari</SelectItem>
                                <SelectItem v-for="day in daysList" :key="day.id" :value="day.id">
                                    {{ day.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                    <div class="flex-1">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 flex items-center gap-2">
                            <MapPin class="w-4 h-4 text-emerald-500" /> Pilih Kelas
                        </label>
                        <Select v-model="searchClass">
                            <SelectTrigger class="bg-slate-50 border-slate-200">
                                <SelectValue placeholder="Semua Kelas" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="all">Semua Kelas</SelectItem>
                                <SelectItem v-for="cls in classrooms" :key="cls.id" :value="cls.id.toString()">
                                    {{ cls.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                </div>
            </CardContent>
        </Card>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            <div v-if="searchResults.length === 0" class="col-span-full text-center py-20 bg-white rounded-xl border border-slate-200 border-dashed">
                <Search class="w-12 h-12 text-slate-300 mx-auto mb-4" />
                <h3 class="text-lg font-bold text-slate-700">Mulai Pencarian</h3>
                <p class="text-slate-500 mt-1 max-w-sm mx-auto">Silakan pilih filter Hari atau Kelas di atas untuk menampilkan jadwal mengajar guru.</p>
            </div>

            <Card v-for="item in searchResults" :key="item.id" class="border-slate-200 hover:shadow-md transition-all group">
                <CardHeader class="pb-3 bg-slate-50/50 border-b border-slate-100">
                    <CardTitle class="text-base flex items-center text-slate-800">
                        <User class="w-4 h-4 mr-2 text-blue-600 group-hover:scale-110 transition-transform" />
                        {{ item.user.name }}
                    </CardTitle>
                </CardHeader>
                <CardContent class="pt-4">
                    <div class="space-y-3 text-sm">
                        <div class="flex items-center text-slate-700">
                            <BookOpen class="w-4 h-4 mr-3 text-blue-500" />
                            <span class="font-semibold">{{ item.subject.name }}</span>
                        </div>
                        <div class="flex items-center text-slate-700">
                            <MapPin class="w-4 h-4 mr-3 text-emerald-500" />
                            Kelas <span class="font-bold ml-1 bg-emerald-50 text-emerald-700 border border-emerald-100 px-2 py-0.5 rounded">{{ item.classroom.name }}</span>
                        </div>
                        <div class="flex items-center text-slate-700">
                            <Clock class="w-4 h-4 mr-3 text-orange-500" />
                            <span class="font-medium">{{ item.day_name }}</span>, {{ item.formatted_time }} WIB
                        </div>
                        <div class="flex items-center text-slate-700">
                            <Phone class="w-4 h-4 mr-3 text-slate-500" />
                            <span v-if="item.user.whatsapp_number" class="font-medium text-slate-700 font-mono">{{ item.user.whatsapp_number }}</span>
                            <span v-else class="text-slate-400 italic text-xs">Nomor WA tidak disematkan</span>
                        </div>
                    </div>
                    <div v-if="item.user.whatsapp_number" class="mt-4 pt-4 border-t border-slate-100">
                        <a :href="formatWhatsAppLink(
                                item.user.whatsapp_number, 
                                item.user.name, 
                                { 
                                    subject: item.subject.name, 
                                    classroom: item.classroom.name, 
                                    day: item.day_name, 
                                    time: item.formatted_time 
                                }
                            )" 
                            target="_blank" 
                            class="flex items-center justify-center w-full bg-[#25D366]/10 text-[#128C7E] hover:bg-[#25D366] hover:text-white py-2 rounded-lg text-sm font-bold transition-all duration-300">
                            <MessageCircle class="w-4 h-4 mr-2" />
                            Hubungi via WhatsApp
                        </a>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AuthenticatedLayout>
</template>