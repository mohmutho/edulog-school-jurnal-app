<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

// Import komponen Shadcn yang sudah diinstall
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/Components/ui/card';
import { Label } from '@/Components/ui/label';
import { Input } from '@/Components/ui/input';
import { Button } from '@/Components/ui/button';

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

// 2. Ubah fungsi submit menjadi aksi POST ke backend Laravel
const submit = () => {
    form.post('/login', {
        onFinish: () => form.reset('password'), // Otomatis kosongkan input password jika login gagal
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
            </CardHeader>
            
            <CardContent>
                <form @submit.prevent="submit" class="space-y-4">
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
                            <a href="#" class="text-sm text-blue-600 hover:text-blue-500 font-medium">Lupa password?</a>
                        </div>
                        <Input 
                            id="password" 
                            type="password" 
                            v-model="form.password" 
                            required 
                        />
                    </div>

                    <Button type="submit" class="w-full mt-6 bg-blue-600 hover:bg-blue-700">
                        <Link>
                            Login
                        </Link>
                    </Button>
                </form>
            </CardContent>
        </Card>
    </GuestLayout>
</template>
