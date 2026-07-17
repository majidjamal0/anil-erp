<script setup lang="ts">
withDefaults(
  defineProps<{
    code?: string
    message?: string
  }>(),
  {
    code: '404',
    message: 'صفحه مورد نظر پیدا نشد.',
  },
)
</script>

<template>
  <main class="error-page">
    <h1>{{ code }}</h1>
    <p>{{ message }}</p>
    <RouterLink to="/">بازگشت به داشبورد</RouterLink>
  </main>
</template>
