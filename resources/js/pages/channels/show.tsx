import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import AppLayout from '@/layouts/app-layout';
import { Video, type Channel } from '@/lib/types';
import { cn, formatNumber } from '@/lib/utils';
import { Deferred, Head, Link } from '@inertiajs/react';
import { ExternalLinkIcon, MessageCircleMoreIcon } from 'lucide-react';
import { DateTime } from 'luxon';

type PageProps = {
  channel: Channel;
  videos: Video[];
};

export default function ChannelDetail({ channel, videos }: PageProps) {
  return (
    <AppLayout
      breadcrumbs={[
        {
          title: 'Dashboard',
          href: '/dashboard',
        },
        {
          title: `${channel.name}'s Channel`,
          href: '#',
        },
      ]}
    >
      <Head title="Dashboard" />

      <div className="flex h-full flex-1 flex-col gap-4 rounded-xl">
        <header>
          <section className="relative h-52 w-full bg-cover bg-center" style={{ backgroundImage: `url(${channel.banner_url})` }}>
            <img className="absolute bottom-0 left-8 aspect-square h-2/3 translate-y-1/2 rounded-full" src={channel.image_url} />
          </section>

          <section className="flex justify-between py-4 pr-4 pl-48">
            <div>
              <h2 className="text-xl font-medium">{channel.name}</h2>
              <p className="text-sm text-neutral-500">{formatNumber(channel.subscriber_count, 0)} subscribers</p>
            </div>

            <a href={`https://www.youtube.com/channel/${channel.channel_id}`} target="_blank" rel="noopener noreferrer">
              <Button variant="destructive">
                Go to channel <ExternalLinkIcon />
              </Button>
            </a>
          </section>
        </header>

        <Deferred
          data="videos"
          fallback={
            <div className="grid h-full w-full place-items-center">
              <Spinner />
            </div>
          }
        >
          <section className="grid grid-cols-3 gap-4 p-4">
            {videos?.map((video) => (
              <Link
                href={route('videos.show', { video: video.video_id })}
                key={video.id}
                className={cn('group flex flex-col gap-2', video.visibility !== 'public' && 'opacity-50 transition hover:opacity-100')}
              >
                <div className="relative aspect-video overflow-hidden rounded-xl">
                  <img src={video.thumbnail_url} className="h-full w-full object-cover" alt={video.title} />
                </div>

                <div className="flex justify-between gap-4">
                  <div>
                    <h3 className="line-clamp-2 text-sm font-semibold">{video.title}</h3>
                    <p className="text-sm text-neutral-500">
                      {formatNumber(video.view_count, 0)} views • {DateTime.fromISO(video.published_at).toRelative()}
                    </p>
                  </div>

                  <div>
                    <p className="flex items-center gap-1 text-sm">
                      {video.comment_count} <MessageCircleMoreIcon size={16} />
                    </p>
                  </div>
                </div>
              </Link>
            ))}
          </section>
        </Deferred>
      </div>
    </AppLayout>
  );
}
