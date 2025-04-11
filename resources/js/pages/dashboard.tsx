import { Spinner } from '@/components/ui/spinner';
import AppLayout from '@/layouts/app-layout';
import { type Channel } from '@/lib/types';
import { formatNumber } from '@/lib/utils';
import { type BreadcrumbItem } from '@/types';
import { Deferred, Head, Link } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: '/dashboard',
  },
];

type PageProps = {
  channels: Channel[];
};

export default function Dashboard({ channels }: PageProps) {
  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title="Dashboard" />

      <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
        <Deferred
          data="channels"
          fallback={
            <div className="grid h-full w-full place-items-center">
              <Spinner />
            </div>
          }
        >
          <div className="grid auto-rows-min gap-4 md:grid-cols-3">
            {channels?.map((channel) => (
              <Link
                href={route('channels.show', channel.channel_id)}
                key={channel.id}
                className="border-sidebar-border/70 dark:border-sidebar-border relative flex items-center gap-4 overflow-hidden rounded-xl border p-2"
              >
                <img src={channel.image_url} className="h-20 rounded-md" />

                <div>
                  <h3 className="text-md font-semibold">{channel.name}</h3>
                  <p className="text-sm text-neutral-600">{formatNumber(channel.subscriber_count, 0)} subs</p>
                  <p className="text-sm text-neutral-600">{formatNumber(channel.video_count, 0)} videos</p>
                </div>
              </Link>
            ))}
          </div>
        </Deferred>
      </div>
    </AppLayout>
  );
}
