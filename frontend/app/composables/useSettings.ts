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
  // logo_url/favicon_url bisa berupa URL eksternal lama (dipakai apa adanya)
  // atau path hasil upload sendiri ("/storage/settings/..") yang perlu
  // di-prefix domain backend supaya bisa dipakai sebagai <img src>.
  const fileUrl = useFileUrl();
  const logoUrl = computed(() => {
    const val = getSettingValue("logo_url", "/logo.png");
    return val.startsWith("/storage/") ? fileUrl(val) : val;
  });
  const faviconUrl = computed(() => {
    const val = getSettingValue("favicon_url");
    return val.startsWith("/storage/") ? fileUrl(val) : val;
  });
  const metaDescription = computed(() => getSettingValue("meta_description"));
  const instagramUrl = computed(() => getSettingValue("instagram_account"));
  const facebookUrl = computed(() => getSettingValue("facebook_account"));
  const mapEmbed = computed(() => getSettingValue("gmap_embed_map"));
  const operatingHours = computed(() => getSettingValue("operating_hours"));
  const waLink = computed(() => {
    const number = getSettingValue("wa_number").replace(/[^0-9]/g, "");
    return number ? `https://wa.me/${number}` : "#";
  });

  // Gambar slider hero section - disimpan sbg JSON array di value setting
  // "hero_images". Path mentah (buat aksi hapus) dan versi siap-tampil
  // (di-prefix domain backend) dipisah supaya keduanya gampang dipakai.
  const heroImagePaths = computed<string[]>(() => {
    try {
      const parsed = JSON.parse(getSettingValue("hero_images", "[]"));
      return Array.isArray(parsed) ? parsed : [];
    } catch {
      return [];
    }
  });
  const heroImages = computed<string[]>(() => {
    if (!heroImagePaths.value.length) return ["/quran.webp", "/tejasari.webp"];
    return heroImagePaths.value.map((p) =>
      p.startsWith("/storage/") ? fileUrl(p) : p,
    );
  });

  // Update a single setting
  const updateSetting = async (key: string, value: string) => {
    return await client(`/api/settings/${key}`, {
      method: "PUT",
      body: { value },
    });
  };

  // Upload gambar (logo/favicon) - langsung tersimpan begitu dipilih, TIDAK
  // lewat form bulk-save (nggak bisa kirim File lewat JSON, dan biar nggak
  // ketiban kalau admin nyimpen tab lain sebelum gambar sempat ke-set balik
  // ke form).
  const uploadSettingImage = async (key: string, file: File) => {
    const formData = new FormData();
    formData.append("image", file);
    const res = await client<{ data: Setting }>(`/api/settings/${key}/upload`, {
      method: "POST",
      body: formData,
    });
    await refresh();
    return res.data.value;
  };

  // Tambah/hapus satu gambar hero section - langsung tersimpan di server,
  // bukan bagian dari form bulk-save (sama alasannya kayak upload logo/favicon).
  const addHeroImage = async (file: File) => {
    const formData = new FormData();
    formData.append("image", file);
    await client("/api/settings/hero-images", {
      method: "POST",
      body: formData,
    });
    await refresh();
  };

  const removeHeroImage = async (path: string) => {
    await client("/api/settings/hero-images", {
      method: "DELETE",
      body: { path },
    });
    await refresh();
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
    uploadSettingImage,
    addHeroImage,
    removeHeroImage,
    saveAllSettings,
    siteName,
    siteDescription,
    contactEmail,
    contactPhone,
    address,
    tagline,
    logoUrl,
    faviconUrl,
    heroImages,
    heroImagePaths,
    metaDescription,
    instagramUrl,
    facebookUrl,
    mapEmbed,
    operatingHours,
    waLink,
  };
};
