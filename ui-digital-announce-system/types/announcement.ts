 export interface Announcement {
  id: number;
  title: string;
  content: string;
  image_path: string | null;
  publish_at: string;
  is_active: boolean;
  created_at: string;
  updated_at: string;
}

export interface PaginationMeta {
  current_page: number;
  per_page: number;
  total: number;
  last_page: number;
  from: number;
  to: number;
}

export interface AnnouncementsResponse {
  success: boolean;
  data: Announcement[];
  pagination: PaginationMeta;
}

export interface AnnouncementDetailResponse {
  success: boolean;
  data: Announcement;
}
