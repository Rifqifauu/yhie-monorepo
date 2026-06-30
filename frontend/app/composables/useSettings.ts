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

  // Update a single setting
  const updateSetting = async (key: string, value: string) => {
    return await client(`/api/settings/${key}`, {
      method: "PUT",
      body: { value },
    });
  };

  // Save all modified settings concurrently
  const saveAllSettings = async (formState: Record<string, string>) => {
    isSubmitting.value = true;
    try {
      // Mengirim request satu per satu (berurutan) agar tidak membebani server / memicu rate-limit
      for (const [key, value] of Object.entries(formState)) {
        await updateSetting(key, value);
      }
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
  };
};
