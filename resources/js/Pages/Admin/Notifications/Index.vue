<script setup>
import { ref } from "vue"
import { Head, useForm } from "@inertiajs/vue3"
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import Card from "@/Components/ui/card/Card.vue"
import CardContent from "@/Components/ui/card/CardContent.vue"
import CardHeader from "@/Components/ui/card/CardHeader.vue"
import CardTitle from "@/Components/ui/card/CardTitle.vue"
import Button from "@/Components/ui/button/Button.vue"
import Textarea from "@/Components/ui/textarea/Textarea.vue"
import Select from "@/Components/ui/select/Select.vue"

const props = defineProps({
    templates: Object,
})

const form = useForm({
    type: '',
    date: '',
    time: '',
    location: '',
    speaker: '',
    bank_account: '',
    cattle_price: '',
    goat_price: '',
    deadline: '',
    period: '',
    pengumuman: '',
})

const generatedMessage = ref('')

const submit = () => {
    form.post(route('notifications.generate'), {
        preserveScroll: true,
        onSuccess: (response) => {
            generatedMessage.value = response.props.message
        },
    })
}
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Notifikasi" />

        <div class="grid gap-6 lg:grid-cols-2">
            <Card>
                <CardHeader>
                    <CardTitle>Generate Pesan Broadcast</CardTitle>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Jenis Notifikasi</label>
                            <select v-model="form.type" class="w-full border rounded px-3 py-2" required>
                                <option value="">Pilih Jenis</option>
                                <option value="kajian">Kajian Rutin</option>
                                <option value="jumat">Shalat Jumat</option>
                                <option value="donasi">Donasi Masjid</option>
                                <option value="qurban">Qurban</option>
                                <option value="zakat">Zakat</option>
                                <option value="emergency">Pengumuman Darurat</option>
                            </select>
                        </div>

                        <div v-if="form.type === 'kajian'">
                            <label class="block text-sm font-medium mb-1">Tanggal</label>
                            <input v-model="form.date" type="date" class="w-full border rounded px-3 py-2" />
                            <label class="block text-sm font-medium mb-1 mt-2">Waktu</label>
                            <input v-model="form.time" type="text" placeholder="19:00" class="w-full border rounded px-3 py-2" />
                            <label class="block text-sm font-medium mb-1 mt-2">Tempat</label>
                            <input v-model="form.location" type="text" class="w-full border rounded px-3 py-2" />
                            <label class="block text-sm font-medium mb-1 mt-2">Ustadz</label>
                            <input v-model="form.speaker" type="text" class="w-full border rounded px-3 py-2" />
                        </div>

                        <div v-if="form.type === 'donasi'">
                            <label class="block text-sm font-medium mb-1">Rekening Bank</label>
                            <input v-model="form.bank_account" type="text" placeholder="BRI 1234-5678-9012-3456" class="w-full border rounded px-3 py-2" />
                        </div>

                        <div v-if="form.type === 'qurban'">
                            <label class="block text-sm font-medium mb-1">Harga Sapi</label>
                            <input v-model="form.cattle_price" type="text" placeholder="Rp 2.500.000" class="w-full border rounded px-3 py-2" />
                            <label class="block text-sm font-medium mb-1 mt-2">Harga Kambing</label>
                            <input v-model="form.goat_price" type="text" placeholder="Rp 1.800.000" class="w-full border rounded px-3 py-2" />
                            <label class="block text-sm font-medium mb-1 mt-2">Batas Pendaftaran</label>
                            <input v-model="form.deadline" type="text" placeholder="30 Juni 2026" class="w-full border rounded px-3 py-2" />
                        </div>

                        <div v-if="form.type === 'zakat'">
                            <label class="block text-sm font-medium mb-1">Periode Zakat</label>
                            <input v-model="form.period" type="text" placeholder="10-15 Ramadan 1447 H" class="w-full border rounded px-3 py-2" />
                            <label class="block text-sm font-medium mb-1 mt-2">Lokasi</label>
                            <input v-model="form.location" type="text" class="w-full border rounded px-3 py-2" />
                        </div>

                        <div v-if="form.type === 'emergency'">
                            <label class="block text-sm font-medium mb-1">Isi Pengumuman</label>
                            <textarea v-model="form.pengumuman" rows="4" class="w-full border rounded px-3 py-2"></textarea>
                        </div>

                        <Button type="submit" :disabled="form.processing">Generate Pesan</Button>
                    </form>
                </CardContent>
            </Card>

            <Card v-if="generatedMessage">
                <CardHeader>
                    <CardTitle>Pesan Siap Salin</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="bg-gray-50 p-4 rounded border whitespace-pre-wrap">{{ generatedMessage }}</div>
                    <Button class="mt-3" @click="navigator.clipboard.writeText(generatedMessage)">Salin ke Clipboard</Button>
                </CardContent>
            </Card>
        </div>
    </AuthenticatedLayout>
</template>