import { useMemo, useState } from 'react';
import { useDebouncedCallback } from 'use-debounce';
import { Input } from './ui/input';
import { Label } from './ui/label';
import { usePage } from '@inertiajs/react';

interface SearchInputProps {
    label: string;
    placeholder?: string;
    onSearch: (value: string) => void;
}

export default function SearchInput({ label, placeholder, onSearch }: SearchInputProps) {
    const {url} = usePage();
    const queryParams = useMemo(() => new URLSearchParams(url.split('?')[1] ?? ''), [url]);
    const [search, setSearch] = useState(queryParams.get('search') ?? '');
    const debouncedSearch = useDebouncedCallback(onSearch, 500);


    return (
        <div className="flex flex-col gap-2">
            <Label htmlFor="search">{label}</Label>
            <Input
                type="text"
                id="search"
                value={search}
                onChange={(e) => {
                    setSearch(e.target.value);
                    debouncedSearch(e.target.value);
                }}
                placeholder={placeholder}
            />
        </div>
    );
}
