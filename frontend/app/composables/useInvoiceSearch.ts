// Publik (tanpa login) - cari invoice pakai kombinasi email & telepon.
export interface InvoiceSearchResult {
  reference_id: string;
  amount: number;
  payment_status: "pending" | "completed" | "failed" | "expired";
  transaction_receipt: string | null;
  program_registration?: any;
}

export const useInvoiceSearch = () => {
  const client = useSanctumClient();
  const results = ref<InvoiceSearchResult[]>([]);
  const isSearching = ref(false);
  const searchError = ref("");
  const hasSearched = ref(false);

  const search = async (email: string, phone: string) => {
    searchError.value = "";
    isSearching.value = true;

    try {
      const response = await client<{ data: InvoiceSearchResult[] }>(
        "/api/transactions/search",
        { method: "POST", body: { email, phone } },
      );
      results.value = response?.data ?? [];
    } catch (err: any) {
      searchError.value =
        err?.data?.message || "Gagal mencari invoice. Silakan coba lagi.";
      results.value = [];
    } finally {
      isSearching.value = false;
      hasSearched.value = true;
    }
  };

  return { results, isSearching, searchError, hasSearched, search };
};
