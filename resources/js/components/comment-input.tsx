import { SharedData } from '@/types';
import { Link, useForm, usePage } from '@inertiajs/react';
import { LoaderCircle } from 'lucide-react';
import { FormEventHandler } from 'react';
import { Button } from './ui/button';
import { Input } from './ui/input';

interface CommentInputProps {
    mealId: number;
}

export default function CommentInput({ mealId }: CommentInputProps) {
    const { auth } = usePage<SharedData>().props;

    const { data, setData, post, processing, reset, errors } = useForm({
        content: '',
    });

    const submit: FormEventHandler = (e) => {
        console.log(data);
        e.preventDefault();
        post(route('comment', { meal: mealId }), {
            preserveScroll: true,
            only: ['comments'],
            onSuccess: () => reset('content'),
        });
    };

    return (
        <form onSubmit={submit}>
            <div className="flex items-center gap-2">
                <Input
                    type="text"
                    placeholder="Add a comment"
                    disabled={!auth.user}
                    required
                    value={data.content}
                    onChange={(e) => setData('content', e.target.value)}
                />
                {auth.user ? (
                    <>
                        <Button type="submit" disabled={processing}>
                            {processing && <LoaderCircle className="h-4 w-4 animate-spin" />}
                            Add
                        </Button>
                    </>
                ) : (
                    <Button asChild>
                        <Link href={route('login', { redirect_to: route('meals.show', mealId) })}>
                            Log in to add a comment
                        </Link>
                    </Button>
                )}
            </div>
            {errors.content && <p className="w-full text-red-500 mt-2">{errors.content}</p>}
        </form>
    );
}
