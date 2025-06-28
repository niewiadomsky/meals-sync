import { Toaster } from '@/components/ui/sonner';
import { Link } from '@inertiajs/react';

export default function AuthLayout({ children }: { children: React.ReactNode }) {
    return (
        <div className="max-w-md mt-16 px-8 py-8 mx-auto">
            {children}
            <Toaster position="top-center" closeButton={true} />
        </div>
    );
}