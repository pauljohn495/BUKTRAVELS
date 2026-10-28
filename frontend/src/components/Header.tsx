import { useState } from 'react'
import { Link } from 'react-router-dom'
import { Brand } from './Brand'

export function Header({ savedCount, onSaved, accountName }: { savedCount: number; onSaved: () => void; accountName?: string }) {
  const [open, setOpen] = useState(false)
  return <header className="site-header"><nav className="wrap nav" aria-label="Primary navigation">
    <a href="#top"><Brand /></a>
    <button className="mobile-toggle" aria-expanded={open} aria-controls="nav-links" onClick={() => setOpen(!open)}>Menu</button>
    <div id="nav-links" className={`nav-links ${open ? 'open' : ''}`} onClick={() => setOpen(false)}>
      <a href="#destinations">Destinations</a><a href="#map">Map</a><a href="#trip-ideas">Trip ideas</a><Link to="/planner">Planner</Link><Link to="/account">{accountName || 'Account'}</Link>
      <button className="saved-link" onClick={onSaved}>Saved <b>{savedCount}</b></button>
    </div>
  </nav></header>
}
