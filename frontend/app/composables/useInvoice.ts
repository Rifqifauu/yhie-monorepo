// composables/useInvoice.ts
// Publik (tanpa login) - cek status invoice & upload bukti transfer manual.
export interface InvoiceTransaction {
  id: number | string;
  reference_id: string;
  amount: number;
  payment_status: "pending" | "completed" | "failed" | "expired";
  payment_method: string;
  payment_url: string | null;
  transaction_receipt: string | null;
  program_registration?: any;
}

export const useInvoice = (referenceId: string) => {
  const client = useSanctumClient();
  const isUploading = ref(false);
  const uploadError = ref("");
  const isGeneratingPayment = ref(false);
  const generatePaymentError = ref("");

  const {
    data: apiResponse,
    status,
    error,
    refresh,
  } = useAsyncData<{ data: InvoiceTransaction }>(`invoice-${referenceId}`, () =>
    client(`/api/transactions/track/${referenceId}`),
  );

  const transaction = computed<InvoiceTransaction | null>(
    () => apiResponse.value?.data ?? null,
  );
  const pending = computed(() => status.value === "pending");

  const uploadReceipt = async (file: File) => {
    uploadError.value = "";
    isUploading.value = true;

    try {
      const formData = new FormData();
      formData.append("receipt", file);

      await client(`/api/transactions/track/${referenceId}/receipt`, {
        method: "POST",
        body: formData,
      });

      await refresh();
      return true;
    } catch (err: any) {
      uploadError.value =
        err?.data?.message || "Gagal mengunggah bukti transfer.";
      return false;
    } finally {
      isUploading.value = false;
    }
  };

  const generatePayment = async () => {
    generatePaymentError.value = "";
    isGeneratingPayment.value = true;

    try {
      const res = await client<{ data: InvoiceTransaction }>(
        `/api/transactions/track/${referenceId}/generate-payment`,
        { method: "POST" },
      );
      apiResponse.value = res;
      return res.data.payment_url;
    } catch (err: any) {
      generatePaymentError.value =
        err?.data?.message || "Gagal membuat link pembayaran.";
      return null;
    } finally {
      isGeneratingPayment.value = false;
    }
  };

  return {
    transaction,
    pending,
    error,
    isUploading,
    uploadError,
    uploadReceipt,
    isGeneratingPayment,
    generatePaymentError,
    generatePayment,
    refresh,
  };
};
