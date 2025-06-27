import { usePage } from '@inertiajs/react';
import { useMemo, useState } from 'react';
import { useDebouncedCallback } from 'use-debounce';

interface SearchInputProps {
    label: string;
    placeholder?: string;
    onSearch: (value: string) => void;
}

export default function SearchInput({ label, placeholder, onSearch }: SearchInputProps) {
    const url = useMemo(() => new URLSearchParams(window.location.search), []);
    const [search, setSearch] = useState(url.get('search') ?? '');
    const debouncedSearch = useDebouncedCallback(onSearch, 500);

    return (
        <div>
            <label htmlFor="search" className="block text-sm font-medium text-gray-700">
                {label}
            </label>
            <input
                type="text"
                id="search"
                value={search}
                onChange={(e) => {
                    setSearch(e.target.value);
                    debouncedSearch(e.target.value);
                }}
                placeholder={placeholder}
                className="w-full p-2 border-2 border-gray-800 rounded-md"
            />
        </div>
    );
}
