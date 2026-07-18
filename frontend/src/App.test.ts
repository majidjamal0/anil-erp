import { mount } from '@vue/test-utils'
import { describe, expect, it } from 'vitest'
import ErrorView from './views/ErrorView.vue'

describe('ErrorView', () => {
  it('renders localized authorization errors', () => {
    const wrapper = mount(ErrorView, {
      props: { code: '403', message: 'اجازه دسترسی ندارید' },
      global: { stubs: { RouterLink: true } },
    })

    expect(wrapper.text()).toContain('403')
    expect(wrapper.text()).toContain('اجازه دسترسی ندارید')
  })
})
