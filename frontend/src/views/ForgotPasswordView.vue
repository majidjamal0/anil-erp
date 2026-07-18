<script setup lang="ts">
import { ref } from 'vue'
import { api, csrf } from '../api/client'

const email = ref('')
const sent = ref(false)
const loading = ref(false)
const error = ref('')

async function submit() {
  loading.value = true
  error.value = ''

  try {
    await csrf()
    await api('/auth/forgot-password', {
      method: 'POST',
      body: JSON.stringify({ email: email.value }),
    })
    sent.value = true
  } catch (exception) {
    error.value = exception instanceof Error ? exception.message : 'ارسال لینک ناموفق بود'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <main class="auth-page">
    <form class="card" @submit.prevent="submit">
      <h1>بازیابی رمز عبور</h1>
      <p v-if="sent">اگر حساب وجود داشته باشد، لینک بازیابی ارسال شد.</p>
      <template v-else>
        <label>
          ایمیل
          <input v-model="email" type="email" required />
        </label>
        <p v-if="error" class="error" role="alert">{{ error }}</p>
        <button :disabled="loading">ارسال لینک</button>
      </template>
      <RouterLink to="/login">بازگشت به ورود</RouterLink>
    </form>
  </main>
</template>
