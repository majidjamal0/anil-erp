<script setup lang="ts">
import { computed, onMounted, reactive, ref } from 'vue'
import { api } from '../../api/client'

const props = defineProps<{ resource: string; title: string; permission: string }>()
const rows = ref<Record<string, unknown>[]>([])
const loading = ref(false)
const error = ref('')
const filters = reactive({ q: '', is_active: '', type: '' })
const endpoint = computed(() => `/api/${props.resource}`)

async function load() {
  loading.value = true
  error.value = ''
  try {
    const params = [
      filters.q ? `q=${encodeURIComponent(filters.q)}` : '',
      filters.is_active ? `is_active=${encodeURIComponent(filters.is_active)}` : '',
      filters.type ? `type=${encodeURIComponent(filters.type)}` : '',
    ]
      .filter(Boolean)
      .join('&')
    const response = (await api(`${endpoint.value}?${params}`)) as
      | { data?: Record<string, unknown>[] }
      | Record<string, unknown>[]
    rows.value = Array.isArray(response) ? response : (response.data ?? [])
  } catch {
    error.value = 'خطا در دریافت داده‌های سازمانی.'
  } finally {
    loading.value = false
  }
}

onMounted(load)
</script>

<template>
  <section class="org-page" dir="rtl">
    <header class="page-header">
      <div>
        <p class="eyebrow">سازمان</p>
        <h1>{{ title }}</h1>
      </div>
      <button type="button" @click="load">به‌روزرسانی</button>
    </header>

    <form class="filters" @submit.prevent="load">
      <label>
        جستجو
        <input v-model="filters.q" type="search" placeholder="نام یا کد" />
      </label>
      <label>
        وضعیت
        <select v-model="filters.is_active">
          <option value="">همه</option>
          <option value="1">فعال</option>
          <option value="0">غیرفعال</option>
        </select>
      </label>
      <label>
        نوع
        <input v-model="filters.type" type="text" placeholder="اختیاری" />
      </label>
      <button type="submit">اعمال فیلتر</button>
    </form>

    <p v-if="loading" class="state">در حال بارگذاری…</p>
    <p v-else-if="error" class="error">{{ error }}</p>
    <p v-else-if="rows.length === 0" class="state">موردی یافت نشد.</p>
    <div v-else class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>نام</th>
            <th>کد</th>
            <th>نوع</th>
            <th>وضعیت</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="row in rows" :key="String(row.id)">
            <td>{{ row.name }}</td>
            <td>{{ row.code }}</td>
            <td>{{ row.type ?? '—' }}</td>
            <td>
              <span :class="['badge', row.is_active ? 'ok' : 'off']">{{
                row.is_active ? 'فعال' : 'غیرفعال'
              }}</span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </section>
</template>

<style scoped>
.org-page {
  display: grid;
  gap: 1rem;
}
.page-header,
.filters {
  display: flex;
  flex-wrap: wrap;
  align-items: end;
  justify-content: space-between;
  gap: 1rem;
}
.eyebrow {
  color: #64748b;
  margin: 0;
}
.filters label {
  display: grid;
  gap: 0.35rem;
  font-weight: 700;
}
input,
select,
button {
  border: 1px solid #cbd5e1;
  border-radius: 0.75rem;
  padding: 0.7rem 0.9rem;
}
button {
  background: #0f766e;
  color: white;
  cursor: pointer;
}
.table-wrap {
  overflow-x: auto;
  background: white;
  border-radius: 1rem;
  border: 1px solid #e2e8f0;
}
table {
  border-collapse: collapse;
  min-width: 720px;
  width: 100%;
}
th,
td {
  padding: 0.9rem;
  text-align: right;
  border-bottom: 1px solid #e2e8f0;
}
.badge {
  border-radius: 999px;
  padding: 0.2rem 0.6rem;
}
.ok {
  background: #dcfce7;
  color: #166534;
}
.off {
  background: #fee2e2;
  color: #991b1b;
}
.error {
  color: #b91c1c;
}
.state {
  color: #475569;
}
</style>
