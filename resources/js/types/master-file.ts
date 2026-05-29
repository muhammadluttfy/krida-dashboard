export type CustomerRecord = {
    id: number;
    customer_code: string;
    full_name: string;
    address: string | null;
    phone_number: string | null;
};

export type CustomerFormValues = {
    customer_code: string;
    full_name: string;
    address: string;
    phone_number: string;
};

export type CustomerDialogMode = 'create' | 'edit' | 'view';

export type ItemRecord = {
    id: number;
    item_code: string;
    description: string;
    price: string | number;
};

export type ItemFormValues = {
    item_code: string;
    description: string;
    price: string;
};

export type ItemDialogMode = 'create' | 'edit' | 'view';
