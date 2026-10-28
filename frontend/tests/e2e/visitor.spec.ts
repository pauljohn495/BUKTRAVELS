import AxeBuilder from '@axe-core/playwright'
import { expect, test } from '@playwright/test'

const destinations = [
  {
    id: 1, slug: 'dahilayan', name: 'Dahilayan Adventure Park', summary: 'Mountain adventure rides.', description: 'Unverified description.',
    address: null, latitude: null, longitude: null, access_notes: 'Check current access.', source_url: 'https://example.test', source_label: 'Source',
    status: 'published', verification_status: 'unverified', verified_at: null, published_at: '2026-10-27T00:00:00Z',
    municipality: { id: 1, name: 'Manolo Fortich', slug: 'manolo-fortich', type: 'municipality', verification_status: 'unverified' },
    category: { id: 1, name: 'Adventure', slug: 'adventure' }, primary_photo: null, photos: [], highlights: [{ id: 1, label: 'Adventure', verification_status: 'unverified' }], updates: [],
  },
  {
    id: 2, slug: 'lakeapo', name: 'Lake Apo', summary: 'A tranquil lake.', description: 'Unverified description.',
    address: null, latitude: null, longitude: null, access_notes: 'Confirm water access.', source_url: 'https://example.test', source_label: 'Source',
    status: 'published', verification_status: 'unverified', verified_at: null, published_at: '2026-10-27T00:00:00Z',
    municipality: { id: 2, name: 'Valencia City', slug: 'valencia-city', type: 'city', verification_status: 'unverified' },
    category: { id: 2, name: 'Lakes', slug: 'lakes' }, primary_photo: null, photos: [], highlights: [{ id: 2, label: 'Lake views', verification_status: 'unverified' }], updates: [],
  },
]

test.beforeEach(async ({ page }) => {
  await page.route('**/api/destinations?**', route => route.fulfill({ json: { data: destinations } }))
})

test('search, favorites and details work at desktop and mobile sizes', async ({ page }) => {
  await page.goto('/')
  await expect(page.getByRole('heading', { name: 'Dahilayan Adventure Park' })).toBeVisible()
  await page.getByPlaceholder('Search destinations, towns or experiences').fill('lake')
  await expect(page.getByRole('heading', { name: 'Lake Apo' })).toBeVisible()
  await expect(page.getByRole('heading', { name: 'Dahilayan Adventure Park' })).toBeHidden()
  await page.getByRole('button', { name: 'Save Lake Apo' }).click()
  await expect(page.getByRole('button', { name: 'Remove Lake Apo' })).toHaveAttribute('aria-pressed', 'true')
  await page.getByRole('button', { name: 'View details' }).click()
  await expect(page.getByRole('dialog')).toContainText('Unverified listing')
})

test('home page has no automatically detectable accessibility violations', async ({ page }) => {
  await page.goto('/')
  await expect(page.getByRole('heading', { name: 'Dahilayan Adventure Park' })).toBeVisible()
  const results = await new AxeBuilder({ page }).analyze()
  const violations = results.violations.map(({ id, impact, nodes }) => ({
    id,
    impact,
    targets: nodes.map(node => node.target.join(' ')),
  }))
  expect(violations).toEqual([])
})

test('signed-in traveler can load a private itinerary', async ({ page }) => {
  await page.addInitScript(() => localStorage.setItem('buktravels-account-token', 'test-token'))
  await page.route('**/api/account/me', route => route.fulfill({ json: { user: { id: 8, name: 'Traveler', email: 'traveler@example.test', role: 'visitor' } } }))
  await page.route('**/api/account/itineraries', route => route.fulfill({ json: { data: [{
    id: 9, name: 'Weekend plan', start_date: '2026-11-07', notes: 'Confirm access.', created_at: '2026-10-28T00:00:00Z', updated_at: '2026-10-28T00:00:00Z',
    stops: [{ id: 10, position: 1, visit_date: '2026-11-07', notes: 'Morning option.', destination: { id: 2, slug: 'lakeapo', name: 'Lake Apo', latitude: null, longitude: null, verification_status: 'unverified', municipality: 'Valencia City', category: 'Lakes' } }],
  }] } }))
  await page.goto('/planner')
  await expect(page.getByRole('heading', { name: 'Weekend plan' })).toBeVisible()
  await expect(page.getByRole('heading', { name: 'Lake Apo' })).toBeVisible()
  await expect(page.getByText('Route unlocks when all stop coordinates are verified.')).toBeVisible()
})
