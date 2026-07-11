import { ref } from "vue";

export interface AdminAccount {
  id: number;
  name: string;
  email: string;
  role: string;
}

export interface UpdateAccountPayload {
  name?: string;
  email?: string;
  current_password?: string;
  password?: string;
  password_confirmation?: string;
}

export const useAccount = () => {
  const client = useSanctumClient();
  const { user, refreshIdentity } = useSanctumAuth<AdminAccount>();
  const isSubmitting = ref(false);

  const updateAccount = async (payload: UpdateAccountPayload) => {
    isSubmitting.value = true;
    try {
      await client("/api/account", { method: "PUT", body: payload });
      await refreshIdentity();
      return { success: true };
    } catch (err: any) {
      return {
        success: false,
        error: err.data?.message || err.message || "Gagal memperbarui akun.",
      };
    } finally {
      isSubmitting.value = false;
    }
  };

  return { user, isSubmitting, updateAccount };
};
