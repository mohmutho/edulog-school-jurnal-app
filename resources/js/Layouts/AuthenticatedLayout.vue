<script setup>
import { Link } from '@inertiajs/vue3';
import { 
    LayoutDashboard, 
    CalendarDays, 
    BookOpenCheck, 
    Users, 
    LogOut, 
    Menu, 
    Bell
} from 'lucide-vue-next';
//Komponen Shadcn UI
import { Button } from '@/Components/ui/button';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from '@/Components/ui/dropdown-menu';
import { Sheet, SheetContent, SheetTrigger } from '@/Components/ui/sheet';
import { computed, ref, onMounted, onBeforeUnmount } from 'vue';
import { usePage } from '@inertiajs/vue3';

// MENGAMBIL DATA DARI BACKEND (ROUTING)
const page = usePage();
const user = computed(() => page.props.auth?.user || { name: 'Guest', role: 'Guest' });

// Menu navigasi disiapkan dalam array agar mudah dilooping
// Menu navigasi disiapkan dalam array agar mudah dilooping
const navigation = [
    { 
        name: 'Dashboard', 
        href: route('dashboard'), 
        icon: LayoutDashboard, 
        current: route().current('dashboard') 
    },
    { 
        name: 'Jadwal Mengajar', 
        href: '#', // Biarkan '#' untuk fitur yang belum dibuat
        icon: CalendarDays, 
        current: false 
    },
    { 
        name: 'Jurnal & Presensi', 
        href: route('journal.index'), // Arahkan ke route index jurnal
        icon: BookOpenCheck, 
        current: route().current('journal.index') || route().current('journal.show') || route().current('journal.edit')
    },
    { 
        name: 'Data Siswa', 
        href: '#', 
        icon: Users, 
        current: false 
    },
];

// LOGIKA HARI & TANGGAL SAJA
const currentDate = ref('');
let dateTimer = null;

const updateDate = () => {
    const now = new Date();
    // Format: Rabu, 11 Maret 2026
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    currentDate.value = now.toLocaleDateString('id-ID', options);
};

onMounted(() => {
    updateDate();
    dateTimer = setInterval(updateDate, 60000); // Cek perubahan hari setiap 1 menit
});

onBeforeUnmount(() => {
    if (dateTimer) clearInterval(dateTimer);
});
</script>

<template>
    <div class="min-h-screen bg-slate-50 flex w-full">
        <aside class="hidden md:flex flex-col w-64 bg-slate-900 text-white fixed inset-y-0 z-50">
            <div class="h-16 flex items-center px-6 bg-slate-950">
                <img src="/images/logo_smansa.webp" alt="Logo" class="h-8 w-8 mr-3 object-contain" />
                <span class="text-lg font-bold tracking-tight">E-Jurnal App</span>
            </div>

            <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
                <Link 
                    v-for="item in navigation" 
                    :key="item.name" 
                    :href="item.href"
                    :class="[
                        item.current ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white',
                        'group flex items-center px-3 py-2.5 text-sm font-medium rounded-md transition-colors'
                    ]"
                >
                    <component :is="item.icon" class="mr-3 h-5 w-5 shrink-0" aria-hidden="true" />
                    {{ item.name }}
                </Link>
            </nav>

            <div class="p-4 bg-slate-950 flex items-center">
                <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold text-sm mr-3">
                    {{ user.name.charAt(0) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-white truncate">{{ user.name }}</p>
                    <p class="text-xs text-slate-400 truncate">{{ user.role }}</p>
                </div>
            </div>
        </aside>

        <div class="md:pl-64 flex flex-col flex-1 min-h-screen">
            <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 lg:px-8 sticky top-0 z-40 shadow-sm">
                
                <div class="md:hidden flex items-center">
                    <Sheet>
                        <SheetTrigger asChild>
                            <Button variant="ghost" size="icon" class="-ml-2">
                                <Menu class="h-6 w-6 text-slate-600" />
                            </Button>
                        </SheetTrigger>
                        <SheetContent side="left" class="w-64 bg-slate-900 p-0 border-none text-white">
                            <div class="h-16 flex items-center px-6 bg-slate-950">
                                <span class="text-lg font-bold">E-Jurnal Menu</span>
                            </div>
                            <nav class="px-4 py-6 space-y-1">
                                <Link v-for="item in navigation" :key="item.name" :href="item.href" class="text-slate-300 hover:bg-slate-800 flex items-center px-3 py-2.5 text-sm font-medium rounded-md">
                                    <component :is="item.icon" class="mr-3 h-5 w-5 shrink-0" />
                                    {{ item.name }}
                                </Link>
                            </nav>
                        </SheetContent>
                    </Sheet>
                </div>

                <div class="flex items-center space-x-4 ml-auto">
                    <div class="hidden md:flex items-center text-sm font-medium text-slate-600 bg-slate-100 px-3 py-1.5 rounded-full border border-slate-200">
                        {{ currentDate }}
                    </div>
                    
                    <Button variant="ghost" size="icon" class="text-slate-500 hover:text-slate-700">
                        <Bell class="h-5 w-5" />
                    </Button>

                    <DropdownMenu>
                        <DropdownMenuTrigger asChild>
                            <Button variant="ghost" class="relative h-8 w-8 rounded-full ml-2 cursor-pointer">
                                <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold border border-blue-200">
                                    {{ user.name.charAt(0) }}
                                </div>
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end" class="w-56 mt-2">
                            <DropdownMenuLabel class="font-normal">
                                <div class="flex flex-col space-y-1">
                                    <p class="text-sm font-medium leading-none">{{ user.name }}</p>
                                    <p class="text-xs leading-none text-muted-foreground">{{ user.email }}</p>
                                </div>
                            </DropdownMenuLabel>
                            <DropdownMenuSeparator />
                            <DropdownMenuItem class="cursor-pointer">Profil Saya</DropdownMenuItem>
                            <DropdownMenuItem class="cursor-pointer">Pengaturan</DropdownMenuItem>
                            <DropdownMenuSeparator />
                            <DropdownMenuItem class="text-red-600 cursor-pointer focus:text-red-600 focus:bg-red-50">
                                <LogOut class="mr-2 h-4 w-4" />
                                <span>Logout</span>
                            </DropdownMenuItem>
                        </DropdownMenuContent>
                    </DropdownMenu>
                </div>
            </header>

            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                <slot />
            </main>
        </div>
    </div>
</template>