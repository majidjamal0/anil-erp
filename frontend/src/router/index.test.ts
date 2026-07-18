import { createPinia, setActivePinia } from 'pinia'
import { beforeEach, describe, expect, it } from 'vitest'
import { useAuthStore } from '../stores/auth'
import router from './index'

describe('route guards', () => {
  beforeEach(async () => {
    setActivePinia(createPinia())
    const auth = useAuthStore()
    auth.initialized = true
    await router.replace('/login')
  })

  it('redirects guests away from protected routes', async () => {
    await router.push('/users')
    expect(router.currentRoute.value.path).toBe('/login')
  })

  it('redirects unauthorized users to the 403 page', async () => {
    const auth = useAuthStore()
    auth.user = {
      id: '1',
      name: 'کاربر',
      email: 'user@example.com',
      is_active: true,
      roles: [],
      permissions: [],
    }

    await router.push('/users')
    expect(router.currentRoute.value.path).toBe('/unauthorized')
  })
})
