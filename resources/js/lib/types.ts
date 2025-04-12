export type Channel = {
  channel_id: string;
  created_at: string;
  id: number;
  image_url: string;
  name: string;
  subscriber_count: number;
  video_count: number;
  updated_at: string;
  user_id: number;
  banner_url: string;
};

export type Video = {
  channel_id: number;
  comment_count: number;
  created_at: string;
  description: string;
  id: number;
  published_at: string;
  thumbnail_url: string;
  title: string;
  updated_at: string;
  video_id: string;
  view_count: number;
  visibility: 'public' | 'private' | 'unlisted';
  livestream_status: 'live' | 'upcoming' | 'none';
};

export type Comment = {
  author_channel_id: string;
  author_name: string;
  comment_id: string;
  created_at: string;
  id: number;
  like_count: number;
  published_at: string;
  removed_at: string | null;
  spam_probability: number;
  text: string;
  updated_at: string;
  video_id: number;
};
