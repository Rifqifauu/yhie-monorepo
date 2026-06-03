export const useArticles = () => {
    const config = useRuntimeConfig();
    const { locale } = useI18n();
    const client = useSanctumClient();

    const page = ref(1);
    const searchInput = ref("");
    const search = ref("");
    const category = ref("");

    const searchTerm = computed(() => search.value.trim());

    watch(category, () => {
        page.value = 1;
    });

    watch(search, () => {
        page.value = 1;
    });

    const {
        data: apiResponse,
        status,
        error,
        refresh,
    } = useAsyncData(
        "articles-page",
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

    const paginator = computed(() => {
        if (!apiResponse.value) return {} as any;
        return (apiResponse.value as any)?.data ?? {};
    });

    const articles = computed(() => paginator.value?.data ?? []);
    const totalPages = computed(() => paginator.value?.last_page ?? 1);
    const totalItems = computed(() => paginator.value?.total ?? 0);
    const fromItem = computed(() => paginator.value?.from ?? 0);
    const toItem = computed(() => paginator.value?.to ?? 0);
    const pending = computed(() => status.value === "pending");

    // Locale-aware helpers
    const titleOf = (article: any) =>
        locale.value === "en" ? article.title_en : article.title_id;

    const contentOf = (article: any) =>
        locale.value === "en"
            ? article.content_en
            : article.content_id;

    const slugOf = (article: any) =>
        locale.value === "en" ? article.slug_en : article.slug_id;

    // Image URL builder
    const backendUrl =
        config.public.sanctum?.baseUrl || "http://127.0.0.1:8000";

    const buildImageUrl = (path?: string) => {
        if (!path) return "";
        if (path.startsWith("http")) return path;
        return `${backendUrl}${path.startsWith("/") ? path : `/${path}`}`;
    };

    // Extract cover image
    const coverOf = (article: any): string => {
        const img = article.image;
        if (!img) return "";

        // Array of path strings
        if (Array.isArray(img) && img.length > 0) {
            return buildImageUrl(img[0]);
        }

        // Object with 'path' key
        if (typeof img === "object" && img.path) {
            return buildImageUrl(img.path);
        }

        // Plain string
        if (typeof img === "string") {
            try {
                // Check if it's stored as encoded JSON string
                const parsed = JSON.parse(img);
                if (Array.isArray(parsed) && parsed.length > 0) {
                    return buildImageUrl(parsed[0]);
                }
                if (typeof parsed === "object" && parsed.path) {
                    return buildImageUrl(parsed.path);
                }
            } catch (e) {
                return buildImageUrl(img);
            }
        }

        return "";
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
        if (import.meta.client) {
            window.scrollTo({ top: 0, behavior: "smooth" });
        }
    };

    return {
        // State
        page,
        searchInput,
        searchTerm,
        category,
        // Data
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
        // Actions
        applySearch,
        clearSearch,
        changePage,
        refresh,
    };
};
