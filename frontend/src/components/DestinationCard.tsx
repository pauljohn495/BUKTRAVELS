import type { Destination } from '../types'

export function DestinationCard({ destination, saved, onToggle, onOpen }: { destination: Destination; saved: boolean; onToggle: () => void; onOpen: () => void }) {
  return <article className="destination-card">
    <div className="card-image" style={destination.primary_photo ? { backgroundImage: `url("${destination.primary_photo.url}")` } : undefined}>
      <span className="sr-only">{destination.primary_photo?.alt_text || 'No verified destination photo available'}</span>
      <span className="badge">{destination.category.name}</span>{destination.primary_photo?.verification_status !== 'verified' && <span className="unverified-badge">Image unverified</span>}
      <button className={`heart ${saved ? 'saved' : ''}`} aria-label={`${saved ? 'Remove' : 'Save'} ${destination.name}`} aria-pressed={saved} onClick={onToggle}>{saved ? '♥' : '♡'}</button>
    </div>
    <div className="card-body"><div className="town">⌖ &nbsp; {destination.municipality.name}</div><h3>{destination.name}</h3><p>{destination.summary || 'Details are awaiting verification.'}</p><div className="card-footer"><span>{destination.verification_status}</span><button onClick={onOpen}>View details →</button></div></div>
  </article>
}
