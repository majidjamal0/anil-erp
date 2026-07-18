import { createPinia, setActivePinia } from 'pinia'
import { beforeEach, describe, expect, it, vi } from 'vitest'
import { useAuthStore } from './auth'

const user = {
  id: '1',
  name: 'مدیر',
  email: 'admin@example.com',
  is_active: true,
  roles: ['Administrator'],
  permissions: ['users.view'],
}

describe('auth store', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
    vi.stubGlobal('fetch', vi.fn())
  })

  it('loads the current user and checks permissions', async () => {
    vi.mocked(fetch).mockResolvedValueOnce(
      new Response(JSON.stringify({ data: user }), { status: 200 }),
    )
    const store = useAuthStore()

    await store.fetchUser()

    expect(store.authenticated).toBe(true)
    expect(store.can('users.view')).toBe(true)
    expect(store.initialized).toBe(true)
  })

  it('clears the user after logout', async () => {
    vi.mocked(fetch).mockResolvedValueOnce(new Response('{}', { status: 200 }))
    const store = useAuthStore()
    store.user = user

    await store.logout()

    expect(store.user).toBeNull()
  })
})
