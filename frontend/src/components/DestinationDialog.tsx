import type { Destination } from '../types'
import { Dialog } from './Dialog'

export function DestinationDialog({ destination, saved, onToggle, onClose }: { destination: Destination; saved: boolean; onToggle: () => void; onClose: () => void }) {
  const maps = destination.latitude && destination.longitude ? `https://www.google.com/maps/search/?api=1&query=${destination.latitude},${destination.longitude}` : `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(`${destination.name}, ${destination.municipality.name}, Bukidnon, Philippines`)}`
  const share = async () => { const url = `${location.origin}${location.pathname}#spot=${destination.slug}`; try { await navigator.clipboard.writeText(url) } catch { window.prompt('Copy link:', url) } }
  return <Dialog onClose={onClose}>
    {destination.primary_photo && <div className="dialog-photo" style={{ backgroundImage: `url("${destination.primary_photo.url}")` }} role="img" aria-label={destination.primary_photo.alt_text}>{destination.primary_photo.verification_status !== 'verified' && <span>Illustrative image · unverified</span>}</div>}
    <div className="dialog-body"><div className="kicker">{destination.category.name} · {destination.municipality.name}, Bukidnon</div><h2 id="dialog-title">{destination.name}</h2>
      {destination.verification_status !== 'verified' && <div className="verification-note"><strong>Unverified listing.</strong> Confirm details with the cited operator or tourism office before travel.</div>}
      <p>{destination.description || 'A verified description has not been added yet.'}</p><h4>Experience highlights</h4><div className="pill-row">{destination.highlights.map(item => <span className="pill" key={item.id}>{item.label}</span>)}</div>
      {destination.access_notes && <div className="tip"><b>Before you go:</b> {destination.access_notes}</div>}
      <div className="dialog-actions"><a className="btn btn-dark" href={maps} target="_blank" rel="noopener noreferrer">Search in Maps ↗</a><button className="btn btn-outline" onClick={onToggle}>{saved ? '♥ Saved' : '♡ Save this spot'}</button><button className="btn btn-outline" onClick={share}>Share link</button></div>
      <div className="credit">Source: {destination.source_url ? <a href={destination.source_url} target="_blank" rel="noopener noreferrer">{destination.source_label || 'External source'}</a> : 'not yet recorded'} · Scenic image is illustrative.</div>
    </div>
  </Dialog>
}
