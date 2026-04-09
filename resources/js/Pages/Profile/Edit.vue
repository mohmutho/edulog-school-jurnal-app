<script setup>
import { ref } from 'vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { User, Lock, Save, CheckCircle, XCircle } from 'lucide-vue-next';

const user = usePage().props.auth.user;

// --- STATE UNTUK TOAST NOTIFICATION ---
const showToast = ref(false);
const toastMessage = ref('');
const toastType = ref('success'); // 'success' atau 'error'

const triggerToast = (message, type = 'success') => {
    toastMessage.value = message;
    toastType.value = type;
    showToast.value = true;
    
    // Hilangkan toast otomatis setelah 3 detik
    setTimeout(() => {
        showToast.value = false;
    }, 3000);
};

// --- FORM 1: INFORMASI PROFIL ---
const formProfile = useForm({
    name: user.name,
    email: user.email,
    whatsapp_number: user.whatsapp_number || '',
    gender: user.gender || 'L',
});

const updateProfile = () => {
    formProfile.patch(route('profile.update'), {
        preserveScroll: true,
        onSuccess: () => {
            triggerToast('Profil berhasil diperbarui!', 'success');
        },
        onError: () => {
            triggerToast('Gagal memperbarui profil. Silakan periksa kembali isian Anda.', 'error');
        }
    });
};

// --- FORM 2: UBAH PASSWORD ---
const formPassword = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    formPassword.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => {
            formPassword.reset();
            triggerToast('Password keamanan Anda berhasil diubah!', 'success');
        },
        onError: () => {
            if (formPassword.errors.password) {
                formPassword.reset('password', 'password_confirmation');
            }
            if (formPassword.errors.current_password) {
                formPassword.reset('current_password');
            }
            triggerToast('Gagal mengubah password. Pastikan password lama Anda benar.', 'error');
        },
    });
};
</script>

<template>
    <Head title="Pengaturan Akun" />

    <AuthenticatedLayout>
        
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Pengaturan Akun</h1>
                <p class="text-sm text-slate-500">Kelola informasi profil dan keamanan akun Anda.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
            
            <Card class="border-slate-200 shadow-sm">
                <CardHeader class="bg-slate-50/50 border-b border-slate-100 pb-4">
                    <CardTitle class="text-lg flex items-center text-slate-800">
                        <User class="w-5 h-5 mr-2 text-indigo-600" /> Profil Saya
                    </CardTitle>
                    <CardDescription>
                        Perbarui informasi dasar profil dan alamat email Anda.
                    </CardDescription>
                </CardHeader>
                <CardContent class="pt-6">
                    <form @submit.prevent="updateProfile" class="space-y-5">
                        
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1 uppercase tracking-wider">Nama Lengkap & Gelar</label>
                            <input type="text" v-model="formProfile.name" class="w-full border-slate-200 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <p v-if="formProfile.errors.name" class="mt-1 text-xs text-red-500">{{ formProfile.errors.name }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1 uppercase tracking-wider">Alamat Email</label>
                            <input type="email" v-model="formProfile.email" class="w-full border-slate-200 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <p v-if="formProfile.errors.email" class="mt-1 text-xs text-red-500">{{ formProfile.errors.email }}</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1 uppercase tracking-wider">Nomor WhatsApp</label>
                                <input type="text" v-model="formProfile.whatsapp_number" placeholder="Contoh: 081234567890" class="w-full border-slate-200 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <p v-if="formProfile.errors.whatsapp_number" class="mt-1 text-xs text-red-500">{{ formProfile.errors.whatsapp_number }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1 uppercase tracking-wider">Jenis Kelamin</label>
                                <select v-model="formProfile.gender" class="w-full border-slate-200 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                                <p v-if="formProfile.errors.gender" class="mt-1 text-xs text-red-500">{{ formProfile.errors.gender }}</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-end pt-4 border-t border-slate-100">
                            <Button type="submit" :disabled="formProfile.processing" class="bg-indigo-600 hover:bg-indigo-700 w-full sm:w-auto">
                                <Save class="w-4 h-4 mr-2" /> Simpan Profil
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>

            <Card class="border-slate-200 shadow-sm">
                <CardHeader class="bg-slate-50/50 border-b border-slate-100 pb-4">
                    <CardTitle class="text-lg flex items-center text-slate-800">
                        <Lock class="w-5 h-5 mr-2 text-rose-600" /> Keamanan Akun
                    </CardTitle>
                    <CardDescription>
                        Pastikan akun Anda menggunakan kata sandi yang panjang dan acak agar tetap aman.
                    </CardDescription>
                </CardHeader>
                <CardContent class="pt-6">
                    <form @submit.prevent="updatePassword" class="space-y-5">
                        
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1 uppercase tracking-wider">Password Saat Ini</label>
                            <input type="password" v-model="formPassword.current_password" class="w-full border-slate-200 rounded-lg text-sm focus:ring-rose-500 focus:border-rose-500">
                            <p v-if="formPassword.errors.current_password" class="mt-1 text-xs text-red-500">{{ formPassword.errors.current_password }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1 uppercase tracking-wider">Password Baru</label>
                            <input type="password" v-model="formPassword.password" class="w-full border-slate-200 rounded-lg text-sm focus:ring-rose-500 focus:border-rose-500">
                            <p v-if="formPassword.errors.password" class="mt-1 text-xs text-red-500">{{ formPassword.errors.password }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1 uppercase tracking-wider">Konfirmasi Password Baru</label>
                            <input type="password" v-model="formPassword.password_confirmation" class="w-full border-slate-200 rounded-lg text-sm focus:ring-rose-500 focus:border-rose-500">
                            <p v-if="formPassword.errors.password_confirmation" class="mt-1 text-xs text-red-500">{{ formPassword.errors.password_confirmation }}</p>
                        </div>

                        <div class="flex items-center justify-end pt-4 border-t border-slate-100">
                            <Button type="submit" :disabled="formPassword.processing" class="bg-slate-800 hover:bg-slate-900 w-full sm:w-auto">
                                <Save class="w-4 h-4 mr-2" /> Ubah Password
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>

        </div>

        <Transition
            enter-active-class="transition ease-out duration-300 transform"
            enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            enter-to-class="opacity-100 translate-y-0 sm:scale-100"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div 
                v-if="showToast" 
                class="fixed bottom-6 right-6 z-50 rounded-xl px-5 py-3.5 shadow-xl flex items-center gap-3 text-white max-w-sm border"
                :class="toastType === 'success' ? 'bg-emerald-600 border-emerald-500' : 'bg-rose-600 border-rose-500'"
            >
                <CheckCircle v-if="toastType === 'success'" class="w-5 h-5 shrink-0" />
                <XCircle v-else class="w-5 h-5 shrink-0" />
                <p class="font-medium text-sm">{{ toastMessage }}</p>
            </div>
        </Transition>

    </AuthenticatedLayout>
</template>