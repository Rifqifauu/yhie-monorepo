import { ref, reactive, computed } from "vue";

// 1. Definisikan Interface
export interface Program {
  id?: number | string;
  title_en: string;
  title_id: string;
  description_en: string;
  description_id: string;
  image_path?: string;
  slug_en: string;
  slug_id: string;
  price_en?: number | string;
  price_id?: number | string;
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

// Interface tambahan untuk respons detail tunggal (Diperbaiki ke tipe generik T)
export interface SingleApiResponse<T> {
  data: T;
}

export const usePrograms = () => {
  const config = useRuntimeConfig();
  const { locale } = useI18n();
  const client = useSanctumClient();

  const page = ref(1);
  const searchInput = ref("");
  const search = ref("");

  const searchTerm = computed(() => search.value.trim());
  const isSubmitting = ref(false);

  const {
    data: apiResponse,
    status,
    error,
    refresh,
  } = useAsyncData<ApiResponse<Program>>(
    "programs-page",
    () =>
      client("/api/programs", {
        params: {
          page: page.value,
          search: searchTerm.value || undefined,
        },
      }),
    { watch: [page, searchTerm] },
  );

  // Computed Properties for Pagination
  const paginator = computed<PaginatedResponse<Program>>(() => {
    if (!apiResponse.value) return {} as PaginatedResponse<Program>;
    return apiResponse.value.data ?? ({} as PaginatedResponse<Program>);
  });

  const programs = computed<Program[]>(() => paginator.value.data ?? []);
  const totalPages = computed<number>(() => paginator.value.last_page ?? 1);
  const totalItems = computed<number>(() => paginator.value.total ?? 0);
  const fromItem = computed<number>(() => paginator.value.from ?? 0);
  const toItem = computed<number>(() => paginator.value.to ?? 0);
  const pending = computed<boolean>(() => status.value === "pending");

  // Locale-aware helpers
  const titleOf = (program: Program): string =>
    locale.value === "en" ? program.title_en : program.title_id;

  const descOf = (program: Program): string =>
    locale.value === "en" ? program.description_en : program.description_id;

  const slugOf = (program: Program): string =>
    locale.value === "en" ? program.slug_en : program.slug_id;

  const priceOf = (program: Program): number | string | undefined =>
    locale.value === "en" ? program.price_en : program.price_id;

  // Image URL builder (Safe URL generation)
  const backendUrl =
    (config.public.sanctum?.baseUrl as string) || "http://127.0.0.1:8000";

  const imageUrl = (path?: string): string => {
    if (!path) return "";
    if (path.startsWith("http")) return path;

    const base = backendUrl.endsWith("/")
      ? backendUrl.slice(0, -1)
      : backendUrl;
    const cleanPath = path.startsWith("/") ? path : `/${path}`;

    return `${base}${cleanPath}`;
  };

  // Price formatting
  const formatPrice = (price?: number | string): string => {
    if (price === null || price === undefined || price === "")
      return locale.value === "en" ? "Free" : "Gratis";

    const numericPrice = typeof price === "string" ? parseFloat(price) : price;
    if (isNaN(numericPrice)) return String(price);

    const currency = locale.value === "en" ? "USD" : "IDR";
    const localeTag = locale.value === "en" ? "en-US" : "id-ID";

    return new Intl.NumberFormat(localeTag, {
      style: "currency",
      currency,
      maximumFractionDigits: 0,
    }).format(numericPrice);
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

  // Actions
  const applySearch = () => {
    search.value = searchInput.value.trim();
    page.value = 1;
  };

  const clearSearch = () => {
    searchInput.value = "";
    search.value = "";
    page.value = 1;
  };

  const changePage = (target: number) => {
    if (target < 1 || target > totalPages.value) return;
    page.value = target;

    if (import.meta.client) {
      window.scrollTo({ top: 0, behavior: "smooth" });
    }
  };
  const fetchDetail = async (slug: string) => {
    return await useAsyncData<SingleApiResponse<Program>>(
      `program-detail-${slug}`,
      () => client(`/api/programs/${slug}`),
    );
  };

  const updateProgram = async (
    id: number | string,
    payload: FormData | Record<string, any>,
  ) => {
    isSubmitting.value = true;
    try {
      let body = payload;
      if (payload instanceof FormData && !payload.has("_method")) {
        payload.append("_method", "PUT");
      }

      const response = await client(`/api/programs/${id}`, {
        method: payload instanceof FormData ? "POST" : "PUT",
        body: body,
      });

      return { success: true, data: response };
    } catch (err: any) {
      return {
        success: false,
        error:
          err.data?.message || err.message || "Gagal memperbarui data program.",
      };
    } finally {
      isSubmitting.value = false;
    }
  };

  return {
    page,
    searchInput,
    searchTerm,
    programs,
    totalPages,
    totalItems,
    fromItem,
    toItem,
    pending,
    error,
    pageItems,
    titleOf,
    descOf,
    slugOf,
    priceOf,
    imageUrl,
    formatPrice,
    isSubmitting,
    applySearch,
    clearSearch,
    changePage,
    refresh,
    updateProgram,
    fetchDetail,
  };
};
