const baseUrl = import.meta.env.VITE_API_URL ?? '/api'

export class ApiError extends Error {
  constructor(
    public status: number,
    public errors: Record<string, string[]> = {},
    message = 'خطایی رخ داد',
  ) {
    super(message)
  }
}

export async function api<T>(path: string, options: RequestInit = {}): Promise<T> {
  const response = await fetch(`${baseUrl}${path}`, {
    credentials: 'include',
    ...options,
    headers: {
      Accept: 'application/json',
      'Content-Type': 'application/json',
      ...(options.headers ?? {}),
    },
  })

  if (!response.ok) {
    const body = await response.json().catch(() => ({}))
    throw new ApiError(response.status, body.errors, body.message)
  }

  return response.status === 204 ? (undefined as T) : response.json()
}

export async function csrf(): Promise<void> {
  await fetch('/sanctum/csrf-cookie', { credentials: 'include' })
}
