import { ref, computed } from "vue";

export interface Setting {
  id: number | string;
  key: string;
  value: string;
}

export interface ApiResponse<T> {
  data: T[];
}

export const useSettings = () => {
  const client = useSanctumClient();
  const isSubmitting = ref(false);

  const {
    data: apiResponse,
    status,
    error,
    refresh,
  } = useAsyncData<ApiResponse<Setting>>(
    "setting-all",
    () => client("/api/settings"),
  );

  const settings = computed<Setting[]>(() => apiResponse.value?.data ?? []);
  const pending = computed<boolean>(() => status.value === "pending");

  // Helper to dynamically get the value of a setting by its key
  const getSettingValue = (key: string, defaultValue = ""): string => {
    const found = settings.value.find((s) => s.key === key);
    return found ? found.value : defaultValue;
  };

  // Nilai siap-pakai untuk komponen publik (Footer, CTA, dsb).
  const siteName = computed(() =>
    getSettingValue("site_name", "Yayasan Hafizh Indonesia Emas"),
  );
  const siteDescription = computed(() => getSettingValue("site_description"));
  const contactEmail = computed(() => getSettingValue("contact_email"));
  const contactPhone = computed(() => getSettingValue("contact_phone"));
  const address = computed(() => getSettingValue("office_address"));
  const tagline = computed(() => getSettingValue("tagline"));
  const logoUrl = computed(() => getSettingValue("logo_url", "/logo.png"));
  const faviconUrl = computed(() => getSettingValue("favicon_url"));
  const metaDescription = computed(() => getSettingValue("meta_description"));
  const instagramUrl = computed(() => getSettingValue("instagram_account"));
  const facebookUrl = computed(() => getSettingValue("facebook_account"));
  const mapEmbed = computed(() => getSettingValue("gmap_embed_map"));
  const operatingHours = computed(() => getSettingValue("operating_hours"));
  const waLink = computed(() => {
    const number = getSettingValue("wa_number").replace(/[^0-9]/g, "");
    return number ? `https://wa.me/${number}` : "#";
  });

  // Update a single setting
  const updateSetting = async (key: string, value: string) => {
    return await client(`/api/settings/${key}`, {
      method: "PUT",
      body: { value },
    });
  };

  // Save all modified settings dalam satu request (endpoint settings-bulk)
  const saveAllSettings = async (formState: Record<string, string>) => {
    isSubmitting.value = true;
    try {
      await client("/api/settings-bulk", {
        method: "POST",
        body: { settings: formState },
      });
      await refresh();
      return { success: true };
    } catch (err: any) {
      return {
        success: false,
        error: err.data?.message || err.message || "Gagal menyimpan pengaturan.",
      };
    } finally {
      isSubmitting.value = false;
    }
  };

  return {
    settings,
    pending,
    error,
    refresh,
    isSubmitting,
    getSettingValue,
    updateSetting,
    saveAllSettings,
    siteName,
    siteDescription,
    contactEmail,
    contactPhone,
    address,
    tagline,
    logoUrl,
    faviconUrl,
    metaDescription,
    instagramUrl,
    facebookUrl,
    mapEmbed,
    operatingHours,
    waLink,
  };
};
