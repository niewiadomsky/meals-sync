import type { Comment } from '@/types';
import { Card, CardContent, CardHeader, CardTitle } from './ui/card';
import { Avatar, AvatarFallback, AvatarImage } from './ui/avatar';
import { useMemo } from 'react';
import { format } from 'date-fns';

interface CommentCardProps {
    comment: Comment;
}

export default function CommentCard({ comment }: CommentCardProps) {
    const formattedDate = useMemo(() => {
        return format(new Date(comment.created_at), 'dd/MM/yyyy HH:mm');
    }, [comment.created_at]);
    return <Card>
        <CardHeader className='flex items-center gap-2'>
            <Avatar>
                <AvatarImage src={`https://i.pravatar.cc/150?u=${comment.user.id}`} />
                <AvatarFallback>{comment.user.name.charAt(0)}</AvatarFallback>
            </Avatar>
            <CardTitle>{comment.user.name}</CardTitle>
            <p className='text-sm text-gray-500'>{formattedDate}</p>

        </CardHeader>
        <CardContent>
            <p>{comment.content}</p>
        </CardContent>
    </Card>;
}