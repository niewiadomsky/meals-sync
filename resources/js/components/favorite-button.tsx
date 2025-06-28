import useFavorites from '@/hooks/useFavorites';
import HeartIcon from './icons/heart-icon';

interface FavoriteButtonProps {
    mealId: number;
}

export default function FavoriteButton({ mealId }: FavoriteButtonProps) {
    const { addToFavorites, removeFromFavorites, isFavorite } = useFavorites(mealId);

    return (
        <button onClick={isFavorite ? removeFromFavorites : addToFavorites}>
            <HeartIcon
                size={32}
                stroke={isFavorite ? 'none' : 'currentColor'}
                fill={isFavorite ? 'currentColor' : 'none'}
                className={isFavorite ? 'text-red-500' : 'text-gray-400 hover:text-red-500'}
            />
        </button>
    );
}
