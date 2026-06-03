export const useSettings = () => {
    const client = useSanctumClient();
    const { locale } = useI18n();

    const {
        data: rawSettings,
        status,
        error,
        refresh,
    } = useAsyncData(
        "site-settings",
        () => client("/api/settings"),
        {
            transform: (res: any) => {
                const list = res?.data ?? [];
                const mapped: Record<string, string> = {};
                for (const item of list) {
                    if (item.key) {
                        mapped[item.key] = item.value;
                    }
                }
                return mapped;
            },
        }
    );

    const getSetting = (key: string, defaultValue = "") => {
        return rawSettings.value?.[key] ?? defaultValue;
    };

    const siteName = computed(() => getSetting("site_name", "Yayasan Hafiz Indonesia Emas"));
    const siteDescription = computed(() => getSetting("site_description", "Yayasan Hafiz Indonesia Emas (YHIE)"));
    const contactEmail = computed(() => getSetting("contact_email", "info@yhie.or.id"));
    const contactPhone = computed(() => getSetting("contact_phone", "+62 812-3456-7890"));
    const waNumber = computed(() => getSetting("wa_number", "6281234567890"));

    const aboutHistory = computed(() =>
        locale.value === "en"
            ? getSetting("about_history_en", "Yayasan Hafiz Indonesia Emas is a premium Quranic education foundation.")
            : getSetting("about_history_id", "Yayasan Hafiz Indonesia Emas adalah yayasan pendidikan Al-Qur'an terkemuka.")
    );

    const aboutVision = computed(() =>
        locale.value === "en"
            ? getSetting("about_vision_en", "To become a world-class Quranic education center.")
            : getSetting("about_vision_id", "Menjadi pusat pendidikan Al-Qur'an tingkat dunia.")
    );

    const aboutMission = computed(() =>
        locale.value === "en"
            ? getSetting("about_mission_en", "Spiritual and academic excellence.")
            : getSetting("about_mission_id", "Keunggulan spiritual dan akademik.")
    );

    const waLink = computed(() => {
        const cleanPhone = waNumber.value.replace(/\D/g, "");
        return `https://wa.me/${cleanPhone}`;
    });

    const isPending = computed(() => status.value === "pending");

    return {
        settings: rawSettings,
        isPending,
        error,
        getSetting,
        siteName,
        siteDescription,
        contactEmail,
        contactPhone,
        waNumber,
        waLink,
        aboutHistory,
        aboutVision,
        aboutMission,
        refresh,
    };
};
