import { divIcon } from 'leaflet'
import { MapContainer, Marker, Popup, TileLayer } from 'react-leaflet'
import type { Destination } from '../types'
import 'leaflet/dist/leaflet.css'

const marker = divIcon({ className: 'buk-marker', html: '<span></span>', iconSize: [24, 30], iconAnchor: [12, 30] })
const tileUrl = import.meta.env.VITE_MAP_TILE_URL || 'https://tile.openstreetmap.org/{z}/{x}/{y}.png'

export function DestinationMap({ destinations, onOpen }: { destinations: Destination[]; onOpen: (destination: Destination) => void }) {
  const mapped = destinations.filter(item => item.verification_status === 'verified' && item.latitude && item.longitude)
  if (!mapped.length) return <div className="map-empty"><strong>Map ready for verified coordinates</strong><p>No destination coordinates are published yet. A reviewer can add them after manual verification.</p></div>
  const first = mapped[0]
  return <MapContainer className="destination-map" center={[Number(first.latitude), Number(first.longitude)]} zoom={10} scrollWheelZoom={false}>
    <TileLayer attribution='&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap contributors</a>' url={tileUrl} />
    {mapped.map(item => <Marker key={item.id} position={[Number(item.latitude), Number(item.longitude)]} icon={marker}><Popup><strong>{item.name}</strong><br/>{item.municipality.name}<br/><button className="map-details" onClick={() => onOpen(item)}>View details</button></Popup></Marker>)}
  </MapContainer>
}
