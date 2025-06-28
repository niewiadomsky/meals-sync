import AppHeader from '@/components/app-header';
import { Toaster } from '@/components/ui/sonner';

export default function AppLayout({ children }: { children: React.ReactNode }) {

    return (
        <div className="mx-auto max-w-7xl space-y-4 px-8 py-8 scroll-smooth">
            <AppHeader />
            {children}
            <Toaster position="top-center" closeButton={true} />
        </div>
    );
}