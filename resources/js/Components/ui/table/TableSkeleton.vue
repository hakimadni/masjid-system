<script setup>
import DataTable from "@/Components/ui/table/DataTable.vue"
import SkeletonBlock from "@/Components/ui/skeleton/SkeletonBlock.vue"

defineProps({
  rows: { type: Number, default: 5 },
  columns: { type: Number, default: 4 },
})
</script>

<template>
  <DataTable>
    <thead class="bg-slate-50/80">
      <tr>
        <th v-for="column in columns" :key="column" class="px-4 py-3">
          <SkeletonBlock class="h-3 w-20" />
        </th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="row in rows" :key="row" class="border-t border-slate-100">
        <td v-for="column in columns" :key="`${row}-${column}`" class="px-4 py-4">
          <SkeletonBlock class="h-4 w-full max-w-[12rem]" />
        </td>
      </tr>
    </tbody>
  </DataTable>
</template>
