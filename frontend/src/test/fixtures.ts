import type { Destination } from '../types'

const base = {
  address: null, latitude: null, longitude: null, source_url: 'https://example.test', source_label: 'Test source',
  status: 'published' as const, verification_status: 'unverified' as const, verified_at: null, published_at: '2026-10-27T00:00:00Z', updates: [], photos: [],
}
export const destinations: Destination[] = [
  { ...base, id: 1, slug: 'dahilayan', name: 'Dahilayan Adventure Park', summary: 'Mountain adventure rides.', description: 'Unverified adventure description.', access_notes: 'Check before travel.', municipality: { id: 1, name: 'Manolo Fortich', slug: 'manolo-fortich', type: 'municipality', verification_status: 'unverified' }, category: { id: 1, name: 'Adventure', slug: 'adventure' }, primary_photo: { id: 1, url: 'https://example.test/forest.jpg', alt_text: 'Illustrative placeholder', attribution: null, source_url: null, license: null, verification_status: 'unverified' }, highlights: [{ id: 1, label: 'Zipline', verification_status: 'unverified' }] },
  { ...base, id: 2, slug: 'lakeapo', name: 'Lake Apo', summary: 'A tranquil lake setting.', description: 'Unverified lake description.', access_notes: 'Confirm water activities.', municipality: { id: 2, name: 'Valencia City', slug: 'valencia-city', type: 'city', verification_status: 'unverified' }, category: { id: 2, name: 'Lakes', slug: 'lakes' }, primary_photo: null, highlights: [{ id: 2, label: 'Lake views', verification_status: 'unverified' }] },
]
