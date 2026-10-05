import re

with open('/Users/user/Work/masjid-system/resources/js/Pages/Admin/Finance/Index.vue', 'r') as f:
    content = f.read()

# Add formatCurrency function
format_currency_func = """
const formatCurrency = (value) =>
  new Intl.NumberFormat("id-ID", {
    style: "currency",
    currency: "IDR",
    maximumFractionDigits: 0,
  }).format(Number(value || 0))
"""

if 'const formatCurrency' not in content:
    content = content.replace('const summaryCards = computed(() => [', format_currency_func + '\nconst summaryCards = computed(() => [')

# Update summaryCards mapping
content = content.replace(
    '''const summaryCards = computed(() => [
  { title: "Total Uang Masuk", value: props.summary.income_total, type: "currency" },
  { title: "Total Uang Keluar", value: props.summary.expense_total, type: "currency" },
  { title: "Transaksi Pending", value: props.summary.pending_total, type: "plain" },
  { title: "Total Catatan", value: props.summary.records_total, type: "plain" },
])''',
    '''const summaryCards = computed(() => [
  { title: "Total Uang Masuk", value: formatCurrency(props.summary.income_total) },
  { title: "Total Uang Keluar", value: formatCurrency(props.summary.expense_total) },
  { title: "Transaksi Pending", value: props.summary.pending_total },
  { title: "Total Catatan", value: props.summary.records_total },
])'''
)

# Update StatCard usage
old_stat_card = """<StatCard
          v-for="card in summaryCards"
          :key="card.title"
          :title="card.title"
          :value="card.type === 'currency' ? undefined : card.value"
        >
          <template v-if="card.type === 'currency'" #value>
            <MoneyDisplay :value="card.value" />
          </template>
        </StatCard>"""

new_stat_card = """<StatCard
          v-for="card in summaryCards"
          :key="card.title"
          :title="card.title"
          :value="card.value"
        />"""

content = content.replace(old_stat_card, new_stat_card)

with open('/Users/user/Work/masjid-system/resources/js/Pages/Admin/Finance/Index.vue', 'w') as f:
    f.write(content)

