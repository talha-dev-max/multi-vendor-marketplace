export interface ShippingAddress {
  full_name: string;
  phone: string;
  address_line1: string;
  address_line2: string | null;
  city: string;
  state: string;
  postal_code: string;
  country: string;
}

export interface OrderItem {
  id: number;
  product_id: number;
  product_name: string;
  unit_price: number;
  quantity: number;
  line_total: number;
}

export interface VendorOrder {
  id: number;
  order_id: number;
  vendor_id: number;
  subtotal: number;
  commission: number;
  net: number;
  status: 'pending' | 'confirmed' | 'shipped' | 'delivered' | 'canceled';
  confirmed_at: string | null;
  shipped_at: string | null;
  delivered_at: string | null;
  vendor?: { id: number; store_name: string; store_slug: string };
  items?: OrderItem[];
}

export interface Order {
  id: number;
  customer_id: number;
  total: number;
  currency: string;
  status: string;
  payment_method: 'cod' | 'stripe';
  payment_status: 'pending' | 'paid' | 'failed';
  shipping_address: ShippingAddress;
  placed_at: string | null;
  vendor_orders?: VendorOrder[];
}

export interface PlaceOrderResponse {
  order: Order;
  stripe_checkout_url: string | null;
}
