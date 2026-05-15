<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/Components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table';
import { Badge } from '@/Components/ui/badge';
import { Button } from '@/Components/ui/button';
import { Key } from 'lucide-vue-next';

const props = defineProps({
    users: {
        type: Array,
        required: true,
    },
    status: {
        type: String,
    }
});

const resetPassword = (user) => {
    if (window.confirm(`Yakin reset password guru ${user.name} ini ke 'smansa123'?`)) {
        router.post(route('kurikulum.users-reset.reset', user.id), {}, {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Reset Password Guru" />

        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight">Manajemen Password Guru</h2>
                    <p class="text-sm text-slate-500">
                        Kelola permintaan reset password dari guru. Guru yang me-request reset akan berada di urutan paling atas.
                    </p>
                </div>
            </div>

            <div v-if="status" class="bg-green-50 text-green-700 p-4 rounded-md border border-green-200">
                {{ status }}
            </div>

            <Card class="border-0 shadow-sm">
                <CardHeader>
                    <CardTitle>Daftar Guru</CardTitle>
                    <CardDescription>
                        Berikut adalah daftar guru beserta status permintaan reset password mereka.
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="rounded-md border">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead class="w-[50px]">No</TableHead>
                                    <TableHead>Nama</TableHead>
                                    <TableHead>Email</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead class="text-right">Aksi</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="(user, index) in users" :key="user.id" :class="{'bg-red-50': user.is_requesting_password_reset}">
                                    <TableCell class="font-medium">{{ index + 1 }}</TableCell>
                                    <TableCell>{{ user.name }}</TableCell>
                                    <TableCell>{{ user.email }}</TableCell>
                                    <TableCell>
                                        <Badge 
                                            v-if="user.is_requesting_password_reset" 
                                            variant="destructive"
                                        >
                                            Meminta Reset
                                        </Badge>
                                        <Badge 
                                            v-else 
                                            variant="secondary"
                                            class="bg-slate-100 text-slate-500 border-slate-200"
                                        >
                                            Aman
                                        </Badge>
                                    </TableCell>
                                    <TableCell class="text-right">
                                        <Button 
                                            variant="outline" 
                                            size="sm" 
                                            class="text-blue-600 border-blue-200 hover:bg-blue-50"
                                            @click="resetPassword(user)"
                                            title="Reset Password"
                                        >
                                            <Key class="h-4 w-4 mr-2" />
                                            Reset
                                        </Button>
                                    </TableCell>
                                </TableRow>
                                <TableRow v-if="users.length === 0">
                                    <TableCell colspan="5" class="h-24 text-center">
                                        Tidak ada data guru.
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AuthenticatedLayout>
</template>
