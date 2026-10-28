import type { AccountUser, AuditEvent, Category, Destination, Itinerary, Municipality, PaginatedResponse, StaffUser } from '../types'

export const API_URL = (import.meta.env.VITE_API_URL || 'http://localhost:8000/api').replace(/\/$/, '')

async function request<T>(path: string, options: RequestInit = {}, token?: string): Promise<T> {
  const isFormData = options.body instanceof FormData
  const response = await fetch(`${API_URL}${path}`, {
    ...options,
    headers: { Accept: 'application/json', ...(!isFormData ? { 'Content-Type': 'application/json' } : {}), ...(token ? { Authorization: `Bearer ${token}` } : {}), ...options.headers },
  })
  if (!response.ok) {
    const body = await response.json().catch(() => ({})) as { message?: string; errors?: Record<string, string[]> }
    throw new Error((body.errors && Object.values(body.errors).flat()[0]) || body.message || `Request failed (${response.status})`)
  }
  if (response.status === 204) return undefined as T
  return response.json() as Promise<T>
}

export const api = {
  destinations: () => request<PaginatedResponse<Destination>>('/destinations?per_page=48'),
  categories: () => request<{ data: Category[] }>('/categories'),
  municipalities: () => request<{ data: Municipality[] }>('/municipalities'),
  login: (email: string, password: string) => request<{ token: string; user: AccountUser }>('/admin/login', { method: 'POST', body: JSON.stringify({ email, password }) }),
  adminMe: (token: string) => request<{ user: AccountUser }>('/admin/me', {}, token),
  adminDestinations: (token: string) => request<PaginatedResponse<Destination>>('/admin/destinations', {}, token),
  saveDestination: (token: string, payload: Record<string, unknown>, slug?: string) => request<{ data: Destination }>(slug ? `/admin/destinations/${slug}` : '/admin/destinations', { method: slug ? 'PUT' : 'POST', body: JSON.stringify(payload) }, token),
  createUpdate: (token: string, slug: string, payload: Record<string, unknown>) => request(`/admin/destinations/${slug}/updates`, { method: 'POST', body: JSON.stringify(payload) }, token),
  uploadPhoto: (token: string, slug: string, payload: FormData) => request<{ data: Destination }>(`/admin/destinations/${slug}/photos`, { method: 'POST', body: payload }, token),
  reorderPhotos: (token: string, slug: string, photoIds: number[]) => request<{ data: Destination }>(`/admin/destinations/${slug}/photos/reorder`, { method: 'PUT', body: JSON.stringify({ photo_ids: photoIds }) }, token),
  deletePhoto: (token: string, slug: string, photoId: number) => request(`/admin/destinations/${slug}/photos/${photoId}`, { method: 'DELETE' }, token),
  auditEvents: (token: string) => request<PaginatedResponse<AuditEvent>>('/admin/audit-events', {}, token),
  staff: (token: string) => request<{ data: StaffUser[] }>('/admin/staff', {}, token),
  createStaff: (token: string, payload: Record<string, unknown>) => request<{ data: StaffUser }>('/admin/staff', { method: 'POST', body: JSON.stringify(payload) }, token),
  updateStaff: (token: string, id: number, payload: Record<string, unknown>) => request<{ data: StaffUser }>(`/admin/staff/${id}`, { method: 'PUT', body: JSON.stringify(payload) }, token),
  logout: (token: string) => request('/admin/logout', { method: 'POST' }, token),
  accountRegister: (payload: Record<string, string>) => request<{ token: string; user: AccountUser }>('/account/register', { method: 'POST', body: JSON.stringify(payload) }),
  accountLogin: (email: string, password: string) => request<{ token: string; user: AccountUser }>('/account/login', { method: 'POST', body: JSON.stringify({ email, password }) }),
  accountMe: (token: string) => request<{ user: AccountUser }>('/account/me', {}, token),
  accountLogout: (token: string) => request('/account/logout', { method: 'POST' }, token),
  cloudFavorites: (token: string) => request<{ data: string[] }>('/account/favorites', {}, token),
  syncFavorites: (token: string, slugs: string[]) => request<{ data: string[] }>('/account/favorites/sync', { method: 'POST', body: JSON.stringify({ slugs }) }, token),
  addFavorite: (token: string, slug: string) => request<{ data: string[] }>(`/account/favorites/${slug}`, { method: 'PUT' }, token),
  removeFavorite: (token: string, slug: string) => request<{ data: string[] }>(`/account/favorites/${slug}`, { method: 'DELETE' }, token),
  itineraries: (token: string) => request<{ data: Itinerary[] }>('/account/itineraries', {}, token),
  createItinerary: (token: string, payload: Record<string, unknown>) => request<{ data: Itinerary }>('/account/itineraries', { method: 'POST', body: JSON.stringify(payload) }, token),
  updateItinerary: (token: string, id: number, payload: Record<string, unknown>) => request<{ data: Itinerary }>(`/account/itineraries/${id}`, { method: 'PUT', body: JSON.stringify(payload) }, token),
  deleteItinerary: (token: string, id: number) => request(`/account/itineraries/${id}`, { method: 'DELETE' }, token),
  addItineraryStop: (token: string, id: number, slug: string) => request<{ data: Itinerary }>(`/account/itineraries/${id}/destinations/${slug}`, { method: 'POST', body: JSON.stringify({}) }, token),
  updateItineraryStop: (token: string, itineraryId: number, stopId: number, payload: Record<string, unknown>) => request<{ data: Itinerary }>(`/account/itineraries/${itineraryId}/stops/${stopId}`, { method: 'PUT', body: JSON.stringify(payload) }, token),
  reorderItineraryStops: (token: string, id: number, stopIds: number[]) => request<{ data: Itinerary }>(`/account/itineraries/${id}/stops/reorder`, { method: 'PUT', body: JSON.stringify({ stop_ids: stopIds }) }, token),
  removeItineraryStop: (token: string, itineraryId: number, stopId: number) => request<{ data: Itinerary }>(`/account/itineraries/${itineraryId}/stops/${stopId}`, { method: 'DELETE' }, token),
}
