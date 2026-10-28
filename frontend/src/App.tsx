import { Navigate, Route, Routes } from 'react-router-dom'
import { AdminPage } from './pages/AdminPage'
import { HomePage } from './pages/HomePage'
import { AccountPage } from './pages/AccountPage'
import { PlannerPage } from './pages/PlannerPage'
import './App.css'

export default function App() {
  return (
    <Routes>
      <Route path="/" element={<HomePage />} />
      <Route path="/admin" element={<AdminPage />} />
      <Route path="/account" element={<AccountPage />} />
      <Route path="/planner" element={<PlannerPage />} />
      <Route path="*" element={<Navigate to="/" replace />} />
    </Routes>
  )
}
