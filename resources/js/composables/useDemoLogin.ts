import { useForm } from '@inertiajs/vue3';

export const DEMO_ACCOUNT = {
    name: 'Demo Reviewer',
    email: 'demo@syntavex.app',
    password: 'syntavex-demo',
} as const;

export function useDemoLogin() {
    const form = useForm({ email: '', password: '' });

    const enterDemo = () => {
        form.email = DEMO_ACCOUNT.email;
        form.password = DEMO_ACCOUNT.password;
        form.post(route('login'));
    };

    return { form, enterDemo };
}
