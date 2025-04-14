import { Button } from '@/components/ui/button';
import { type SharedData } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import { CheckCircle, Shield, Youtube, Zap } from 'lucide-react';

export default function Welcome() {
  const { auth } = usePage<SharedData>().props;

  return (
    <>
      <Head title="Welcome">
        <link rel="preconnect" href="https://fonts.bunny.net" />
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
      </Head>
      <div className="flex min-h-screen flex-col">
        <header className="fixed inset-x-0 top-0 mx-auto flex h-16 w-full items-center bg-white px-8 sm:px-16 md:px-32 lg:px-40 xl:px-56">
          <Link className="flex items-center justify-center" href="#">
            <Shield className="h-6 w-6 text-rose-500" />
            <span className="ml-2 text-xl font-bold">Joedoldel</span>
          </Link>

          <nav className="ml-auto flex items-center gap-4 sm:gap-6">
            <Link className="text-sm font-medium underline-offset-4 hover:underline" href="#features">
              Fitur
            </Link>
            <Link className="text-sm font-medium underline-offset-4 hover:underline" href="#how-it-works">
              Cara Kerja
            </Link>

            {auth.user ? (
              <Link
                href={route('dashboard')}
                className="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
              >
                Dashboard
              </Link>
            ) : (
              <>
                <Link
                  href={route('login')}
                  className="inline-block rounded-sm border border-transparent px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#19140035] dark:text-[#EDEDEC] dark:hover:border-[#3E3E3A]"
                >
                  Log in
                </Link>
              </>
            )}
          </nav>
        </header>
        <main className="flex-1">
          <section className="w-full py-12 md:py-24 lg:py-32 xl:py-48">
            <div className="container mx-auto max-w-7xl px-6 sm:px-8 md:px-12 lg:px-16 xl:px-20">
              <div className="grid gap-6 lg:grid-cols-2 lg:gap-12 xl:grid-cols-2">
                <div className="flex flex-col justify-center space-y-4">
                  <div className="space-y-2">
                    <h1 className="text-3xl font-bold tracking-tighter sm:text-4xl md:text-5xl lg:text-6xl/none">
                      Bersihkan Komentar Spam YouTube Anda
                    </h1>
                    <p className="max-w-[600px] text-gray-500 md:text-xl dark:text-gray-400">
                      Joedoldel membantu Anda menghapus komentar spam "judol" dengan cepat dan efisien. Lindungi komunitas YouTube Anda.
                    </p>
                  </div>
                  <div className="flex flex-col gap-2 min-[400px]:flex-row">
                    <Link href={route('login')}>
                      <Button size="lg" className="bg-rose-600 hover:bg-rose-700">
                        Mulai
                      </Button>
                    </Link>

                    <Link href="#how-it-works">
                      <Button size="lg" variant="outline">
                        Pelajari Lebih Lanjut
                      </Button>
                    </Link>
                  </div>
                </div>
              </div>
            </div>
          </section>
          <section id="features" className="w-full bg-gray-50 py-12 md:py-24 lg:py-32 dark:bg-gray-900">
            <div className="container mx-auto max-w-7xl px-6 sm:px-8 md:px-12 lg:px-16 xl:px-20">
              <div className="flex flex-col items-center justify-center space-y-4 text-center">
                <div className="space-y-2">
                  <div className="inline-block rounded-lg bg-rose-100 px-3 py-1 text-sm text-rose-600 dark:bg-rose-800/30 dark:text-rose-400">
                    Fitur Utama
                  </div>
                  <h2 className="text-3xl font-bold tracking-tighter md:text-4xl">Moderasi Komentar yang Lebih Cerdas</h2>
                  <p className="max-w-[900px] text-gray-500 md:text-xl/relaxed lg:text-base/relaxed xl:text-xl/relaxed dark:text-gray-400">
                    Joedoldel menawarkan alat yang Anda butuhkan untuk menjaga komentar YouTube Anda bebas dari spam.
                  </p>
                </div>
              </div>
              <div className="mx-auto mt-12 grid max-w-5xl grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                <div className="flex flex-col items-center space-y-2 rounded-lg border p-6 shadow-sm">
                  <div className="rounded-full bg-rose-100 p-3 text-rose-600 dark:bg-rose-800/30 dark:text-rose-400">
                    <Zap className="h-6 w-6" />
                  </div>
                  <h3 className="text-xl font-bold">Deteksi Otomatis</h3>
                  <p className="text-center text-gray-500 dark:text-gray-400">
                    Algoritma cerdas yang mendeteksi komentar spam "judol" secara otomatis.
                  </p>
                </div>
                <div className="flex flex-col items-center space-y-2 rounded-lg border p-6 shadow-sm">
                  <div className="rounded-full bg-rose-100 p-3 text-rose-600 dark:bg-rose-800/30 dark:text-rose-400">
                    <Youtube className="h-6 w-6" />
                  </div>
                  <h3 className="text-xl font-bold">Integrasi YouTube</h3>
                  <p className="text-center text-gray-500 dark:text-gray-400">
                    Terhubung langsung dengan akun YouTube Anda untuk moderasi yang mulus.
                  </p>
                </div>
                <div className="flex flex-col items-center space-y-2 rounded-lg border p-6 shadow-sm">
                  <div className="rounded-full bg-rose-100 p-3 text-rose-600 dark:bg-rose-800/30 dark:text-rose-400">
                    <CheckCircle className="h-6 w-6" />
                  </div>
                  <h3 className="text-xl font-bold">Moderasi Massal</h3>
                  <p className="text-center text-gray-500 dark:text-gray-400">Hapus ratusan komentar spam dengan sekali klik.</p>
                </div>
              </div>
            </div>
          </section>
          <section id="how-it-works" className="w-full py-12 md:py-24 lg:py-32">
            <div className="container mx-auto max-w-7xl px-6 sm:px-8 md:px-12 lg:px-16 xl:px-20">
              <div className="flex flex-col items-center justify-center space-y-4 text-center">
                <div className="space-y-2">
                  <div className="inline-block rounded-lg bg-rose-100 px-3 py-1 text-sm text-rose-600 dark:bg-rose-800/30 dark:text-rose-400">
                    Cara Kerja
                  </div>
                  <h2 className="text-3xl font-bold tracking-tighter md:text-4xl">Mudah Digunakan, Hasil Maksimal</h2>
                  <p className="max-w-[900px] text-gray-500 md:text-xl/relaxed lg:text-base/relaxed xl:text-xl/relaxed dark:text-gray-400">
                    Lihat bagaimana Joedoldel bekerja untuk membersihkan komentar YouTube Anda.
                  </p>
                </div>
              </div>
              <div className="mx-auto mt-12 grid max-w-5xl grid-cols-1 gap-8 md:grid-cols-3">
                <div className="flex flex-col items-center space-y-2 rounded-lg p-4">
                  <div className="flex h-12 w-12 items-center justify-center rounded-full bg-rose-600 text-lg font-semibold text-white">1</div>
                  <h3 className="text-xl font-bold">Hubungkan Akun</h3>
                  <p className="text-center text-gray-500 dark:text-gray-400">Hubungkan akun YouTube Anda dengan Joedoldel dalam beberapa klik.</p>
                </div>
                <div className="flex flex-col items-center space-y-2 rounded-lg p-4">
                  <div className="flex h-12 w-12 items-center justify-center rounded-full bg-rose-600 text-lg font-semibold text-white">2</div>
                  <h3 className="text-xl font-bold">Atur Preferensi</h3>
                  <p className="text-center text-gray-500 dark:text-gray-400">Sesuaikan pengaturan deteksi spam sesuai kebutuhan Anda.</p>
                </div>
                <div className="flex flex-col items-center space-y-2 rounded-lg p-4">
                  <div className="flex h-12 w-12 items-center justify-center rounded-full bg-rose-600 text-lg font-semibold text-white">3</div>
                  <h3 className="text-xl font-bold">Bersihkan Komentar</h3>
                  <p className="text-center text-gray-500 dark:text-gray-400">Jalankan moderasi dan lihat komentar spam hilang dalam sekejap.</p>
                </div>
              </div>
            </div>
          </section>
        </main>
        <footer className="mx-auto flex w-full max-w-7xl shrink-0 flex-col items-center gap-2 border-t px-6 py-6 sm:flex-row sm:px-8 md:px-12 lg:px-16 xl:px-20">
          <p className="text-xs text-gray-500 dark:text-gray-400">© {new Date().getFullYear()} Joedoldel. Semua hak dilindungi.</p>
          <nav className="flex gap-4 sm:ml-auto sm:gap-6">
            {/* <Link className="text-xs underline-offset-4 hover:underline" href="#">
              Syarat & Ketentuan
            </Link>
            <Link className="text-xs underline-offset-4 hover:underline" href="#">
              Kebijakan Privasi
            </Link> */}
            <Link className="text-xs underline-offset-4 hover:underline" href="https://discord.gg/Bq7nSHtAM3" target="_blank" rel="noreferrer">
              Discord
            </Link>
          </nav>
        </footer>
      </div>
    </>
  );
}
