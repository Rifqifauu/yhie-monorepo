export interface CertificateItem {
  id?: number | string;
  program_registration_id?: number | string;
  program_id?: number | string;
  external_user_id?: number | string | null;
  certificate_number: string;
  issued_at: string;
  file_path?: string | null;
  created_at?: string;
  program_registration?: any;
  program?: any;
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

export const useCertificates = () => {
  const client = useSanctumClient();
  const page = ref(1);
  const searchInput = ref("");
  const search = ref("");
  const searchTerm = computed(() => search.value.trim());

  watch(search, () => {
    page.value = 1;
  });

  const {
    data: apiResponse,
    status,
    error,
    refresh,
  } = useAsyncData<ApiResponse<CertificateItem>>(
    "certificates-page",
    () =>
      client("/api/certificates", {
        params: {
          page: page.value,
          search: searchTerm.value || undefined,
        },
      }),
    { watch: [page, searchTerm] },
  );

  const paginator = computed<PaginatedResponse<CertificateItem>>(() => {
    if (!apiResponse.value) return {} as PaginatedResponse<CertificateItem>;
    return apiResponse.value.data ?? ({} as PaginatedResponse<CertificateItem>);
  });

  const certificates = computed<CertificateItem[]>(() => paginator.value.data ?? []);
  const totalPages = computed<number>(() => paginator.value.last_page ?? 1);
  const totalItems = computed<number>(() => paginator.value.total ?? 0);
  const fromItem = computed<number>(() => paginator.value.from ?? 0);
  const toItem = computed<number>(() => paginator.value.to ?? 0);
  const pending = computed<boolean>(() => status.value === "pending");

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

    if (import.meta.client) window.scrollTo({ top: 0, behavior: "smooth" });
  };

  return {
    page,
    searchInput,
    searchTerm,

    certificates,
    totalPages,
    totalItems,
    fromItem,
    toItem,
    pending,
    error,

    applySearch,
    clearSearch,
    changePage,
    refresh,
  };
};
