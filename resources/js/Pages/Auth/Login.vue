<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

// Import komponen Shadcn yang sudah diinstall
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/Components/ui/card';
import { Label } from '@/Components/ui/label';
import { Input } from '@/Components/ui/input';
import { Button } from '@/Components/ui/button';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/Components/ui/dialog';
import { ref } from 'vue';
import { AlertCircle } from 'lucide-vue-next';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post('/login', {
        onFinish: () => form.reset('password'), // Otomatis kosongkan input password jika login gagal
    });
};

const resetForm = useForm({
    email: '',
});

const isResetModalOpen = ref(false);

const submitReset = () => {
    resetForm.post('/password-reset-request', {
        onSuccess: () => {
            isResetModalOpen.value = false;
            resetForm.reset();
        },
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <Card class="border-0 shadow-xl bg-white/70 backdrop-blur-sm lg:border lg:shadow-sm lg:bg-white lg:backdrop-blur-none">
            <CardHeader class="space-y-1">
                <CardTitle class="text-2xl font-bold tracking-tight">Selamat Datang</CardTitle>
                <CardDescription>
                    Masukkan email dan password untuk mengakses dashboard.
                </CardDescription>
                
                <div v-if="status" class="mb-4 font-medium text-sm text-green-600 bg-green-50 p-3 rounded-md border border-green-200">
                    {{ status }}
                </div>
            </CardHeader>
            
            <CardContent>
                <form @submit.prevent="submit" class="space-y-4">
                    <div v-if="form.errors.email" class="flex items-start gap-2 p-3 text-sm font-medium text-rose-600 bg-rose-50 rounded-md border border-rose-200">
                        <AlertCircle class="h-4 w-4 shrink-0 mt-0.5" />
                        <span>{{ form.errors.email }}</span>
                    </div>

                    <div class="space-y-2">
                        <Label for="email">Email</Label>
                        <Input 
                            id="email" 
                            type="email" 
                            v-model="form.email" 
                            placeholder="guru@sekolah.com" 
                            required 
                            autofocus 
                        />
                    </div>
                    
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <Label for="password">Password</Label>
                            
                            <Dialog v-model:open="isResetModalOpen">
                                <DialogTrigger as-child>
                                    <button type="button" class="text-sm text-blue-600 hover:text-blue-500 font-medium bg-transparent border-none p-0 cursor-pointer">Lupa password?</button>
                                </DialogTrigger>
                                <DialogContent class="sm:max-w-md">
                                    <DialogHeader>
                                        <DialogTitle>Reset Password Internal</DialogTitle>
                                        <DialogDescription>
                                            Masukkan email Anda untuk mengirim notifikasi permintaan reset password ke Admin Kurikulum.
                                        </DialogDescription>
                                    </DialogHeader>
                                    
                                    <form @submit.prevent="submitReset" class="space-y-4 py-4">
                                        <div class="space-y-2">
                                            <Label for="reset_email">Email Terdaftar</Label>
                                            <Input 
                                                id="reset_email" 
                                                type="email" 
                                                v-model="resetForm.email" 
                                                placeholder="guru@sekolah.com" 
                                                required 
                                            />
                                            <p v-if="resetForm.errors.email" class="text-xs font-medium text-red-500 mt-1">
                                                {{ resetForm.errors.email }}
                                            </p>
                                        </div>
                                        <DialogFooter>
                                            <Button type="submit" class="bg-blue-600 hover:bg-blue-700 w-full sm:w-auto" :disabled="resetForm.processing">
                                                {{ resetForm.processing ? 'Mengirim...' : 'Kirim Permintaan' }}
                                            </Button>
                                        </DialogFooter>
                                    </form>
                                </DialogContent>
                            </Dialog>
                        </div>
                        <Input 
                            id="password" 
                            type="password" 
                            v-model="form.password" 
                            required 
                        />
                        <p v-if="form.errors.password" class="text-xs font-medium text-red-500 mt-1">
                            {{ form.errors.password }}
                        </p>
                    </div>

                    <Button 
                        type="submit" 
                        class="w-full mt-6 bg-blue-600 hover:bg-blue-700 cursor-pointer disabled:cursor-not-allowed"
                        :disabled="form.processing"
                    >
                        {{ form.processing ? 'Sedang Masuk...' : 'Login' }}
                    </Button>
                </form>
            </CardContent>
        </Card>
    </GuestLayout>
</template>
