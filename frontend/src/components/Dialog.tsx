import { useEffect, type ReactNode } from 'react'

export function Dialog({ children, onClose, labelledBy = 'dialog-title' }: { children: ReactNode; onClose: () => void; labelledBy?: string }) {
  useEffect(() => {
    const escape = (event: KeyboardEvent) => event.key === 'Escape' && onClose()
    document.addEventListener('keydown', escape); document.body.style.overflow = 'hidden'
    return () => { document.removeEventListener('keydown', escape); document.body.style.overflow = '' }
  }, [onClose])
  return <div className="overlay" role="presentation" onMouseDown={event => event.target === event.currentTarget && onClose()}><section className="dialog" role="dialog" aria-modal="true" aria-labelledby={labelledBy}><button className="dialog-close" aria-label="Close details" autoFocus onClick={onClose}>×</button>{children}</section></div>
}
