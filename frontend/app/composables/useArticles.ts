import { is } from "@nuxt/ui/runtime/locale/index.js";

// 1. Definisikan Interface untuk Type Safety
export interface Article {
  title_en: string;
  title_id: string;
  content_en: string;
  content_id: string;
  slug_en: string;
  slug_id: string;
  image?: string | any[] | { path?: string; [key: string]: any } | any;
  is_published: boolean;
  [key: string]: any; // Fallback untuk properti tambahan dari backend
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

export const useArticles = () => {
  const config = useRuntimeConfig();
  const { locale } = useI18n();
  const client = useSanctumClient();

  const page = ref(1);
  const searchInput = ref("");
  const search = ref("");
  const category = ref("");

  const searchTerm = computed(() => search.value.trim());

  const isSubmitting = ref(false);

  watch([category, search], () => {
    page.value = 1;
  });

  // Kolom search dikosongkan (backspace) -> otomatis tampilkan semua data lagi
  watch(searchInput, (val) => {
    if (!val.trim()) search.value = "";
  });

  const {
    data: apiResponse,
    status,
    error,
    refresh,
  } = useAsyncData<ApiResponse<Article>>(
    "articles-page", // Menggunakan static key, cache di-overwrite saat parameter berubah (hemat memori)
    () =>
      client("/api/articles", {
        params: {
          page: page.value,
          category: category.value || undefined,
          search: searchTerm.value || undefined,
        },
      }),
    { watch: [page, category, searchTerm] },
  );

  const paginator = computed<PaginatedResponse<Article>>(() => {
    if (!apiResponse.value) return {} as PaginatedResponse<Article>;
    return apiResponse.value.data ?? ({} as PaginatedResponse<Article>);
  });

  const articles = computed<Article[]>(() => paginator.value.data ?? []);
  const totalPages = computed<number>(() => paginator.value.last_page ?? 1);
  const totalItems = computed<number>(() => paginator.value.total ?? 0);
  const fromItem = computed<number>(() => paginator.value.from ?? 0);
  const toItem = computed<number>(() => paginator.value.to ?? 0);
  const pending = computed<boolean>(() => status.value === "pending");

  const imageUrl = (path?: string): string => {
    if (!path) return "";
    if (path.startsWith("http")) return path;

    const base = backendUrl.endsWith("/")
      ? backendUrl.slice(0, -1)
      : backendUrl;
    const cleanPath = path.startsWith("/") ? path : `/${path}`;

    return `${base}${cleanPath}`;
  };
  // Helper Functions (Localization)
  const titleOf = (article: Article): string =>
    locale.value === "en" ? article.title_en : article.title_id;

  const contentOf = (article: Article): string =>
    locale.value === "en" ? article.content_en : article.content_id;

  const slugOf = (article: Article): string =>
    locale.value === "en" ? article.slug_en : article.slug_id;

  // Image Handling
  const backendUrl = config.public.sanctum?.baseUrl as string | undefined;

  const buildImageUrl = (path?: string): string => {
    if (!path) return "";
    if (path.startsWith("http")) return path;

    const base = backendUrl?.endsWith("/")
      ? backendUrl.slice(0, -1)
      : backendUrl || "";
    const cleanPath = path.startsWith("/") ? path : `/${path}`;

    return `${base}${cleanPath}`;
  };

  const coverOf = (article: Article): string => {
    const img = article.image;
    if (!img) return "";

    if (Array.isArray(img) && img.length > 0) {
      return buildImageUrl(img[0]);
    }

    if (typeof img === "object" && img.path) {
      return buildImageUrl(img.path);
    }

    if (typeof img === "string") {
      try {
        const parsed = JSON.parse(img);
        if (Array.isArray(parsed) && parsed.length > 0) {
          return buildImageUrl(parsed[0]);
        }
        if (typeof parsed === "object" && parsed.path) {
          return buildImageUrl(parsed.path);
        }
      } catch (e) {
        // Jika gagal parsing, asumsikan string tersebut adalah path/URL langsung
        return buildImageUrl(img);
      }
    }
    return "";
  };

  // Pagination Logic (Generator array untuk UI Halaman)
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

  // Actions
  const applySearch = () => {
    search.value = searchInput.value.trim();
  };

  const clearSearch = () => {
    searchInput.value = "";
    search.value = "";
  };

  const changePage = (target: number) => {
    if (target < 1 || target > totalPages.value) return;
    page.value = target;

    // Pastikan window exists (hanya jalan di client-side)
    if (import.meta.client) {
      window.scrollTo({ top: 0, behavior: "smooth" });
    }
  };

  const fetchDetail = async (slug: string) => {
    return await useAsyncData<SingleApiResponse<Article>>(
      `article-detail-${slug}`,
      () => client(`/api/articles/${slug}`),
    );
  };
  const createArticle = async (payload: FormData | Record<string, any>) => {
    isSubmitting.value = true;
    try {
      const response = await client("/api/articles", {
        method: "POST",
        body: payload,
      });
      return { success: true, data: response };
    } catch (err: any) {
      return {
        success: false,
        error:
          err.data?.message || "Terjadi kesalahan saat menyimpan artikel baru.",
      };
    } finally {
      isSubmitting.value = false;
    }
  };

  const updateArticle = async (
    id: number | string,
    payload: FormData | Record<string, any>,
  ) => {
    isSubmitting.value = true;
    try {
      let body = payload;
      if (payload instanceof FormData && !payload.has("_method")) {
        payload.append("_method", "PUT");
      }

      const response = await client(`/api/articles/${id}`, {
        method: payload instanceof FormData ? "POST" : "PUT",
        body: body,
      });

      return { success: true, data: response };
    } catch (err: any) {
      return {
        success: false,
        error:
          err.data?.message || err.message || "Gagal memperbarui data artikel.",
      };
    } finally {
      isSubmitting.value = false;
    }
  };
  return {
    // State
    page,
    searchInput,
    searchTerm,
    category,

    // Computed Data
    articles,
    totalPages,
    totalItems,
    fromItem,
    toItem,
    pending,
    error,
    pageItems,

    // Helpers
    titleOf,
    contentOf,
    slugOf,
    coverOf,

    // Methods
    applySearch,
    clearSearch,
    changePage,
    refresh,
    fetchDetail,
    updateArticle,
    imageUrl,
    createArticle,
    isSubmitting,
  };
};
