import { readFileSync } from 'node:fs'
import { describe, expect, it } from 'vitest'

describe('Persian RTL shell', () => {
  it('declares Persian RTL document direction', () => {
    const html = readFileSync(new URL('../index.html', import.meta.url), 'utf8')
    expect(html).toContain('<html lang="fa" dir="rtl">')
  })

  it('preserves RTL direction in the application stylesheet', () => {
    const css = readFileSync(new URL('./assets/main.css', import.meta.url), 'utf8')
    expect(css).toContain('direction: rtl')
  })
})
