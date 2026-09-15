export interface Branch {
  id: number;
  name: string;
  code?: string;
  address?: string;
}

export interface BranchAvailability {
  branch_id: number;
  branch_name: string;
  available_copies: number;
}

export interface Item {
  id: number | string;
  barcode: string;
  branchId?: number;
  branch_id?: number;
  branchName?: string;
  branch_name?: string;
  shelfLocation?: string;
  condition?: string;
  status: string;
}

export interface Manifestation {
  id: number | string;
  expression_id?: number;
  format?: string;
  publisher?: string;
  publicationYear?: number | string;
  isbn?: string;
  dimensions?: string;
  notes?: string;
  items?: Item[];
}

export interface Expression {
  id?: number | string;
  work_id?: number;
  title: string;
  language?: string;
  type?: string;
  revisionYear?: number | string;
  description?: string;
  manifestations?: Manifestation[];
}

export interface Work {
  id: number | string;
  title: string;
  author: string;
  authorDates?: string;
  yearRange?: string;
  originalLanguage?: string;
  abstract?: string;
  dewey?: string;
  nature?: string;
  subjects?: string[];
  expressions?: Expression[];
  branches_availability?: BranchAvailability[];
}

export interface WorkItem {
  id: number | string;
  title: string;
  author: string;
  abstract?: string;
  dewey?: string;
  nature?: string;
  branches_availability?: BranchAvailability[];
}
