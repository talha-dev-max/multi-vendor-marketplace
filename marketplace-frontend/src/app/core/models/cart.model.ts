export interface CartItemProductSummary {
  id: number;
  name: string;
  slug: string;
  price: number;
  stock: number;
  primary_image: string | null;
  vendor: { id: number; store_name: string };
}

export interface CartItem {
  id: number;
  product_id: number;
  quantity: number;
  unit_price: number;
  line_total: number;
  product: CartItemProductSummary;
}

export interface Cart {
  id: number;
  user_id: number;
  items: CartItem[];
  total: number;
  item_count: number;
  quantity_total: number;
}
