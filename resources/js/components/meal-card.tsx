import type { Meal } from '@/types';

interface MealCardProps {
    meal: Meal;
}

export default function MealCard({ meal }: MealCardProps) {
    return (
        <div>
            <h2 className="text-ellipsis overflow-hidden whitespace-nowrap" title={meal.name}>{meal.name}</h2>
            <img src={meal.thumbnail_url} alt={meal.name}/>
            <p>{meal.area?.name}</p>
            <p>{meal.category?.name}</p>
        </div>
    );
}