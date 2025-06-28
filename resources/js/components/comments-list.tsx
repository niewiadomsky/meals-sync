import type { Comment, PaginatedData } from '@/types';
import CommentCard from './comment-card';
import Pagination from './pagination';
import CommentInput from './comment-input';

interface CommentsListProps {
    comments: PaginatedData<Comment>;
    mealId: number;
}

export default function CommentsList({ comments, mealId }: CommentsListProps) {
    return (
        <div className="space-y-4">
            <CommentInput mealId={mealId} />
            {comments.data.map((comment) => (
                <CommentCard key={comment.id} comment={comment} />
            ))}
            <Pagination pagination={comments} />
        </div>
    );
}
