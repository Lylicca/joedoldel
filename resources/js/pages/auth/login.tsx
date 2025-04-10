import { Button } from '@/components/ui/button';

export default function Login() {
    return (
        <main className="grid h-screen w-screen place-items-center">
            <a href={route('google.login')}>
                <Button>Login with Google</Button>
            </a>
        </main>
    );
}
