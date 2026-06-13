export interface Setting {
  id: number | string;
  key: string;
  value: string;
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

export const useSettings = () => {
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
  } = useAsyncData<ApiResponse<Setting>>(
    "setting-page",
    () =>
      client("/api/settings", {
        params: {
          page: page.value,
          search: searchTerm.value || undefined,
        },
      }),
    { watch: [page, searchTerm] },
  );

  const paginator = computed<PaginatedResponse<Setting>>(() => {
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

  const settings = computed<Setting[]>(() => paginator.value.data);
  const totalPages = computed<number>(() => paginator.value.last_page);
  const totalItems = computed<number>(() => paginator.value.total);
  const fromItem = computed<number>(() => paginator.value.from ?? 0);
  const toItem = computed<number>(() => paginator.value.to ?? 0);
  const pending = computed<boolean>(() => status.value === "pending");

  const valueOf = (setting: Setting): string => {
    return setting.value;
  };
  const keyOf = (setting: Setting): string => {
    return setting.key;
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

  const applySearch = () => {
    search.value = searchInput.value.trim();
    page.value = 1; // Reset ke halaman 1 saat cari data baru
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

  const fetchDetail = async (id: string) => {
    return await useAsyncData<SingleApiResponse<Setting>>(
      `setting-detail-${id}`,
      () => client(`/api/settings/${id}`),
    );
  };

  const createSetting = async (payload: FormData | Record<string, any>) => {
    isSubmitting.value = true;
    try {
      const response = await client(`/api/settings`, {
        method: "POST",
        body: payload,
      });

      return { success: true, data: response };
    } catch (err: any) {
      return {
        success: false,
        error:
          err.data?.message || err.message || "Gagal membuat data program.",
      };
    } finally {
      isSubmitting.value = false;
    }
  };

  const updateSetting = async (
    id: number | string,
    payload: FormData | Record<string, any>,
  ) => {
    isSubmitting.value = true;
    try {
      let body = payload;
      if (payload instanceof FormData && !payload.has("_method")) {
        payload.append("_method", "PUT");
      }

      const response = await client(`/api/settings/${id}`, {
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
    // State & Computed
    page,
    searchInput,
    searchTerm,
    settings,
    totalPages,
    totalItems,
    fromItem,
    toItem,
    pending,
    error,
    pageItems,
    valueOf,
    keyOf,
    // Actions
    applySearch,
    clearSearch,
    changePage,
    refresh,
    isSubmitting,
    fetchDetail,
    updateSetting,
    createSetting,
  };
};
