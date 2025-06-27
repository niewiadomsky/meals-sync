import { LucideIcon } from 'lucide-react';
import type { Config } from 'ziggy-js';


export interface SharedData {
    name: string;
    ziggy: Config & { location: string };
    [key: string]: unknown;
}

export interface PaginatedData<T> {
    data: T[];
    first_page_url: string;
    from: number;
    last_page: number;
    last_page_url: string;
    next_page_url: string;
    path: string;
    per_page: number;
    prev_page_url: string;
    to: number;
    total: number;
    links: {
        active: boolean;
        label: string;
        url: string | null;
    }[];
}

export interface Meal {
    id: number;
    external_id: string;
    name: string;
    instructions: string;
    thumbnail_url: string;
    video_url: string;
    area?: Area;
    category?: Category;
    ingredients?: Ingredient[];
}

export interface Area {
    id: number;
    name: string;
}

export interface Category {
    id: number;
    name: string;
}

export interface Ingredient {
    id: number;
    name: string;
}