// Muat script resmi DOKU Checkout (jokul-checkout-1.0.0.js) sekali saja lalu
// panggil loadJokulCheckout(payment_url) bawaannya - ini menampilkan halaman
// pembayaran DOKU sebagai popup modal di atas halaman kita (metode embed
// resmi yang didukung DOKU, bukan iframe manual).
// Dok: https://developers.doku.com/accept-payments/doku-checkout/integration-guide/frontend-integration

declare global {
  interface Window {
    loadJokulCheckout?: (paymentUrl: string) => void;
  }
}

let scriptPromise: Promise<void> | null = null;

const loadDokuScript = (scriptUrl: string): Promise<void> => {
  if (window.loadJokulCheckout) return Promise.resolve();
  if (scriptPromise) return scriptPromise;

  scriptPromise = new Promise((resolve, reject) => {
    const script = document.createElement("script");
    script.src = scriptUrl;
    script.async = true;
    script.onload = () => resolve();
    script.onerror = () => {
      scriptPromise = null;
      reject(new Error("Failed to load DOKU Checkout script"));
    };
    document.head.appendChild(script);
  });

  return scriptPromise;
};

export const useDokuCheckout = () => {
  const config = useRuntimeConfig();
  const isLoading = ref(false);
  const loadError = ref(false);

  const openCheckout = async (paymentUrl: string) => {
    loadError.value = false;
    isLoading.value = true;
    try {
      await loadDokuScript(config.public.dokuCheckoutScriptUrl as string);
      window.loadJokulCheckout?.(paymentUrl);
    } catch {
      loadError.value = true;
    } finally {
      isLoading.value = false;
    }
  };

  return { isLoading, loadError, openCheckout };
};
