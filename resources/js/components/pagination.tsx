import type { PaginatedData } from '@/types';
import { Link } from '@inertiajs/react';
import { Pagination as PaginationComponent, PaginationContent, PaginationItem, PaginationLink, PaginationNext, PaginationPrevious } from './ui/pagination';
import { Fragment } from 'react/jsx-runtime';

interface PaginationProps {
    pagination: PaginatedData<unknown>;
}

export default function Pagination({ pagination }: PaginationProps) {
    return (
        <div className="px-16">
            <PaginationComponent>
                <PaginationContent>
                {pagination.meta.links.map((link, index) => (
                    <Fragment key={link.label}>
                        {index === 0 && <PaginationPrevious href={link.url ?? ''} disabled={!link.url || link.active} />}
                        {index === pagination.meta.links.length - 1 && <PaginationNext href={link.url ?? ''} disabled={!link.url || link.active} />}
                        {index !== 0 && index !== pagination.meta.links.length - 1 && (
                            <PaginationItem key={link.label}>
                                <PaginationLink href={link.url ?? ''} disabled={!link.url || link.active}>
                                    <span dangerouslySetInnerHTML={{ __html: link.label }} />
                                </PaginationLink>
                            </PaginationItem>
                        )}
                    </Fragment>
                ))}
                </PaginationContent>
            </PaginationComponent>
        </div>
    );
}
