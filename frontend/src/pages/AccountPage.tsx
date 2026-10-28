import { useState, type FormEvent } from 'react'
import { Link } from 'react-router-dom'
import { Brand } from '../components/Brand'
import { useAccount } from '../context/AccountContext'

export function AccountPage() {
  const { user, loading, login, register, logout } = useAccount()
  const [mode, setMode] = useState<'login' | 'register'>('login')
  const [form, setForm] = useState({ name: '', email: '', password: '', password_confirmation: '' })
  const [error, setError] = useState(''); const [busy, setBusy] = useState(false)
  const submit = async (event: FormEvent) => {
    event.preventDefault(); setBusy(true); setError('')
    try { if (mode === 'login') await login(form.email, form.password); else await register(form) }
    catch (err) { setError((err as Error).message) } finally { setBusy(false) }
  }
  if (loading) return <main className="account-shell"><div className="notice">Loading account…</div></main>
  return <main className="account-shell"><section className="login-card"><Link to="/"><Brand /></Link>
    {user ? <><div className="kicker">Traveler account</div><h1>Hello, {user.name}.</h1><p>Your favorites now merge with this account while the browser keeps a local copy.</p><Link className="btn btn-dark" to="/planner">Open trip planner</Link><button className="btn btn-outline account-signout" onClick={logout}>Sign out</button><Link className="back-link" to="/">← Return to BUKTRAVELS</Link></> : <><div className="auth-tabs"><button className={mode === 'login' ? 'active' : ''} onClick={() => setMode('login')}>Sign in</button><button className={mode === 'register' ? 'active' : ''} onClick={() => setMode('register')}>Create account</button></div><div className="kicker">Optional cloud sync</div><h1>{mode === 'login' ? 'Welcome back.' : 'Travel with your list.'}</h1><p>Favorites work without an account. Sign in only if you want them across devices or want to build itineraries.</p><form onSubmit={submit}>{mode === 'register' && <label>Name<input required value={form.name} onChange={event => setForm(current => ({ ...current, name: event.target.value }))} /></label>}<label>Email<input type="email" required autoComplete="email" value={form.email} onChange={event => setForm(current => ({ ...current, email: event.target.value }))} /></label><label>Password<input type="password" required minLength={12} autoComplete={mode === 'login' ? 'current-password' : 'new-password'} value={form.password} onChange={event => setForm(current => ({ ...current, password: event.target.value }))} /></label>{mode === 'register' && <label>Confirm password<input type="password" required minLength={12} autoComplete="new-password" value={form.password_confirmation} onChange={event => setForm(current => ({ ...current, password_confirmation: event.target.value }))} /></label>}{error && <div className="form-error" role="alert">{error}</div>}<button className="btn btn-dark" disabled={busy}>{busy ? 'Please wait…' : mode === 'login' ? 'Sign in' : 'Create account'}</button></form><Link className="back-link" to="/">← Continue without an account</Link></>}
  </section></main>
}
