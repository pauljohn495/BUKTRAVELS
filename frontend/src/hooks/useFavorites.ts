import { useEffect, useRef, useState } from 'react'
import { api } from '../api/client'
import { useAccount } from '../context/AccountContext'

const KEY = 'buktravels-saved'
export function useFavorites() {
  const { token } = useAccount()
  const syncedToken = useRef('')
  const [favorites, setFavorites] = useState<Set<string>>(() => {
    try { return new Set(JSON.parse(localStorage.getItem(KEY) || '[]')) } catch { return new Set() }
  })
  useEffect(() => { localStorage.setItem(KEY, JSON.stringify([...favorites])) }, [favorites])
  useEffect(() => {
    if (!token || syncedToken.current === token) return
    syncedToken.current = token
    api.syncFavorites(token, [...favorites]).then(result => setFavorites(new Set(result.data))).catch(() => { syncedToken.current = '' })
  }, [token, favorites])
  const toggle = (slug: string) => setFavorites(current => {
    const next = new Set(current); const removing = next.has(slug)
    if (removing) next.delete(slug); else next.add(slug)
    if (token) {
      const operation = removing ? api.removeFavorite(token, slug) : api.addFavorite(token, slug)
      operation.then(result => setFavorites(new Set(result.data))).catch(() => setFavorites(current))
    }
    return next
  })
  return { favorites, toggle, cloudSynced: Boolean(token) }
}
