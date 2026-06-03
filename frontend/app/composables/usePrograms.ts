export const usePrograms = () => {
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

    const paginator = computed(() => {
        if (!apiResponse.value) return {} as any;
        return (apiResponse.value as any)?.data ?? {};
    });

    const programs = computed(() => paginator.value?.data ?? []);
    const totalPages = computed(() => paginator.value?.last_page ?? 1);
    const totalItems = computed(() => paginator.value?.total ?? 0);
    const fromItem = computed(() => paginator.value?.from ?? 0);
    const toItem = computed(() => paginator.value?.to ?? 0);
    const pending = computed(() => status.value === "pending");

    // Locale-aware helpers
    const titleOf = (program: any) =>
        locale.value === "en" ? program.title_en : program.title_id;

    const descOf = (program: any) =>
        locale.value === "en"
            ? program.description_en
            : program.description_id;

    const slugOf = (program: any) =>
        locale.value === "en" ? program.slug_en : program.slug_id;

    const priceOf = (program: any) =>
        locale.value === "en" ? program.price_en : program.price_id;

    // Image URL
    const backendUrl =
        config.public.sanctum?.baseUrl || "http://127.0.0.1:8000";

    const imageUrl = (path?: string) => {
        if (!path) return "";
        if (path.startsWith("http")) return path;
        return `${backendUrl}${path.startsWith("/") ? path : `/${path}`}`;
    };

    // Price formatting
    const formatPrice = (price?: number | string) => {
        if (price === null || price === undefined) return "Gratis";
        const numericPrice =
            typeof price === "string" ? parseFloat(price) : price;
        if (isNaN(numericPrice)) return String(price);
        const currency = locale.value === "en" ? "USD" : "IDR";
        const localeTag = locale.value === "en" ? "en-US" : "id-ID";
        return new Intl.NumberFormat(localeTag, {
            style: "currency",
            currency,
            maximumFractionDigits: 0,
        }).format(numericPrice);
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
        programs,
        totalPages,
        totalItems,
        fromItem,
        toItem,
        pending,
        error,
        pageItems,
        // Helpers
        titleOf,
        descOf,
        slugOf,
        priceOf,
        imageUrl,
        formatPrice,
        // Actions
        applySearch,
        clearSearch,
        changePage,
        refresh,
    };
};
