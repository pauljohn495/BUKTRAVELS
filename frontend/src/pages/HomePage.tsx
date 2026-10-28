import { useCallback, useEffect, useMemo, useState } from 'react'
import { api } from '../api/client'
import { Brand } from '../components/Brand'
import { DestinationCard } from '../components/DestinationCard'
import { DestinationDialog } from '../components/DestinationDialog'
import { DestinationMap } from '../components/DestinationMap'
import { Dialog } from '../components/Dialog'
import { Header } from '../components/Header'
import { trips } from '../data/trips'
import { useFavorites } from '../hooks/useFavorites'
import { useAccount } from '../context/AccountContext'
import type { Destination } from '../types'

const CURRENT_YEAR = new Date().getFullYear()

export function HomePage() {
  const [destinations, setDestinations] = useState<Destination[]>([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState('')
  const [search, setSearch] = useState('')
  const [category, setCategory] = useState('all')
  const [municipality, setMunicipality] = useState('all')
  const [savedOnly, setSavedOnly] = useState(false)
  const [selectedSlug, setSelectedSlug] = useState(() => location.hash.match(/^#spot=([a-z0-9-]+)$/)?.[1] || '')
  const [tripId, setTripId] = useState<string | null>(null)
  const { favorites, toggle, cloudSynced } = useFavorites()
  const { user } = useAccount()

  useEffect(() => {
    api.destinations().then(result => setDestinations(result.data)).catch(err => setError(err.message)).finally(() => setLoading(false))
  }, [])

  const categories = useMemo(() => [...new Map(destinations.map(item => [item.category.slug, item.category])).values()].sort((a, b) => a.name.localeCompare(b.name)), [destinations])
  const municipalities = useMemo(() => [...new Map(destinations.map(item => [item.municipality.slug, item.municipality])).values()].sort((a, b) => a.name.localeCompare(b.name)), [destinations])
  const selected = destinations.find(item => item.slug === selectedSlug) || null
  const filtered = useMemo(() => {
    const term = search.trim().toLowerCase()
    return destinations.filter(item => {
      const haystack = `${item.name} ${item.municipality.name} ${item.category.name} ${item.summary || ''} ${item.description || ''}`.toLowerCase()
      return (!term || haystack.includes(term)) && (category === 'all' || item.category.slug === category) && (municipality === 'all' || item.municipality.slug === municipality) && (!savedOnly || favorites.has(item.slug))
    })
  }, [destinations, search, category, municipality, savedOnly, favorites])

  const reset = () => { setSearch(''); setCategory('all'); setMunicipality('all'); setSavedOnly(false) }
  const closeDestination = useCallback(() => { setSelectedSlug(''); if (location.hash.startsWith('#spot=')) history.replaceState(null, '', location.pathname) }, [])
  const trip = trips.find(item => item.id === tripId)

  return <>
    <div id="top" />
    <Header accountName={user?.name} savedCount={favorites.size} onSaved={() => { reset(); setSavedOnly(true); document.querySelector('#destinations')?.scrollIntoView() }} />
    <main>
      <section className="hero"><div className="wrap hero-content"><h1>Find your way to <em>Bukidnon.</em></h1><p>Discover mountain landscapes, peaceful lakes and living culture. Every listing is clearly labelled while local details await verification.</p><div className="hero-actions"><a className="btn btn-light" href="#destinations">Explore destinations ↘</a><a className="text-link" href="#trip-ideas">See trip ideas</a></div></div>  </section>
      <section className="highlights"><div className="wrap highlight-grid"><div className="highlight"><i>△</i><div><b>Highland landscapes</b><p>Mountains, ridges and wide-open views</p></div></div><div className="highlight"><i>◎</i><div><b>Nature & culture</b><p>Lakes, forests and meaningful landmarks</p></div></div><div className="highlight"><i>♡</i><div><b>Save as you explore</b><p>Favorites stay private on this device</p></div></div></div></section>
      <section className="section" id="destinations"><div className="wrap"><div className="heading"><div><div className="kicker">Places worth the journey</div><h2>Explore Bukidnon.</h2></div><p>Start with the migrated prototype listings. Check their linked sources before making plans.</p></div>
        <div className="filters"><div className="filter-row"><label className="search"><span>⌕</span><span className="sr-only">Search destinations</span><input value={search} onChange={event => { setSearch(event.target.value); setSavedOnly(false) }} placeholder="Search destinations, towns or experiences" /></label><label><span className="sr-only">Municipality</span><select value={municipality} onChange={event => { setMunicipality(event.target.value); setSavedOnly(false) }}><option value="all">All locations</option>{municipalities.map(item => <option key={item.id} value={item.slug}>{item.name}</option>)}</select></label><button className="clear" onClick={reset}>Clear filters</button></div>
          <div className="filter-bottom"><div className="chips"><button className={`chip ${category === 'all' ? 'active' : ''}`} aria-pressed={category === 'all'} onClick={() => { setCategory('all'); setSavedOnly(false) }}>All</button>{categories.map(item => <button className={`chip ${category === item.slug ? 'active' : ''}`} aria-pressed={category === item.slug} key={item.id} onClick={() => { setCategory(item.slug); setSavedOnly(false) }}>{item.name}</button>)}</div><span className="count">{filtered.length} destination{filtered.length === 1 ? '' : 's'} {savedOnly ? 'saved' : 'to explore'}</span></div>
        </div>
        {error && <div className="notice error"><strong>Could not reach the destination API.</strong><span>{error}</span></div>}
        {loading ? <div className="notice">Loading destinations…</div> : filtered.length ? <div className="cards">{filtered.map(item => <DestinationCard key={item.id} destination={item} saved={favorites.has(item.slug)} onToggle={() => toggle(item.slug)} onOpen={() => setSelectedSlug(item.slug)} />)}</div> : !error && <div className="empty"><h3>No places match your search.</h3><p>Try another location or category.</p><button className="btn btn-dark" onClick={reset}>Show all destinations</button></div>}
      </div></section>
      <section className="section map-section" id="map"><div className="wrap"><div className="heading"><div><div className="kicker">Verified geography only</div><h2>Explore the map.</h2></div><p>Markers appear only after a reviewer verifies both the destination and its coordinates.</p></div><DestinationMap destinations={destinations} onOpen={item => setSelectedSlug(item.slug)} /></div></section>
      <section className="about" id="about"><div className="about-photo" role="img" aria-label="Illustrative mountain landscape; not a verified Bukidnon destination photo"/><div className="about-copy"><div className="kicker">A place unlike anywhere else</div><h2>Closer to nature. Closer to wonder.</h2><p>BUKTRAVELS is becoming a maintainable guide with transparent sourcing. Until tourism offices and operators verify the migrated content, treat it as inspiration—not current travel advice.</p><div className="about-points"><div><strong>Wild landscapes</strong><span>Mountain trails, lakes and springs</span></div><div><strong>Living culture</strong><span>Landmarks, traditions and heritage</span></div></div></div></section>
      <section className="section plans" id="trip-ideas"><div className="wrap"><div className="heading"><div><div className="kicker">Travel inspiration</div><h2>Pick your kind of trip.</h2></div><p>Suggested collections only. They are not timed or route-optimized itineraries.</p></div><div className="plan-grid">{trips.map(item => <article className="plan-card" key={item.id}><span className="plan-index" aria-hidden="true">{item.number}</span><span className="plan-icon" aria-hidden="true">{item.icon}</span><h3>{item.title}</h3><p>{item.description}</p><div className="stops">{item.slugs.map(slug => destinations.find(destination => destination.slug === slug)?.name).filter(Boolean).join(' · ')}</div><button onClick={() => setTripId(item.id)}>Explore this trip →</button></article>)}</div></div></section>
      <section className="closing"><div className="wrap"><div className="closing-panel"><div className="eyebrow">Your next story starts here</div><h2>More mountains.<br/>More memories.</h2><p>Explore the places, save favorites locally and confirm current conditions before setting out.</p><a className="btn btn-light" href="#destinations">Start exploring ↗</a></div></div></section>
    </main>
    <footer className="footer"><div className="wrap foot-grid"><div><a href="#top"><Brand /></a><p>An independent prototype celebrating the landscapes, culture and adventures of Bukidnon, Philippines.</p></div><div><h3>Explore</h3><a href="#destinations">All destinations</a><a href="#map">Verified map</a><a href="#trip-ideas">Trip ideas</a><a href="/planner">Trip planner</a></div><div><h3>Project</h3><a href="/account">{cloudSynced ? 'Favorites synced' : 'Optional account'}</a><a href="/admin">Staff panel</a><p>Facts, access details and imagery remain unverified unless explicitly labelled otherwise.</p></div></div><div className="wrap foot-bottom"><span>© {CURRENT_YEAR} BUKTRAVELS.</span><span>Confirm access, prices, hours and safety guidance before travel.</span></div></footer>
    {selected && <DestinationDialog destination={selected} saved={favorites.has(selected.slug)} onToggle={() => toggle(selected.slug)} onClose={closeDestination} />}
    {trip && <Dialog onClose={() => setTripId(null)}><div className="dialog-body"><div className="kicker">Suggested travel collection</div><h2 id="dialog-title">{trip.title}</h2><p>These are suggested places to explore, not a timed route. Allow for travel between towns and check local access conditions.</p><div className="trip-list">{trip.slugs.map((slug, index) => { const destination = destinations.find(item => item.slug === slug); return destination && <button key={slug} onClick={() => { setTripId(null); setSelectedSlug(destination.slug) }}><b>{index + 1}. {destination.name}</b><small>{destination.municipality.name}</small></button> })}</div><div className="tip">Plan transport, timing, fees, permits and weather before traveling.</div></div></Dialog>}
  </>
}
