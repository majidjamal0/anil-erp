<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { api } from '../../api/client'

const props = defineProps<{
  resource: string
  title: string
  canEdit?: boolean
}>()

const items = ref<Record<string, unknown>[]>([])
const loading = ref(true)
const error = ref('')

async function load() {
  try {
    const response = await api<{ data: Record<string, unknown>[] }>(`/${props.resource}`)
    items.value = response.data
  } catch (exception) {
    error.value = exception instanceof Error ? exception.message : 'خطا در دریافت اطلاعات'
  } finally {
    loading.value = false
  }
}

onMounted(load)
</script>

<template>
  <section>
    <div class="page-head">
      <div>
        <h1>{{ title }}</h1>
        <p>مدیریت، مشاهده و کنترل دسترسی‌ها</p>
      </div>
      <button v-if="canEdit">افزودن</button>
    </div>

    <div v-if="loading" class="loading">در حال بارگذاری…</div>
    <p v-else-if="error" class="error">{{ error }}</p>
    <div v-else class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>نام</th>
            <th v-if="resource === 'users'">ایمیل</th>
            <th>وضعیت / دسترسی</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in items" :key="String(item.id)">
            <td>{{ item.name }}</td>
            <td v-if="resource === 'users'">{{ item.email }}</td>
            <td>
              <span class="badge">
                {{ resource === 'users' ? (item.is_active ? 'فعال' : 'غیرفعال') : 'مدیریت' }}
              </span>
            </td>
          </tr>
        </tbody>
      </table>
      <p v-if="!items.length">موردی یافت نشد.</p>
    </div>
  </section>
</template>
