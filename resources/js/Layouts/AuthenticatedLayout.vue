<script setup>
import { Link } from '@inertiajs/vue3';
import { 
    LayoutDashboard, 
    CalendarDays, 
    BookOpenCheck, 
    Users, 
    LogOut, 
    Menu, 
    Bell,
    ShieldCheck,
    UserCog,
    Database,
    CheckSquare,
    Key
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
import { ChevronDown } from 'lucide-vue-next';

// MENGAMBIL DATA DARI BACKEND (ROUTING)
const page = usePage();
const user = computed(() => page.props.auth?.user || { name: 'Guest', role: 'Guest' });

// Menu navigasi disiapkan dalam array agar mudah dilooping
const menuGuru = [
    { 
        name: 'Dashboard', 
        href: route('dashboard'), 
        icon: LayoutDashboard, 
        current: route().current('dashboard') 
    },
    { 
        name: 'Jadwal Mengajar', 
        href: route('schedule.index'), 
        icon: CalendarDays, 
        current: route().current('schedule.index') 
    },
    { 
        name: 'Jurnal & Presensi', 
        href: route('journal.index'), // Arahkan ke route index jurnal
        icon: BookOpenCheck, 
        current: route().current('journal.index') || route().current('journal.show') || route().current('journal.edit')
    },
    { 
        name: 'Data Siswa', 
        href: route('student.index'), 
        icon: Users, 
        current: route().current('student.index') 
    },
    { 
        name: 'Kalender Akademik', 
        href: route('calendar.index'), 
        icon: CalendarDays, 
        current: route().current('calendar.index') 
    },
];

// 2. Menu Admin Kurikulum
const menuAdminKurikulum = [
    { 
        name: 'Dashboard Kurikulum', 
        href: route('kurikulum.dashboard'), 
        icon: LayoutDashboard, 
        current: route().current('kurikulum.dashboard') 
    },
    { 
        name: 'Tahun Ajaran', 
        href: route('kurikulum.academic-years.index'), 
        icon: CalendarDays, 
        current: route().current('kurikulum.academic-years.*') 
    },
    { 
        name: 'Data Siswa', 
        href: route('kurikulum.students.index'), // <-- Update href ini
        icon: Users, 
        current: route().current('kurikulum.students.*') 
    },
    { 
        name: 'Master Data', 
        href: '#', 
        icon: Database, 
        current: false,
        isAccordion: true,
        children: [
            {
                name: 'Reset Password Guru', 
                href: route('kurikulum.users-reset.index'), 
                icon: Key, 
                current: route().current('kurikulum.users-reset.*')
            }
        ]
    },
    { 
        name: 'Plotting Jadwal', 
        href: route('kurikulum.schedules.index'), 
        icon: CalendarDays, 
        current: route().current('kurikulum.schedules.*') 
    },
    { 
        name: 'Monitor Jurnal', 
        href: '#', 
        icon: CheckSquare, 
        current: false 
    },
];

const isMasterDataExpanded = ref(route().current('kurikulum.users-reset.*'));
const toggleMasterData = () => {
    isMasterDataExpanded.value = !isMasterDataExpanded.value;
};

// 3. Menu Super Administrator
const menuAdministrator = [
    { 
        name: 'System Dashboard', 
        href: '#', 
        icon: LayoutDashboard, 
        current: false 
    },
    { 
        name: 'Manajemen User & Role', 
        href: '#', 
        icon: UserCog, 
        current: false 
    },
    { 
        name: 'Impersonate Akses', 
        href: '#', 
        icon: ShieldCheck, 
        current: false 
    },
];

const activeNavigation = computed(() => {
    if (user.value.role === 'administrator') return menuAdministrator;
    if (user.value.role === 'admin_kurikulum') return menuAdminKurikulum;
    return menuGuru;
});

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
                <template v-for="item in activeNavigation" :key="item.name">
                    
                    <!-- If item is Accordion -->
                    <div v-if="item.isAccordion" class="mt-2">
                        <button 
                            @click="toggleMasterData"
                            class="w-full flex items-center justify-between px-3 py-2.5 text-sm font-medium rounded-md text-slate-300 hover:bg-slate-800 hover:text-white transition-colors"
                        >
                            <div class="flex items-center">
                                <component :is="item.icon" class="mr-3 h-5 w-5 shrink-0" aria-hidden="true" />
                                {{ item.name }}
                            </div>
                            <ChevronDown 
                                :class="[
                                    'h-4 w-4 transition-transform duration-300', 
                                    isMasterDataExpanded ? 'rotate-180' : ''
                                ]" 
                            />
                        </button>
                        
                        <!-- Accordion Children -->
                        <div 
                            v-show="isMasterDataExpanded"
                            class="mt-1 space-y-1 overflow-hidden transition-all duration-300"
                        >
                            <Link 
                                v-for="child in item.children" 
                                :key="child.name" 
                                :href="child.href || '#'"
                                :class="[
                                    child.current ? 'bg-blue-600/20 text-blue-400' : 'text-slate-400 hover:bg-slate-800 hover:text-white',
                                    'ml-8 flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors'
                                ]"
                            >
                                <component :is="child.icon" class="mr-3 h-4 w-4 shrink-0" aria-hidden="true" />
                                {{ child.name }}
                            </Link>
                        </div>
                    </div>

                    <!-- Normal Menu Item -->
                    <Link 
                        v-else
                        :href="item.href || '#'"
                        :class="[
                            item.current ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white',
                            item.isTitle ? 'opacity-70 cursor-default hover:bg-transparent pointer-events-none mt-2' : '',
                            'group flex items-center px-3 py-2.5 text-sm font-medium rounded-md transition-colors'
                        ]"
                    >
                        <component :is="item.icon" class="mr-3 h-5 w-5 shrink-0" aria-hidden="true" />
                        {{ item.name }}
                    </Link>
                </template>
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
                                <template v-for="item in activeNavigation" :key="item.name">
                                    <div v-if="item.isAccordion" class="mt-2">
                                        <button 
                                            @click="toggleMasterData"
                                            class="w-full flex items-center justify-between px-3 py-2.5 text-sm font-medium rounded-md text-slate-300 hover:bg-slate-800 hover:text-white transition-colors"
                                        >
                                            <div class="flex items-center">
                                                <component :is="item.icon" class="mr-3 h-5 w-5 shrink-0" />
                                                {{ item.name }}
                                            </div>
                                            <ChevronDown :class="['h-4 w-4 transition-transform duration-300', isMasterDataExpanded ? 'rotate-180' : '']" />
                                        </button>
                                        <div v-show="isMasterDataExpanded" class="mt-1 space-y-1">
                                            <Link 
                                                v-for="child in item.children" :key="child.name" :href="child.href || '#'"
                                                :class="[
                                                    child.current ? 'bg-blue-600/20 text-blue-400' : 'text-slate-400 hover:bg-slate-800 hover:text-white',
                                                    'ml-8 flex items-center px-3 py-2 text-sm font-medium rounded-md'
                                                ]"
                                            >
                                                <component :is="child.icon" class="mr-3 h-4 w-4 shrink-0" />
                                                {{ child.name }}
                                            </Link>
                                        </div>
                                    </div>
                                    <Link v-else :href="item.href || '#'" :class="[
                                        item.current ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white',
                                        item.isTitle ? 'opacity-70 cursor-default hover:bg-transparent pointer-events-none mt-2' : '',
                                        'flex items-center px-3 py-2.5 text-sm font-medium rounded-md'
                                    ]">
                                        <component :is="item.icon" class="mr-3 h-5 w-5 shrink-0" />
                                        {{ item.name }}
                                    </Link>
                                </template>
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
                            <Link :href="route('profile.edit')">
                                <DropdownMenuItem class="cursor-pointer">Pengaturan Akun</DropdownMenuItem>
                            </Link>
                            <DropdownMenuSeparator />
                            <Link :href="route('logout')" method="post" as="button" class="w-full text-left">
                                <DropdownMenuItem class="text-red-600 cursor-pointer focus:text-red-600 focus:bg-red-50">
                                    <LogOut class="mr-2 h-4 w-4" />
                                    <span>Logout</span>
                                </DropdownMenuItem>
                            </Link>
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