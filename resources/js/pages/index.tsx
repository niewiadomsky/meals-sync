import MealCard from '@/components/meal-card';
import MealsList from '@/components/meals-list';
import SearchInput from '@/components/search-input';
import type { PaginatedData, Meal } from '@/types';
import { router } from '@inertiajs/react';

interface IndexProps {
    meals: PaginatedData<Meal>;
}

export default function Index({ meals }: IndexProps) {
    const handleSearch = (value: string) => {
        router.visit(route('home'), {
            only: ['meals'],
            data: { search: value },
            preserveState: true,
        });
    };

    return (
        <div className="max-w-7xl mx-auto">
            <SearchInput label="Search for a meal" onSearch={handleSearch} />
            <MealsList meals={meals} />
        </div>
    );
}
