export interface Category {
  id: number;
  name: string;
  slug: string;
  parent_id: number | null;
  sort_order: number;
  is_active: boolean;
}

export interface ProductImage {
  id: number;
  path: string;
  thumb_path: string | null;
  url: string | null;
  thumb_url: string | null;
  is_primary: boolean;
}

export interface VendorSummary {
  id: number;
  store_name: string;
  store_slug: string;
}

export interface Product {
  id: number;
  vendor_id: number;
  category_id: number | null;
  name: string;
  slug: string;
  description: string | null;
  price: number;
  stock: number;
  status: 'draft' | 'active' | 'inactive';
  images?: ProductImage[];
  category?: { id: number; name: string; slug: string } | null;
  vendor?: VendorSummary;
}
