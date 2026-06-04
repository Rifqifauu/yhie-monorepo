// 1. Definisikan Interface
export interface Partner {
  id?: number | string;
  name_en: string;
  name_id: string;
  description_en: string;
  description_id: string;
  slug_en: string;
  slug_id: string;
  // Tambahkan properti lain yang relevan dari backend (misal: logo, website, dll)
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

export const usePartners = () => {
  const config = useRuntimeConfig();
  const { locale } = useI18n();
  const client = useSanctumClient();

  // State
  const page = ref(1);
  const searchInput = ref("");
  const search = ref("");

  const searchTerm = computed(() => search.value.trim());

  // Fetching Data
  const {
    data: apiResponse,
    status,
    error,
    refresh,
  } = useAsyncData<ApiResponse<Partner>>(
    "partners-page",
    () =>
      client("/api/partners", {
        params: {
          page: page.value,
          search: searchTerm.value || undefined,
        },
      }),
    { watch: [page, searchTerm] },
  );

  // Computed Properties for Pagination
  const paginator = computed<PaginatedResponse<Partner>>(() => {
    if (!apiResponse.value) return {} as PaginatedResponse<Partner>;
    return apiResponse.value.data ?? ({} as PaginatedResponse<Partner>);
  });

  const partners = computed<Partner[]>(() => paginator.value.data ?? []);
  const totalPages = computed<number>(() => paginator.value.last_page ?? 1);
  const totalItems = computed<number>(() => paginator.value.total ?? 0);
  const fromItem = computed<number>(() => paginator.value.from ?? 0);
  const toItem = computed<number>(() => paginator.value.to ?? 0);
  const pending = computed<boolean>(() => status.value === "pending");

  // Locale-aware helpers
  const nameOf = (partner: Partner): string =>
    locale.value === "en" ? partner.name_en : partner.name_id;

  const descOf = (partner: Partner): string =>
    locale.value === "en" ? partner.description_en : partner.description_id;

  const slugOf = (partner: Partner): string =>
    locale.value === "en" ? partner.slug_en : partner.slug_id;

  // Image URL builder (Safe URL generation)
  const backendUrl =
    (config.public.sanctum?.baseUrl as string) || "http://127.0.0.1:8000";

  const imageUrl = (path?: string): string => {
    if (!path) return "";
    if (path.startsWith("http")) return path;

    // Mencegah double slash (e.g., http://127.0.0.1:8000//storage/...)
    const base = backendUrl.endsWith("/")
      ? backendUrl.slice(0, -1)
      : backendUrl;
    const cleanPath = path.startsWith("/") ? path : `/${path}`;

    return `${base}${cleanPath}`;
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
    page.value = 1; // Reset halaman ke 1 saat melakukan pencarian baru
  };

  const clearSearch = () => {
    searchInput.value = "";
    search.value = "";
    page.value = 1; // Reset halaman ke 1 saat pencarian dihapus
  };

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
    searchInput,
    searchTerm,

    // Data
    partners,
    totalPages,
    totalItems,
    fromItem,
    toItem,
    pending,
    error,
    pageItems,

    // Helpers
    nameOf,
    descOf,
    slugOf,
    imageUrl,

    // Actions
    applySearch,
    clearSearch,
    changePage,
    refresh,
  };
};
