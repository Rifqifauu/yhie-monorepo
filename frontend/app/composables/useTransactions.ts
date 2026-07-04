export interface TransactionItem {
  id?: number | string;
  program_registration_id?: number | string;
  amount: number;
  payment_status?: 'pending' | 'completed' | 'failed' | 'expired';
  created_at?: string;
  program_registration?: any;
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

export const useTransactions = () => {
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
  } = useAsyncData<ApiResponse<TransactionItem>>(
    "transactions-page",
    () =>
      client("/api/transactions", {
        params: {
          page: page.value,
          search: searchTerm.value || undefined,
        },
      }),
    { watch: [page, searchTerm] },
  );

  const paginator = computed<PaginatedResponse<TransactionItem>>(() => {
    if (!apiResponse.value) return {} as PaginatedResponse<TransactionItem>;
    return apiResponse.value.data ?? ({} as PaginatedResponse<TransactionItem>);
  });

  const transactions = computed<TransactionItem[]>(() => paginator.value.data ?? []);
  const totalPages = computed<number>(() => paginator.value.last_page ?? 1);
  const totalItems = computed<number>(() => paginator.value.total ?? 0);
  const fromItem = computed<number>(() => paginator.value.from ?? 0);
  const toItem = computed<number>(() => paginator.value.to ?? 0);
  const pending = computed<boolean>(() => status.value === "pending");

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

    transactions,
    totalPages,
    totalItems,
    fromItem,
    toItem,
    pending,
    error,
    pageItems,

    applySearch,
    clearSearch,
    changePage,
    refresh,
  };
};
