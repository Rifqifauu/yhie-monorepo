export const usePartners = () => {
    const config = useRuntimeConfig();
    const { locale } = useI18n();
    const client = useSanctumClient();

    const page = ref(1);
    const searchInput = ref("");
    const search = ref("");

    const searchTerm = computed(() => search.value.trim());

    const {
        data: apiResponse,
        status,
        error,
        refresh,
    } = useAsyncData(
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

    const paginator = computed(() => {
        if (!apiResponse.value) return {} as any;
        return (apiResponse.value as any)?.data ?? {};
    });

    const partners = computed(() => paginator.value?.data ?? []);
    const totalPages = computed(() => paginator.value?.last_page ?? 1);
    const totalItems = computed(() => paginator.value?.total ?? 0);
    const fromItem = computed(() => paginator.value?.from ?? 0);
    const toItem = computed(() => paginator.value?.to ?? 0);
    const pending = computed(() => status.value === "pending");

    // Locale-aware helpers
    const nameOf = (partner: any) =>
        locale.value === "en" ? partner.name_en : partner.name_id;

    const descOf = (partner: any) =>
        locale.value === "en"
            ? partner.description_en
            : partner.description_id;

    const slugOf = (partner: any) =>
        locale.value === "en" ? partner.slug_en : partner.slug_id;

    // Image URL
    const backendUrl =
        config.public.sanctum?.baseUrl || "http://127.0.0.1:8000";

    const imageUrl = (path?: string) => {
        if (!path) return "";
        if (path.startsWith("http")) return path;
        return `${backendUrl}${path.startsWith("/") ? path : `/${path}`}`;
    };

    // Pagination items with ellipsis
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
