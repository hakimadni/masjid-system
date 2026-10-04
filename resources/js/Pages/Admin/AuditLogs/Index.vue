<script setup>
import { Head, Link } from "@inertiajs/vue3"
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import Table from "@/Components/ui/table/DataTable.vue"
import Card from "@/Components/ui/card/Card.vue"
import CardContent from "@/Components/ui/card/CardContent.vue"
import CardHeader from "@/Components/ui/card/CardHeader.vue"
import CardTitle from "@/Components/ui/card/CardTitle.vue"

defineProps({
    filters: Object,
    logs: Object,
    actions: Array,
})
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Audit Log" />

        <Card>
            <CardHeader>
                <CardTitle>Riwayat Aktivitas</CardTitle>
            </CardHeader>
            <CardContent>
                <div class="overflow-x-auto">
                    <Table>
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-left">Waktu</th>
                                <th class="px-4 py-2 text-left">Aksi</th>
                                <th class="px-4 py-2 text-left">Entitas</th>
                                <th class="px-4 py-2 text-left">Pengguna</th>
                                <th class="px-4 py-2 text-left">IP Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="log in logs.data" :key="log.id" class="border-t">
                                <td class="px-4 py-2">{{ log.created_at }}</td>
                                <td class="px-4 py-2">{{ log.action }}</td>
                                <td class="px-4 py-2">{{ log.entity_type }} #{{ log.entity_id }}</td>
                                <td class="px-4 py-2">{{ log.user_name }}</td>
                                <td class="px-4 py-2">{{ log.ip_address ?? '-' }}</td>
                            </tr>
                            <tr v-if="!logs.data.length">
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">Belum ada riwayat aktivitas</td>
                            </tr>
                        </tbody>
                    </Table>
                </div>
            </CardContent>
        </Card>
    </AuthenticatedLayout>
</template>