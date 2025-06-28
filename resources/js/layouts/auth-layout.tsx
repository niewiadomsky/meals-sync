import { Toaster } from '@/components/ui/sonner';
import { Link } from '@inertiajs/react';

export default function AuthLayout({ children }: { children: React.ReactNode }) {
    return (
        <div className="max-w-md mt-16 px-8 py-8 mx-auto">
            <header>
                <Link href={route('home')} className="text-sm dark:text-gray-200 hover:underline">&laquo; Back to meals</Link>
            </header>
            {children}
            <Toaster position="top-center" closeButton={true} />
        </div>
    );
}