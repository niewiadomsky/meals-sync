interface HeartIconProps {
    className?: string;
    fill?: string;
    stroke?: string;
    strokeWidth?: number;
    size?: number;
}

export default function HeartIcon({ className, fill = 'none', stroke = 'currentColor', strokeWidth = 1.5, size = 24 }: HeartIconProps) {
    return (
        <svg
            width={size}
            height={size}
            fill={fill}
            stroke={stroke}
            strokeWidth={strokeWidth}
            viewBox="0 0 24 24"
            strokeLinecap="round"
            strokeLinejoin="round"
            xmlns="http://www.w3.org/2000/svg"
            className={className}
        >
            <path d="M7.75 3.5C5.127 3.5 3 5.76 3 8.547 3 14.125 12 20.5 12 20.5s9-6.375 9-11.953C21 5.094 18.873 3.5 16.25 3.5c-1.86 0-3.47 1.136-4.25 2.79-.78-1.654-2.39-2.79-4.25-2.79" />
        </svg>
    );
}
