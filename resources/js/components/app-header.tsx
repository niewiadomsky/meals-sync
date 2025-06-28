import { SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/react';

export default function AppHeader() {
    const { auth } = usePage<SharedData>().props;

    return (
        <header>
            <div className="flex items-center justify-between">
                <Link href={route('home')}>
                    <h1 className="text-2xl font-bold">Meal recipes</h1>
                </Link>
                {auth.user ? (
                    <div className="flex items-center gap-4">
                        <span className="text-lg text-gray-300">Welcome, {auth.user.name}</span>
                        <Link href={route('logout')} method="post">Log out</Link>
                    </div>
                ) : (
                    <div className="flex items-center gap-4">
                        <Link href={route('login')}>Log in</Link>
                        <Link href={route('register')}>Sign up</Link>
                    </div>
                )}
            </div>
        </header>
    );
}
