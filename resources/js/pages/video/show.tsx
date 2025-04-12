import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import { Tooltip, TooltipContent, TooltipTrigger } from '@/components/ui/tooltip';
import AppLayout from '@/layouts/app-layout';
import { Comment, Video, type Channel } from '@/lib/types';
import { cn, formatNumber } from '@/lib/utils';
import { Deferred, Head, Link, router } from '@inertiajs/react';
import { AlertCircle, ArrowLeft, ExternalLinkIcon, Info, RefreshCw } from 'lucide-react';
import Markdown from 'react-markdown';

type PageProps = {
  channel: Channel;
  video: Video;
  comments: Comment[];
};

export default function ChannelDetail({ channel, video, comments }: PageProps) {
  return (
    <AppLayout
      breadcrumbs={[
        {
          title: 'Dashboard',
          href: '/dashboard',
        },
        {
          title: `${channel.name}'s Channel`,
          href: `/channels/${channel.channel_id}`,
        },
        {
          title: video.title,
          href: '#',
        },
      ]}
    >
      <Head title="Dashboard" />

      <div className="flex h-full flex-1 flex-col gap-4 rounded-xl">
        <section className="px-8 pt-4">
          <Link href={`/channels/${channel.channel_id}`}>
            <Button variant="ghost">
              <ArrowLeft className="mr-2" />
              Back to channel
            </Button>
          </Link>
        </section>

        <section className="flex gap-5 px-8">
          <img src={video.thumbnail_url} alt="" className="aspect-video h-48 rounded-lg" />

          <section className="w-full pt-2">
            <div className="flex items-center justify-between">
              <h2 className="text-xl font-medium">{video.title}</h2>

              <div className="flex items-center gap-2">
                <Button size="icon" variant="secondary" onClick={() => router.post(route('videos.refresh', video.id), { preserveScroll: true })}>
                  <RefreshCw />
                </Button>
                <a href={`https://youtube.com/watch?v=${video.video_id}`} target="_blank" rel="noopener noreferrer">
                  <Button variant="destructive">
                    Open on Youtube <ExternalLinkIcon />
                  </Button>
                </a>
              </div>
            </div>
            <p className="mb-2 text-neutral-600">{formatNumber(video.view_count, 0)} views</p>
            <div className="h-32 overflow-hidden mask-b-from-30% mask-b-to-95% text-neutral-500">
              <Markdown>{video.description}</Markdown>
            </div>
          </section>
        </section>

        <section className="px-8 pb-8">
          <h3 className="font-medium">Comments ({comments?.length ?? 0})</h3>

          <Deferred
            data="comments"
            fallback={
              <div className="grid h-full w-full place-items-center">
                <Spinner />
              </div>
            }
          >
            <div className="mt-4 space-y-4">
              {comments?.map((comment) => (
                <div key={comment.comment_id} className="flex gap-2 overflow-hidden rounded-lg border p-4">
                  <div className="flex-1">
                    <div className="flex justify-between">
                      <div className="flex items-center gap-2">
                        <span className="font-medium">{comment.author_name}</span>
                        <span className="text-sm text-neutral-500">{new Date(comment.published_at).toLocaleDateString()}</span>
                      </div>

                      <div className="flex items-center gap-2">
                        {comment.spam_probability > 0.5 ? (
                          <Tooltip>
                            <TooltipTrigger>
                              <span
                                className={cn(
                                  'flex items-center gap-1 rounded-full px-2 py-1 text-xs font-medium',
                                  comment.spam_probability > 0.75 ? 'bg-red-100 text-red-600' : 'bg-yellow-100 text-yellow-600',
                                )}
                              >
                                <AlertCircle size={12} />
                                Spam
                              </span>
                            </TooltipTrigger>

                            <TooltipContent>Spam probable ({Math.round(comment.spam_probability * 100)}%)</TooltipContent>
                          </Tooltip>
                        ) : (
                          <Tooltip>
                            <TooltipTrigger>
                              <Info size="16" className="text-neutral-300" />
                            </TooltipTrigger>
                            <TooltipContent>Not spam ({Math.round(comment.spam_probability * 100)}%)</TooltipContent>
                          </Tooltip>
                        )}
                        <Button
                          className="hover:text-neutral-700"
                          variant="ghost"
                          size="sm"
                          onClick={() => router.delete(route('comments.destroy', comment.id), { preserveScroll: true })}
                        >
                          Delete
                        </Button>
                      </div>
                    </div>
                    <p className="text-wrap text-neutral-700">{comment.text}</p>
                    <div className="mt-2 flex items-center gap-4 text-sm text-neutral-500">
                      <span className="flex items-center gap-1">👍 {formatNumber(comment.like_count, 0)}</span>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          </Deferred>
        </section>
      </div>
    </AppLayout>
  );
}
