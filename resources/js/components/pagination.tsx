import type { PaginatedData } from '@/types';
import { Link } from '@inertiajs/react';

interface PaginationProps {
    pagination: PaginatedData<unknown>;
}

export default function Pagination({ pagination }: PaginationProps) {
    return (
        <div className="flex justify-center gap-2">
            {pagination.links.map((link) => (
                <Link
                    key={link.label}
                    href={link.url ?? ''}
                    disabled={!link.url || link.active}
                    className="rounded-md border-2 border-gray-800 min-w-8 min-h-8 p-2 text-center disabled:cursor-not-allowed disabled:opacity-50 disabled:border-gray-400"
                >
                    <span dangerouslySetInnerHTML={{ __html: link.label }} />
                </Link>
            ))}
        </div>
    );
}
