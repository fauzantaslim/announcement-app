import axios, { AxiosError } from 'axios';
import { AnnouncementsResponse, AnnouncementDetailResponse } from '@/types/announcement';
import { LoginRequest, LoginResponse, LogoutResponse } from '@/types/auth';

const API_BASE_URL = 'http://pengumuman.test/api';

// Create axios instance
const api = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
  },
});

export interface FetchAnnouncementsParams {
  page?: number;
  per_page?: number;
  sort_by?: string;
  sort_order?: 'asc' | 'desc';
  search?: string;
}

export async function fetchAnnouncements(
  params: FetchAnnouncementsParams = {}
): Promise<AnnouncementsResponse | null> {
  try {
    const {
      page = 1,
      per_page = 10,
      sort_by = 'created_at',
      sort_order = 'desc',
      search = '',
    } = params;

    const response = await api.get<AnnouncementsResponse>('/announcements', {
      params: {
        page,
        per_page,
        sort_by,
        sort_order,
        search,
      },
    });

    return response.data;
  } catch (error) {
    if (error instanceof AxiosError) {
      console.error(`API Error: ${error.response?.status} ${error.response?.statusText}`);
    } else {
      console.error('Failed to fetch announcements:', error);
    }
    return null;
  }
}

export async function fetchAnnouncementById(
  id: string
): Promise<AnnouncementDetailResponse | null> {
  try {
    const response = await api.get<AnnouncementDetailResponse>(`/announcements/${id}`);
    return response.data;
  } catch (error) {
    if (error instanceof AxiosError) {
      console.error(`API Error: ${error.response?.status} ${error.response?.statusText}`);
    } else {
      console.error('Failed to fetch announcement:', error);
    }
    return null;
  }
}

export async function login(credentials: LoginRequest): Promise<LoginResponse> {
  try {
    const response = await api.post<LoginResponse>('/login', credentials);
    return response.data;
  } catch (error) {
    if (error instanceof AxiosError) {
      return {
        success: false,
        message: error.response?.data?.message || 'Login failed',
      };
    }
    console.error('Login error:', error);
    return {
      success: false,
      message: 'Network error. Please try again.',
    };
  }
}

export async function logout(token: string): Promise<LogoutResponse> {
  try {
    const response = await api.post<LogoutResponse>(
      '/logout',
      {},
      {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      }
    );
    return response.data;
  } catch (error) {
    if (error instanceof AxiosError) {
      return {
        success: false,
        message: error.response?.data?.message || 'Logout failed',
      };
    }
    console.error('Logout error:', error);
    return {
      success: false,
      message: 'Network error. Please try again.',
    };
  }
}

export async function createAnnouncement(
  data: FormData,
  token: string
): Promise<{ success: boolean; message?: string; data?: any }> {
  try {
    const response = await axios.post(`${API_BASE_URL}/announcements`, data, {
      headers: {
        Authorization: `Bearer ${token}`,
        'Content-Type': 'multipart/form-data',
      },
    });

    return { success: true, data: response.data };
  } catch (error) {
    if (error instanceof AxiosError) {
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to create announcement',
      };
    }
    console.error('Create announcement error:', error);
    return {
      success: false,
      message: 'Network error. Please try again.',
    };
  }
}

export async function updateAnnouncement(
  id: number,
  data: FormData,
  token: string
): Promise<{ success: boolean; message?: string; data?: any }> {
  try {
    // Laravel uses POST with _method=PUT for FormData
    const response = await axios.post(
      `${API_BASE_URL}/announcements/${id}`,
      data,
      {
        headers: {
          Authorization: `Bearer ${token}`,
          'Content-Type': 'multipart/form-data',
        },
      }
    );

    return { success: true, data: response.data };
  } catch (error) {
    if (error instanceof AxiosError) {
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to update announcement',
      };
    }
    console.error('Update announcement error:', error);
    return {
      success: false,
      message: 'Network error. Please try again.',
    };
  }
}

export async function deleteAnnouncement(
  id: number,
  token: string
): Promise<{ success: boolean; message?: string }> {
  try {
    await axios.delete(`${API_BASE_URL}/announcements/${id}`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });

    return { success: true };
  } catch (error) {
    if (error instanceof AxiosError) {
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to delete announcement',
      };
    }
    console.error('Delete announcement error:', error);
    return {
      success: false,
      message: 'Network error. Please try again.',
    };
  }
}
