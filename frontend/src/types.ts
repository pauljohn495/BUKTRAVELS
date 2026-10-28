export type VerificationStatus = 'unverified' | 'verified'
export type PublicationStatus = 'draft' | 'published' | 'archived'

export interface Municipality { id: number; name: string; slug: string; type: string; verification_status: VerificationStatus }
export interface Category { id: number; name: string; slug: string }
export interface Photo { id: number; url: string; alt_text: string; attribution: string | null; source_url: string | null; license: string | null; is_primary?: boolean; verification_status: VerificationStatus }
export interface Highlight { id: number; label: string; verification_status: VerificationStatus }
export interface DestinationUpdate { id: number; title: string; body: string; status: 'draft' | 'published'; published_at: string | null }
export interface Destination {
  id: number; slug: string; name: string; summary: string | null; description: string | null;
  address: string | null; latitude: string | null; longitude: string | null; access_notes: string | null;
  source_url: string | null; source_label: string | null; status: PublicationStatus;
  verification_status: VerificationStatus; verified_at: string | null; published_at: string | null;
  municipality: Municipality; category: Category; primary_photo: Photo | null; photos: Photo[];
  highlights: Highlight[]; updates: DestinationUpdate[];
}
export interface PaginatedResponse<T> { data: T[]; meta?: { current_page: number; last_page: number; total: number } }
export interface AccountUser { id: number; name: string; email: string; role: 'visitor' | 'content_editor' | 'reviewer' | 'admin' }
export interface StaffUser extends AccountUser { created_at?: string }
export interface AuditEvent { id: number; action: string; auditable_type: string; auditable_id: number | null; before: Record<string, unknown> | null; after: Record<string, unknown> | null; created_at: string; actor: Pick<AccountUser, 'id' | 'name' | 'email'> | null }
export interface ItineraryStop {
  id: number; position: number; visit_date: string | null; notes: string | null;
  destination: { id: number; slug: string; name: string; latitude: string | null; longitude: string | null; verification_status: VerificationStatus; municipality: string; category: string };
}
export interface Itinerary { id: number; name: string; start_date: string | null; notes: string | null; stops: ItineraryStop[]; created_at: string; updated_at: string }
