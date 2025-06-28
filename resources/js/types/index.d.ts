import type { Config } from 'ziggy-js';

export interface SharedData {
    name: string;
    ziggy: Config & { location: string };
    auth: {
        user: User;
    };
    [key: string]: unknown;
}

export interface PaginatedData<T> {
    data: T[];
    links: {
        first: string | null;
        last: string | null;
        next: string | null;
        prev: string | null;
    };
    meta: {
        from: number;
        last_page: number;
        path: string;
        per_page: number;
        to: number;
        total: number;
        links: {
            active: boolean;
            label: string;
            url: string | null;
        }[];
    };
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
    comments?: Comment[];
    tags?: string[];
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
    measure: string;
}

export interface Comment {
    id: number;
    user: User;
    content: string;
    created_at: string;
}

export interface User {
    id: number;
    name: string;
}
