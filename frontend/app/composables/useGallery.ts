export const useGallery = () => {
    const config = useRuntimeConfig();
    const { locale } = useI18n();
    const client = useSanctumClient();

    const page = ref(1);
    const category = ref("");

    watch(category, () => {
        page.value = 1;
    });

    const {
        data: apiResponse,
        status,
        error,
        refresh,
    } = useAsyncData(
        "gallery-page",
        () =>
            client("/api/media", {
                params: {
                    page: page.value,
                    category: category.value || undefined,
                },
            }),
        { watch: [page, category] },
    );

    const paginator = computed(() => {
        if (!apiResponse.value) return {} as any;
        return (apiResponse.value as any)?.data ?? {};
    });

    const mediaItems = computed(() => paginator.value?.data ?? []);
    const totalPages = computed(() => paginator.value?.last_page ?? 1);
    const totalItems = computed(() => paginator.value?.total ?? 0);
    const pending = computed(() => status.value === "pending");

    // Locale-aware helpers
    const titleOf = (item: any) =>
        locale.value === "en" ? item.title_en : item.title_id;

    const descOf = (item: any) =>
        locale.value === "en"
            ? item.description_en
            : item.description_id;

    // Image URL builder
    const backendUrl =
        config.public.sanctum?.baseUrl || "http://127.0.0.1:8000";

    const buildImageUrl = (path?: string) => {
        if (!path) return "";
        if (path.startsWith("http")) return path;
        return `${backendUrl}${path.startsWith("/") ? path : `/${path}`}`;
    };

    // Extract images from the media item
    // Handles both seeder format { path: "..." } and controller format ["/storage/media/file.jpg", ...]
    const imagesOf = (item: any): string[] => {
        const img = item.image;
        if (!img) return [];

        // Array of path strings from controller store()
        if (Array.isArray(img)) {
            return img.map((p: string) => buildImageUrl(p));
        }

        // Object with 'path' key from seeder
        if (typeof img === "object" && img.path) {
            return [buildImageUrl(img.path)];
        }

        // Plain string
        if (typeof img === "string") {
            return [buildImageUrl(img)];
        }

        return [];
    };

    // First image (for thumbnail/cover display)
    const coverOf = (item: any): string => {
        const imgs = imagesOf(item);
        return imgs.length > 0 ? imgs[0] : "";
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

    const changePage = (target: number) => {
        if (target < 1 || target > totalPages.value) return;
        page.value = target;
        if (import.meta.client) {
            window.scrollTo({ top: 0, behavior: "smooth" });
        }
    };

    return {
        page,
        category,
        mediaItems,
        totalPages,
        totalItems,
        pending,
        error,
        pageItems,
        titleOf,
        descOf,
        imagesOf,
        coverOf,
        changePage,
        refresh,
    };
};
