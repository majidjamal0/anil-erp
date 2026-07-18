<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const sidebarOpen = ref(false)
const auth = useAuthStore()
const router = useRouter()

async function logout() {
  await auth.logout()
  await router.push('/login')
}
</script>

<template>
  <div class="shell">
    <aside :class="{ open: sidebarOpen }">
      <div class="brand">آنیل ERP</div>
      <nav>
        <RouterLink to="/">داشبورد</RouterLink>
        <RouterLink v-if="auth.can('users.view')" to="/users">کاربران</RouterLink>
        <RouterLink v-if="auth.can('roles.manage')" to="/roles">نقش‌ها</RouterLink>
        <RouterLink v-if="auth.can('permissions.manage')" to="/permissions"> مجوزها </RouterLink>
      </nav>
    </aside>

    <div class="content">
      <header class="topbar">
        <button
          class="menu"
          type="button"
          aria-label="باز کردن منو"
          @click="sidebarOpen = !sidebarOpen"
        >
          ☰
        </button>
        <span>{{ auth.user?.name }}</span>
        <button type="button" @click="logout">خروج</button>
      </header>
      <main>
        <RouterView />
      </main>
    </div>

    <button
      v-if="sidebarOpen"
      class="scrim"
      type="button"
      aria-label="بستن منو"
      @click="sidebarOpen = false"
    />
  </div>
</template>
