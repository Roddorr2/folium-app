export type RoleKey = 'admin' | 'cataloger' | 'librarian' | 'network_librarian' | 'reader';

export type BadgeVariant = 'forest' | 'emerald' | 'copper' | 'crimson' | 'stone' | 'amber' | 'indigo';

export interface Role {
  id: number;
  key: RoleKey | string;
  name: RoleKey | string;
  display_name: string;
  short_label: string;
  badge_variant: BadgeVariant;
  description?: string | null;
}

export interface BranchSummary {
  id: number;
  name: string;
  city?: string;
  address?: string;
}

export interface User {
  id: number;
  name: string;
  short_name: string;
  initials: string;
  email: string;
  dni?: string | null;
  role_id?: number;
  role: Role;
  branch_id?: number | null;
  branch?: BranchSummary | null;
  created_at?: string;
}

export interface AuthSession {
  user: User;
  token: string;
}
