<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { api } from '../../api/client'
const context = ref<Record<string, unknown> | null>(null)
const loading = ref(false)
const error = ref('')
onMounted(async () => {
  loading.value = true
  try {
    context.value = await api('/api/organization/context')
  } catch {
    error.value = 'خطا در دریافت دسترسی مؤثر.'
  } finally {
    loading.value = false
  }
})
</script>
<template>
  <section dir="rtl" class="access">
    <h1>دسترسی سازمانی کاربران</h1>
    <p>تخصیص شرکت، شعبه، انبار و سطح دسترسی با اعتبارسنجی سمت سرور انجام می‌شود.</p>
    <p v-if="loading">در حال بارگذاری…</p>
    <p v-else-if="error" class="error">{{ error }}</p>
    <pre v-else>{{ context }}</pre>
  </section>
</template>
<style scoped>
.access {
  display: grid;
  gap: 1rem;
}
.error {
  color: #b91c1c;
}
pre {
  white-space: pre-wrap;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 1rem;
  padding: 1rem;
  direction: ltr;
  text-align: left;
}
</style>
