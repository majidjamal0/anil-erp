import { readFileSync } from 'node:fs'
import { resolve } from 'node:path'
import { describe, expect, it } from 'vitest'

const projectPath = (...segments: string[]) => resolve(process.cwd(), ...segments)

describe('Persian RTL shell', () => {
  it('declares Persian RTL document direction', () => {
    const html = readFileSync(projectPath('index.html'), 'utf8')

    expect(html).toContain('<html lang="fa" dir="rtl">')
  })

  it('preserves RTL direction in the application stylesheet', () => {
    const css = readFileSync(projectPath('src', 'assets', 'main.css'), 'utf8')

    expect(css).toContain('direction: rtl')
  })
})
