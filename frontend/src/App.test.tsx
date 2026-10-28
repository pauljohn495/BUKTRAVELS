import { render, screen, within } from '@testing-library/react'
import userEvent from '@testing-library/user-event'
import { MemoryRouter } from 'react-router-dom'
import { vi } from 'vitest'
import App from './App'
import { AccountProvider } from './context/AccountContext'
import { destinations } from './test/fixtures'

function mockDestinations() {
  vi.stubGlobal('fetch', vi.fn(async () => new Response(JSON.stringify({ data: destinations }), { status: 200, headers: { 'Content-Type': 'application/json' } })))
}

function renderApp(path = '/') {
  return render(<MemoryRouter initialEntries={[path]}><AccountProvider><App /></AccountProvider></MemoryRouter>)
}

describe('visitor experience', () => {
  it('searches destinations and opens a detail dialog', async () => {
    mockDestinations()
    const user = userEvent.setup()
    renderApp()

    expect(await screen.findByRole('heading', { name: 'Dahilayan Adventure Park' })).toBeInTheDocument()
    expect(screen.getByText(/map ready for verified coordinates/i)).toBeInTheDocument()
    await user.type(screen.getByPlaceholderText(/search destinations/i), 'lake')
    expect(screen.queryByRole('heading', { name: 'Dahilayan Adventure Park' })).not.toBeInTheDocument()
    const lakeHeading = screen.getByRole('heading', { name: 'Lake Apo' })
    await user.click(within(lakeHeading.closest('article')!).getByRole('button', { name: /view details/i }))
    expect(within(screen.getByRole('dialog')).getByText(/unverified listing/i)).toBeInTheDocument()
    expect(within(screen.getByRole('dialog')).getByRole('link', { name: /search in maps/i })).toHaveAttribute('href', expect.stringContaining('google.com/maps/search'))
  })

  it('keeps favorites in browser storage before login', async () => {
    mockDestinations()
    const user = userEvent.setup()
    renderApp()

    const heading = await screen.findByRole('heading', { name: 'Dahilayan Adventure Park' })
    await user.click(within(heading.closest('article')!).getByRole('button', { name: 'Save Dahilayan Adventure Park' }))
    expect(JSON.parse(localStorage.getItem('buktravels-saved') || '[]')).toContain('dahilayan')
    expect(screen.getByRole('button', { name: 'Remove Dahilayan Adventure Park' })).toHaveAttribute('aria-pressed', 'true')
  })
})

describe('admin experience', () => {
  it('shows the protected sign-in screen without an admin token', () => {
    renderApp('/admin')
    expect(screen.getByRole('heading', { name: 'Staff sign in' })).toBeInTheDocument()
    expect(screen.getByText(/editors draft content/i)).toBeInTheDocument()
  })
})

describe('traveler account', () => {
  it('keeps account creation optional and explains cloud sync', async () => {
    const user = userEvent.setup()
    renderApp('/account')
    expect(screen.getByText(/favorites work without an account/i)).toBeInTheDocument()
    await user.click(screen.getByRole('button', { name: 'Create account' }))
    expect(screen.getByRole('heading', { name: 'Travel with your list.' })).toBeInTheDocument()
    expect(screen.getByLabelText('Confirm password')).toBeInTheDocument()
  })
})
