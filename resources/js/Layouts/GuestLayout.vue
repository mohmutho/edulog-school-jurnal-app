<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
<div class="min-h-screen w-full flex bg-gray-50">
        <div class="hidden lg:flex lg:w-3/5 bg-slate-900 relative items-center justify-center overflow-hidden">
            <div class="absolute inset-0 bg-blue-600/20 mix-blend-multiply"></div>
            <div class="z-10 text-center text-white px-12 flex flex-col items-center">
                <img 
                    src="/images/logo_smansa.webp" 
                    alt="Logo SMA Negeri 1 Rembang" 
                    class="w-50 h-50 object-contain mb-6 drop-shadow-lg" 
                />
                <h1 class="text-4xl font-bold mb-4">E-Jurnal App</h1>
                <p class="text-lg text-slate-300">Sistem Informasi Akademik & Presensi Cerdas</p>
                <h2 class="text-2xl font-bold mb-2">SMA Negeri 1 Rembang</h2>
            </div>
        </div>

        <div class="w-full lg:w-2/5 flex items-center justify-center p-8 sm:p-12">
            <div class="w-full max-w-md">
                <slot /> </div>
        </div>
    </div>
</template>
