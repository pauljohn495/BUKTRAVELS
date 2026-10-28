import { createContext, useContext, useEffect, useState, type ReactNode } from 'react'
import { api } from '../api/client'
import type { AccountUser } from '../types'

const TOKEN_KEY = 'buktravels-account-token'
type AccountContextValue = {
  token: string; user: AccountUser | null; loading: boolean;
  login: (email: string, password: string) => Promise<void>;
  register: (payload: Record<string, string>) => Promise<void>;
  logout: () => Promise<void>;
}
const AccountContext = createContext<AccountContextValue | null>(null)

export function AccountProvider({ children }: { children: ReactNode }) {
  const [token, setToken] = useState(() => localStorage.getItem(TOKEN_KEY) || '')
  const [user, setUser] = useState<AccountUser | null>(null)
  const [loading, setLoading] = useState(Boolean(token))

  useEffect(() => {
    if (!token) return
    api.accountMe(token).then(result => setUser(result.user)).catch(() => {
      localStorage.removeItem(TOKEN_KEY); setToken(''); setUser(null)
    }).finally(() => setLoading(false))
  }, [token])

  const accept = (result: { token: string; user: AccountUser }) => {
    localStorage.setItem(TOKEN_KEY, result.token); setToken(result.token); setUser(result.user)
  }
  const login = async (email: string, password: string) => accept(await api.accountLogin(email, password))
  const register = async (payload: Record<string, string>) => accept(await api.accountRegister(payload))
  const logout = async () => { if (token) await api.accountLogout(token).catch(() => undefined); localStorage.removeItem(TOKEN_KEY); setToken(''); setUser(null) }

  return <AccountContext.Provider value={{ token, user, loading, login, register, logout }}>{children}</AccountContext.Provider>
}

// oxlint-disable-next-line react/only-export-components
export function useAccount() {
  const value = useContext(AccountContext)
  if (!value) throw new Error('useAccount must be used inside AccountProvider')
  return value
}
