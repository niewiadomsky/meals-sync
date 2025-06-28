import CommentsList from '@/components/comments-list';
import FavoriteButton from '@/components/favorite-button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import type { Comment, Meal, PaginatedData } from '@/types';
import { Head, Link } from '@inertiajs/react';

interface ShowProps {
    meal: Meal;
    comments: PaginatedData<Comment>;
}

export default function Show({ meal, comments }: ShowProps) {
    return (
        <AppLayout>
            <Head>
                <title>{meal.name}</title>
            </Head>
            <div>
                <Link href={route('home')} className="text-sm dark:text-gray-200 hover:underline">&laquo; Back to meals</Link>
            </div>
            <div className="grid grid-cols-4 gap-4">
                <Card className="col-span-3">
                    <CardHeader>
                        <div className="flex items-center justify-between">
                            <CardTitle className="text-2xl font-bold">{meal.name}</CardTitle>
                            <FavoriteButton mealId={meal.id} />
                        </div>
                    </CardHeader>
                    <CardContent>
                        <p>Area: {meal.area?.name}</p>
                        <p>Category: {meal.category?.name}</p>
                    </CardContent>
                </Card>
                <Card className="py-0">
                    <img src={meal.thumbnail_url} alt={meal.name} className="w-full h-full object-cover" />
                </Card>
            </div>
            <div className="grid grid-cols-1 lg:grid-cols-4 gap-4">
                <Card>
                    <CardHeader>
                        <CardTitle>Ingredients</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <ul>
                            {meal.ingredients?.map((ingredient) => (
                                <li key={ingredient.id} className="flex items-center justify-between">
                                    {ingredient.name} <span className="text-sm text-gray-500">{ingredient.measure}</span>
                                </li>
                            ))}
                        </ul>
                    </CardContent>
                </Card>
                <Card className="lg:col-span-3">
                    <CardHeader>
                        <CardTitle>Instructions</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p>{meal.instructions}</p>
                    </CardContent>
                </Card>
            </div>
            <Card>
                <CardHeader>
                    <CardTitle>Comments</CardTitle>
                </CardHeader>
                <CardContent>
                    <CommentsList comments={comments} mealId={meal.id} />
                </CardContent>
            </Card>
        </AppLayout>
    );
}
