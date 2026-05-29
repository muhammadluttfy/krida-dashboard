export type OrderOptionCustomer = {
    id: number;
    customer_code: string;
    full_name: string;
};

export type OrderOptionItem = {
    id: number;
    item_code: string;
    description: string;
    price: string | number;
};

export type OrderLineRecord = {
    id: number;
    item_id: number;
    description?: string | null;
    quantity: number;
    unit_price: string | number;
    discount_amount: string | number;
    line_total: string | number;
    item?: OrderOptionItem;
};

export type OrderRecord = {
    id: number;
    order_number: string;
    order_date: string;
    customer_id: number;
    subtotal: string | number;
    discount_amount: string | number;
    net_amount: string | number;
    dpp_amount: string | number;
    ppn_amount: string | number;
    grand_total: string | number;
    customer?: OrderOptionCustomer;
    order_items?: OrderLineRecord[];
};

export type OrderDialogMode = 'create' | 'edit' | 'view';

export type OrderFormLine = {
    item_id: string;
    description: string;
    quantity: string;
    unit_price: string;
    discount_percent: string;
};

export type OrderFormValues = {
    order_number: string;
    order_date: string;
    customer_id: string;
    items: OrderFormLine[];
};
