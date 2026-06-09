import { ref, computed, watch } from "vue";

export interface GalleryMedia {
  id?: number | string;
  title_en: string;
  title_id: string;
  description_en: string;
  description_id: string;
  category: string;
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
  const searchInput = ref("");
  const search = ref("");
  const isSubmitting = ref(false);
  const searchTerm = computed(() => search.value.trim());

  watch([category, search], () => {
    page.value = 1;
  });

  const {
    data: apiResponse,
    status,
    error,
    refresh,
  } = useAsyncData<ApiResponse<GalleryMedia>>(
    "gallery-page",
    () =>
      client("/api/media", {
        params: {
          page: page.value,
          category: category.value || undefined,
          search: searchTerm.value || undefined,
        },
      }),
    { watch: [page, category, searchTerm] },
  );

  const paginator = computed<PaginatedResponse<GalleryMedia>>(() => {
    return (
      apiResponse.value?.data ?? {
        data: [],
        current_page: 1,
        last_page: 1,
        total: 0,
        from: null,
        to: null,
      }
    );
  });

  const mediaItems = computed<GalleryMedia[]>(() => paginator.value.data);
  const totalPages = computed<number>(() => paginator.value.last_page);
  const totalItems = computed<number>(() => paginator.value.total);
  const fromItem = computed<number>(() => paginator.value.from ?? 0);
  const toItem = computed<number>(() => paginator.value.to ?? 0);
  const pending = computed<boolean>(() => status.value === "pending");

  const titleOf = (item: GalleryMedia): string => {
    if (!item) return "";
    return locale.value === "en" ? item.title_en : item.title_id;
  };

  const descOf = (item: GalleryMedia): string => {
    if (!item) return "";
    return locale.value === "en" ? item.description_en : item.description_id;
  };

  const backendUrl = config.public.sanctum?.baseUrl || "http://127.0.0.1:8000";

  const buildImageUrl = (path?: string): string => {
    if (!path) return "";
    if (path.startsWith("http")) return path;

    const base = backendUrl.endsWith("/")
      ? backendUrl.slice(0, -1)
      : backendUrl;
    const cleanPath = path.startsWith("/") ? path : `/${path}`;

    return `${base}${cleanPath}`;
  };

  const imagesOf = (item: GalleryMedia): string[] => {
    if (!item) return [];
    const img = item.image;
    if (!img) return [];

    if (Array.isArray(img)) {
      return img
        .filter((p) => typeof p === "string")
        .map((p: string) => buildImageUrl(p));
    }

    if (typeof img === "object" && img.path) {
      return [buildImageUrl(img.path)];
    }

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
        return [buildImageUrl(img)];
      }
    }

    return [];
  };

  const coverOf = (item: GalleryMedia): string => {
    const imgs = imagesOf(item);
    return imgs.length > 0 ? imgs[0] : "";
  };

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

  const applySearch = () => {
    search.value = searchInput.value.trim();
  };

  const clearSearch = () => {
    searchInput.value = "";
    search.value = "";
  };

  const fetchDetail = async (id: string | number) => {
    return await useAsyncData<GalleryMedia>(`media-detail-${id}`, () =>
      client(`/api/media/${id}`),
    );
  };

  const storeMedia = async (formData: FormData) => {
    isSubmitting.value = true;
    try {
      const response = await client("/api/media", {
        method: "POST",
        body: formData,
      });
      return { success: true, data: response };
    } catch (err: any) {
      return {
        success: false,
        error:
          err.data?.message || "Terjadi kesalahan saat menyimpan media baru.",
      };
    } finally {
      isSubmitting.value = false;
    }
  };

  const updateMedia = async (id: string | number, formData: FormData) => {
    isSubmitting.value = true;

    if (!formData.has("_method")) {
      formData.append("_method", "PUT");
    }

    try {
      const response = await client(`/api/media/${id}`, {
        method: "POST", // Di-spoof menjadi PUT di mata Laravel router
        body: formData,
      });
      return { success: true, data: response };
    } catch (err: any) {
      return {
        success: false,
        error: err.data?.message || "Terjadi kesalahan saat memperbarui media.",
      };
    } finally {
      isSubmitting.value = false;
    }
  };

  return {
    page,
    category,
    searchInput,
    searchTerm,
    pending,
    error,
    isSubmitting,
    mediaItems,
    totalPages,
    totalItems,
    fromItem,
    toItem,
    pageItems,
    titleOf,
    descOf,
    imagesOf,
    coverOf,
    buildImageUrl,
    changePage,
    applySearch,
    clearSearch,
    refresh,
    fetchDetail,
    storeMedia,
    updateMedia,
  };
};
