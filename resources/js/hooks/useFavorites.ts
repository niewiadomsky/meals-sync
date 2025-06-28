import { useCallback, useEffect, useState } from 'react';
import { toast } from 'sonner';

export default function useFavorites(mealId: number) {
    const [isFavorite, setIsFavorite] = useState(false);

    useEffect(() => {
        setIsFavorite(checkIsFavorite());
    }, [mealId]);

    const addToFavorites = useCallback((e: React.MouseEvent<HTMLButtonElement>) => {
        e.preventDefault();

        const favorites = localStorage.getItem('favorites') || '[]';
        const favoritesArray = JSON.parse(favorites);
        favoritesArray.push(mealId);

        localStorage.setItem('favorites', JSON.stringify(favoritesArray));
        setIsFavorite(true);
        toast.success('Added to favorites');
    }, [mealId]);

    const removeFromFavorites = useCallback((e: React.MouseEvent<HTMLButtonElement>) => {
        e.preventDefault();

        const favorites = localStorage.getItem('favorites') || '[]';
        const favoritesArray = JSON.parse(favorites);
        const index = favoritesArray.indexOf(mealId);

        if (index > -1) {
            favoritesArray.splice(index, 1);
        }

        localStorage.setItem('favorites', JSON.stringify(favoritesArray));
        setIsFavorite(false);
        toast.success('Removed from favorites');
    }, [mealId]);

    const checkIsFavorite = useCallback(() => {
        const favorites = localStorage.getItem('favorites') || '[]';
        const favoritesArray = JSON.parse(favorites);

        return favoritesArray.includes(mealId);
    }, [mealId]);

    return { addToFavorites, removeFromFavorites, isFavorite };
}