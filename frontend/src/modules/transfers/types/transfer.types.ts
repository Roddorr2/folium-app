import type { Branch, Item, Work } from '../../catalog/types';

export interface CatalogItem {
  id?: number | string;
  barcode?: string;
  shelfLocation?: string;
  branchId?: number;
  branch_id?: number;
  branchName?: string;
  format?: string;
}

export interface IllTransferTargetData {
  work?: Work;
  manifestation?: any;
  item?: CatalogItem | Item;
  originBranch?: Branch;
  targetBranch?: Branch;
}

export interface IllTransfer {
  id: number | string;
  trackingCode: string;
  workTitle: string;
  originBranch: Branch | string;
  targetBranch: Branch | string;
  date: string;
  status?: 'pending' | 'in_transit' | 'delivered' | 'failed';
}

export type TransferStatus = 'pending' | 'in_transit' | 'completed' | 'failed';

export interface BranchRoute {
  originBranchId: number;
  targetBranchId: number;
  estimatedHours: number;
}
