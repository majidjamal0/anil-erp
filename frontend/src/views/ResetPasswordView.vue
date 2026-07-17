<script setup lang="ts">
import { ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { api, csrf } from '../api/client'

const route = useRoute()
const router = useRouter()
const email = ref(String(route.query.email ?? ''))
const password = ref('')
const confirmation = ref('')
const error = ref('')
const loading = ref(false)

async function submit() {
  loading.value = true
  error.value = ''

  try {
    await csrf()
    await api('/auth/reset-password', {
      method: 'POST',
      body: JSON.stringify({
        token: route.query.token,
        email: email.value,
        password: password.value,
        password_confirmation: confirmation.value,
      }),
    })
    await router.push('/login')
  } catch (exception) {
    error.value = exception instanceof Error ? exception.message : 'بازیابی ناموفق بود'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <main class="auth-page">
    <form class="card" @submit.prevent="submit">
      <h1>تنظیم رمز عبور</h1>
      <label>ایمیل <input v-model="email" type="email" required /></label>
      <label>
        رمز عبور جدید
        <input v-model="password" type="password" minlength="8" required />
      </label>
      <label>
        تکرار رمز عبور
        <input v-model="confirmation" type="password" required />
      </label>
      <p v-if="error" class="error" role="alert">{{ error }}</p>
      <button :disabled="loading">ذخیره رمز عبور</button>
    </form>
  </main>
</template>
