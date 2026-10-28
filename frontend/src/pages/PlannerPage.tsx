import { useEffect, useMemo, useState, type FormEvent } from 'react'
import { Link } from 'react-router-dom'
import { api } from '../api/client'
import { Brand } from '../components/Brand'
import { useAccount } from '../context/AccountContext'
import type { Destination, Itinerary, ItineraryStop } from '../types'

function directionsUrl(stops: ItineraryStop[]) {
  const verified = stops.length > 0 && stops.every(stop => stop.destination.verification_status === 'verified' && stop.destination.latitude && stop.destination.longitude)
  if (!verified) return ''
  const points = stops.map(stop => `${stop.destination.latitude},${stop.destination.longitude}`)
  if (points.length === 1) return `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(points[0])}`
  const params = new URLSearchParams({ api: '1', origin: points[0], destination: points.at(-1) as string, travelmode: 'driving' })
  if (points.length > 2) params.set('waypoints', points.slice(1, -1).join('|'))
  return `https://www.google.com/maps/dir/?${params}`
}

function StopEditor({ stop, onSave }: { stop: ItineraryStop; onSave: (visitDate: string, notes: string) => Promise<void> }) {
  const [visitDate, setVisitDate] = useState(stop.visit_date || '')
  const [notes, setNotes] = useState(stop.notes || '')
  const [busy, setBusy] = useState(false)
  return <div className="stop-fields"><label>Visit date<input type="date" value={visitDate} onChange={event => setVisitDate(event.target.value)} /></label><label>Notes<textarea rows={2} value={notes} onChange={event => setNotes(event.target.value)} /></label><button className="btn btn-outline" disabled={busy} onClick={async () => { setBusy(true); try { await onSave(visitDate, notes) } finally { setBusy(false) } }}>Save stop</button></div>
}

