import { LucideIcon } from 'lucide-react';
import type { Config } from 'ziggy-js';


export interface SharedData {
    name: string;
    ziggy: Config & { location: string };
    [key: string]: unknown;
}