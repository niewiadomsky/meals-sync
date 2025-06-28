import MealsList from '@/components/meals-list';
import SearchInput from '@/components/search-input';
import AppLayout from '@/layouts/app-layout';
import type { Meal, PaginatedData } from '@/types';
import { Head, router } from '@inertiajs/react';
import { useCallback, useState } from 'react';

interface IndexProps {
    meals: PaginatedData<Meal>;
}

export default function Index({ meals }: IndexProps) {
    const [onlyFavorites, setOnlyFavorites] = useState(false);

    const handleSearch = (value: string) => {
        const data: any = { search: value };
        
        if (onlyFavorites) {
            data.favorites = getFavorites();
        }

        router.visit(route('home'), {
            only: ['meals'],
            data,
            preserveState: true,
        });
    };

    const handleToggleFavorites = useCallback(() => {
        setOnlyFavorites(!onlyFavorites);
        router.reload({
            only: ['meals'],
            data: {
                favorites: !onlyFavorites ? getFavorites() : undefined,
                page: undefined
            },
        });
    }, [onlyFavorites]);

    const getFavorites = useCallback(() => {
        const favorites = localStorage.getItem('favorites') || '[]';
        const favoritesArray = JSON.parse(favorites);
        return favoritesArray.map(Number);
    }, []);

    return (
        <AppLayout>
            <Head title="Meals" />
            <SearchInput label="Search for a meal" onSearch={handleSearch} />
            <button className="rounded-md bg-blue-500 px-4 py-2 text-white" onClick={handleToggleFavorites}>
                {onlyFavorites ? 'Show all meals' : 'Show only favorites'}
            </button>
            <MealsList meals={meals} />
        </AppLayout>
    );
}
