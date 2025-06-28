import type { Meal, PaginatedData } from '@/types';
import { Link } from '@inertiajs/react';
import MealCard from './meal-card';
import Pagination from './pagination';

interface MealsListProps {
    meals: PaginatedData<Meal>;
}

export default function MealsList({ meals }: MealsListProps) {
    return (
        <div>
            <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                {meals.data.map((meal) => (
                    <MealCard key={meal.id} meal={meal} />
                ))}
            </div>
            <div className="my-4">
                <Pagination pagination={meals} />
            </div>
        </div>
    );
}
