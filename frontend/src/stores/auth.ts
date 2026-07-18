import { defineStore } from 'pinia'
import { api, csrf } from '../api/client'

export interface User {
  id: string
  name: string
  email: string
  is_active: boolean
  roles: string[]
  permissions: string[]
}

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null as User | null,
    loading: false,
    initialized: false,
  }),
  getters: {
    authenticated: (state) => Boolean(state.user),
    can: (state) => (permission: string) =>
      state.user?.permissions?.includes(permission) ||
      state.user?.roles.includes('Super Admin') ||
      false,
  },
  actions: {
    async fetchUser() {
      this.loading = true

      try {
        const response = await api<{ data: User }>('/auth/user')
        this.user = response.data
      } catch {
        this.user = null
      } finally {
        this.loading = false
        this.initialized = true
      }
    },
    async login(email: string, password: string) {
      this.loading = true

      try {
        await csrf()
        const response = await api<{ data: User }>('/auth/login', {
          method: 'POST',
          body: JSON.stringify({ email, password }),
        })
        this.user = response.data
      } finally {
        this.loading = false
      }
    },
    async logout() {
      await api('/auth/logout', { method: 'POST' })
      this.user = null
    },
  },
})