export function PlannerPage() {
  const { user, token, loading: accountLoading } = useAccount()
  const [itineraries, setItineraries] = useState<Itinerary[]>([])
  const [destinations, setDestinations] = useState<Destination[]>([])
  const [selectedId, setSelectedId] = useState<number | null>(null)
  const [newName, setNewName] = useState('')
  const [newDate, setNewDate] = useState('')
  const [addSlug, setAddSlug] = useState('')
  const [error, setError] = useState('')
  const [message, setMessage] = useState('')
  const [busy, setBusy] = useState(false)

  useEffect(() => {
    if (!token) return
    Promise.all([api.itineraries(token), api.destinations()]).then(([plans, places]) => {
      setItineraries(plans.data); setDestinations(places.data); setSelectedId(current => current ?? plans.data[0]?.id ?? null)
    }).catch(err => setError(err.message))
  }, [token])

  const selected = itineraries.find(item => item.id === selectedId) || null
  const available = useMemo(() => destinations.filter(destination => !selected?.stops.some(stop => stop.destination.slug === destination.slug)), [destinations, selected])
  const replace = (updated: Itinerary) => { setItineraries(current => current.map(item => item.id === updated.id ? updated : item)); setMessage('Itinerary saved.'); setError('') }
  const run = async (task: () => Promise<void>) => { setBusy(true); setError(''); setMessage(''); try { await task() } catch (err) { setError((err as Error).message) } finally { setBusy(false) } }

  const create = (event: FormEvent) => { event.preventDefault(); run(async () => { const result = await api.createItinerary(token, { name: newName, start_date: newDate || null, notes: null }); setItineraries(current => [result.data, ...current]); setSelectedId(result.data.id); setNewName(''); setNewDate(''); setMessage('Itinerary created.') }) }
  const saveDetails = (event: FormEvent<HTMLFormElement>) => { event.preventDefault(); if (!selected) return; const data = new FormData(event.currentTarget); run(async () => replace((await api.updateItinerary(token, selected.id, { name: data.get('name'), start_date: data.get('start_date') || null, notes: data.get('notes') || null })).data)) }
  const removePlan = () => { if (!selected || !window.confirm(`Delete “${selected.name}” and all its stops?`)) return; run(async () => { await api.deleteItinerary(token, selected.id); const remaining = itineraries.filter(item => item.id !== selected.id); setItineraries(remaining); setSelectedId(remaining[0]?.id ?? null); setMessage('Itinerary deleted.') }) }
  const addStop = (event: FormEvent) => { event.preventDefault(); if (!selected || !addSlug) return; run(async () => { replace((await api.addItineraryStop(token, selected.id, addSlug)).data); setAddSlug('') }) }
  const move = (index: number, direction: -1 | 1) => { if (!selected) return; const stops = [...selected.stops]; const target = index + direction; if (target < 0 || target >= stops.length) return; [stops[index], stops[target]] = [stops[target], stops[index]]; run(async () => replace((await api.reorderItineraryStops(token, selected.id, stops.map(stop => stop.id))).data)) }
  const route = selected ? directionsUrl(selected.stops) : ''

  if (accountLoading) return <main className="planner-empty">Loading planner…</main>
  if (!user) return <main className="account-shell"><section className="login-card"><Link to="/"><Brand /></Link><div className="kicker">Account-only workspace</div><h1>Plan your trip.</h1><p>Sign in to save ordered stops, dates and private notes. Favorites still work without an account.</p><Link className="btn btn-dark" to="/account">Sign in or create account</Link><Link className="back-link" to="/">← Return to destinations</Link></section></main>

  return <main className="planner-page"><header className="planner-header"><Link to="/"><Brand /></Link><nav><Link to="/">Destinations</Link><Link to="/account">Account</Link></nav></header><div className="planner-shell">
    <aside className="planner-sidebar"><div className="kicker">Your journeys</div><h1>Trip planner</h1><div className="plan-tabs">{itineraries.map(plan => <button className={plan.id === selectedId ? 'active' : ''} key={plan.id} onClick={() => setSelectedId(plan.id)}><b>{plan.name}</b><span>{plan.stops.length} stop{plan.stops.length === 1 ? '' : 's'}</span></button>)}</div><form className="new-plan" onSubmit={create}><h2>New itinerary</h2><label>Name<input required maxLength={120} value={newName} onChange={event => setNewName(event.target.value)} placeholder="Bukidnon weekend" /></label><label>Start date<input type="date" value={newDate} onChange={event => setNewDate(event.target.value)} /></label><button className="btn btn-dark" disabled={busy}>Create itinerary</button></form></aside>
    <section className="planner-content">{error && <div className="form-error" role="alert">{error}</div>}{message && <div className="form-success" role="status">{message}</div>}{!selected ? <div className="planner-welcome"><div className="kicker">Start with a name</div><h2>Build a practical travel plan.</h2><p>Add destinations in order, then assign dates and notes. Route links remain unavailable until every location has manually verified coordinates.</p></div> : <><form className="plan-details" key={`${selected.id}-${selected.updated_at}`} onSubmit={saveDetails}><div><div className="kicker">Itinerary details</div><h2>{selected.name}</h2></div><label>Name<input name="name" required maxLength={120} defaultValue={selected.name} /></label><label>Start date<input name="start_date" type="date" defaultValue={selected.start_date || ''} /></label><label className="notes-field">Private notes<textarea name="notes" rows={3} defaultValue={selected.notes || ''} /></label><div className="plan-actions"><button className="btn btn-dark" disabled={busy}>Save details</button><button className="btn btn-outline danger" type="button" onClick={removePlan}>Delete itinerary</button>{route ? <a className="btn btn-outline" href={route} target="_blank" rel="noreferrer">Open verified route ↗</a> : <span>Route unlocks when all stop coordinates are verified.</span>}</div></form>
      <form className="add-stop" onSubmit={addStop}><label>Add a destination<select required value={addSlug} onChange={event => setAddSlug(event.target.value)}><option value="">Choose a place…</option>{available.map(destination => <option key={destination.id} value={destination.slug}>{destination.name} — {destination.municipality.name}</option>)}</select></label><button className="btn btn-dark" disabled={busy || !available.length}>Add stop</button></form>
      <div className="itinerary-stops">{selected.stops.length === 0 ? <div className="empty"><h3>No stops yet.</h3><p>Add a published destination to begin.</p></div> : selected.stops.map((stop, index) => <article key={stop.id}><div className="stop-order">{index + 1}</div><div className="stop-copy"><div className="stop-heading"><div><span>{stop.destination.category} · {stop.destination.municipality}</span><h3>{stop.destination.name}</h3></div><em className={stop.destination.verification_status}>{stop.destination.verification_status}</em></div><StopEditor stop={stop} onSave={async (visitDate, notes) => replace((await api.updateItineraryStop(token, selected.id, stop.id, { visit_date: visitDate || null, notes: notes || null })).data)} /><div className="stop-actions"><button onClick={() => move(index, -1)} disabled={index === 0 || busy}>Move up</button><button onClick={() => move(index, 1)} disabled={index === selected.stops.length - 1 || busy}>Move down</button><button onClick={() => run(async () => replace((await api.removeItineraryStop(token, selected.id, stop.id)).data))}>Remove</button></div></div></article>)}</div></> }</section>
  </div></main>
}
