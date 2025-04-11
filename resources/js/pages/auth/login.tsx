import { Button } from '@/components/ui/button';
import { Head } from '@inertiajs/react';

export default function Login() {
  return (
    <>
      <Head title="Login" />

      <main className="grid h-screen w-screen place-items-center">
        <a href={route('google.login')}>
          <Button>Login with Google</Button>
        </a>
      </main>
    </>
  );
}
