export interface GalleryMedia {
  id?: number | string;
  title_en: string;
  title_id: string;
  description_en: string;
  description_id: string;
  image?: string | string[] | { path?: string; [key: string]: any } | any;
  [key: string]: any;
}

export interface PaginatedResponse<T> {
  data: T[];
  current_page: number;
  last_page: number;
  total: number;
  from: number | null;
  to: number | null;
}

export interface ApiResponse<T> {
  data: PaginatedResponse<T>;
}

export const useGallery = () => {
  const config = useRuntimeConfig();
  const { locale } = useI18n();
  const client = useSanctumClient();

  const page = ref(1);
  const category = ref("");

  watch(category, () => {
    page.value = 1;
  });

  const {
    data: apiResponse,
    status,
    error,
    refresh,
  } = useAsyncData<ApiResponse<GalleryMedia>>(
    "gallery-page", // Static key, cache will be overwritten on param change
    () =>
      client("/api/media", {
        params: {
          page: page.value,
          category: category.value || undefined,
        },
      }),
    { watch: [page, category] },
  );

  // Computed Properties for Pagination
  const paginator = computed<PaginatedResponse<GalleryMedia>>(() => {
    if (!apiResponse.value) return {} as PaginatedResponse<GalleryMedia>;
    return apiResponse.value.data ?? ({} as PaginatedResponse<GalleryMedia>);
  });

  const mediaItems = computed<GalleryMedia[]>(() => paginator.value.data ?? []);
  const totalPages = computed<number>(() => paginator.value.last_page ?? 1);
  const totalItems = computed<number>(() => paginator.value.total ?? 0);
  const pending = computed<boolean>(() => status.value === "pending");

  const titleOf = (item: GalleryMedia): string =>
    locale.value === "en" ? item.title_en : item.title_id;

  const descOf = (item: GalleryMedia): string =>
    locale.value === "en" ? item.description_en : item.description_id;

  const backendUrl = config.public.sanctum?.baseUrl;

  const buildImageUrl = (path?: string): string => {
    if (!path) return "";
    if (path.startsWith("http")) return path;

    // Mencegah double slash (e.g., http://127.0.0.1:8000//storage/...)
    const base = backendUrl.endsWith("/")
      ? backendUrl.slice(0, -1)
      : backendUrl;
    const cleanPath = path.startsWith("/") ? path : `/${path}`;

    return `${base}${cleanPath}`;
  };

  // Extract images from the media item
  const imagesOf = (item: GalleryMedia): string[] => {
    const img = item.image;
    if (!img) return [];

    // Array of path strings (from controller store)
    if (Array.isArray(img)) {
      // Pastikan setiap elemen adalah string sebelum dibuild
      return img
        .filter((p) => typeof p === "string")
        .map((p: string) => buildImageUrl(p));
    }

    // Object with 'path' key (from seeder)
    if (typeof img === "object" && img.path) {
      return [buildImageUrl(img.path)];
    }

    // Plain string (JSON stringified or single path)
    if (typeof img === "string") {
      try {
        const parsed = JSON.parse(img);
        if (Array.isArray(parsed)) {
          return parsed
            .filter((p) => typeof p === "string")
            .map((p: string) => buildImageUrl(p));
        }
        if (typeof parsed === "object" && parsed.path) {
          return [buildImageUrl(parsed.path)];
        }
      } catch (e) {
        // Jika bukan JSON yang valid, asumsikan itu adalah path gambar langsung
        return [buildImageUrl(img)];
      }
    }

    return [];
  };

  // First image (for thumbnail/cover display)
  const coverOf = (item: GalleryMedia): string => {
    const imgs = imagesOf(item);
    return imgs.length > 0 ? imgs[0] : "";
  };

  // Pagination items with ellipsis (UI generator)
  const pageItems = computed(() => {
    const last = totalPages.value;
    const current = page.value;

    if (last <= 7) {
      return Array.from({ length: last }, (_, i) => i + 1);
    }

    const items: Array<number | "ellipsis"> = [1];
    const start = Math.max(2, current - 1);
    const end = Math.min(last - 1, current + 1);

    if (start > 2) items.push("ellipsis");
    for (let i = start; i <= end; i += 1) items.push(i);
    if (end < last - 1) items.push("ellipsis");

    items.push(last);
    return items;
  });

  const changePage = (target: number) => {
    if (target < 1 || target > totalPages.value) return;
    page.value = target;

    if (import.meta.client) {
      window.scrollTo({ top: 0, behavior: "smooth" });
    }
  };

  return {
    // State
    page,
    category,

    // Computed Data
    mediaItems,
    totalPages,
    totalItems,
    pending,
    error,
    pageItems,

    // Helpers
    titleOf,
    descOf,
    imagesOf,
    coverOf,

    // Methods
    changePage,
    refresh,
  };
};
