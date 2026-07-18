<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const email = ref('')
const password = ref('')
const error = ref('')
const auth = useAuthStore()
const router = useRouter()

async function submit() {
  error.value = ''

  try {
    await auth.login(email.value, password.value)
    await router.push('/')
  } catch (exception) {
    error.value = exception instanceof Error ? exception.message : 'ورود ناموفق بود'
  }
}
</script>

<template>
  <main class="auth-page">
    <form class="card" @submit.prevent="submit">
      <h1>ورود به آنیل</h1>
      <p>سامانه مدیریت یکپارچه سازمان</p>
      <label>
        ایمیل
        <input v-model="email" type="email" autocomplete="username" required />
      </label>
      <label>
        رمز عبور
        <input v-model="password" type="password" autocomplete="current-password" required />
      </label>
      <p v-if="error" class="error" role="alert">{{ error }}</p>
      <button :disabled="auth.loading">{{ auth.loading ? 'در حال ورود…' : 'ورود' }}</button>
      <RouterLink to="/forgot-password">رمز عبور را فراموش کرده‌اید؟</RouterLink>
    </form>
  </main>
</template>
