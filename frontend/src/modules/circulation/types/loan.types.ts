export interface Loan {
  id: number | string;
  user_id: number;
  item_id: number;
  borrowed_at: string;
  due_date: string;
  returned_at?: string | null;
  renewal_count: number;
  status: 'active' | 'returned' | 'overdue';
}

export interface ActiveLoan extends Loan {
  item_barcode?: string;
  work_title?: string;
}

export interface LoanHistory {
  id: number | string;
  loans: Loan[];
}

export interface Fine {
  id: number | string;
  user_id: number;
  amount: number;
  status: 'pending' | 'paid';
  reason?: string;
}
