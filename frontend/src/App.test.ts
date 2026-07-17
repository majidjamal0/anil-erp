import { createPinia } from 'pinia'
import { mount } from '@vue/test-utils'
import { describe, expect, it } from 'vitest'
import App from './App.vue'

describe('Persian application shell', () => {
  it('renders the product name in an RTL Persian root', () => {
    const wrapper = mount(App, {
      global: {
        plugins: [createPinia()],
        stubs: ['RouterView'],
      },
    })

    expect(wrapper.text()).toContain('انیل ERP')
    expect(wrapper.get('main').attributes()).toMatchObject({
      dir: 'rtl',
      lang: 'fa',
    })
  })
})
