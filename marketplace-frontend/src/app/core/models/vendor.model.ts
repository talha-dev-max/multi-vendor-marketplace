export interface VendorProfile {
  id: number;
  user_id: number;
  store_name: string;
  store_slug: string;
  description: string | null;
  status: 'pending' | 'approved' | 'rejected';
  approved_at: string | null;
  rejected_at: string | null;
  rejection_reason: string | null;
  user?: { id: number; name: string; email: string };
}

export interface VendorEarning {
  id: number;
  vendor_order_id: number;
  gross: number;
  commission: number;
  net: number;
  status: 'pending' | 'released';
  released_at: string | null;
  order_id: number;
}

export interface EarningsSummary {
  gross: number;
  net: number;
  pending: number;
  released: number;
}

export interface AdminStats {
  users_total: number;
  vendors_approved: number;
  vendors_pending: number;
  products_total: number;
  products_active: number;
  orders_total: number;
  orders_pending: number;
  gross_sales: number;
}
