import type { Meal } from '@/types';
import { Link } from '@inertiajs/react';
import { Card, CardHeader, CardTitle } from './ui/card';
import FavoriteButton from './favorite-button';

interface MealCardProps {
    meal: Meal;
}

export default function MealCard({ meal }: MealCardProps) {

    return (
        <Card className="relative p-0">
            <Link href={route('meals.show', meal.id)}>
                <CardHeader className="my-4">
                    <div className="flex items-center justify-between">
                        <CardTitle title={meal.name}>{meal.name}</CardTitle>
                        <FavoriteButton mealId={meal.id} />
                    </div>
                </CardHeader>
                <div className="relative">
                    <img src={meal.thumbnail_url} alt={meal.name} />
                    <div className="absolute top-4 right-4"></div>
                </div>
            </Link>
        </Card>
    );
}
