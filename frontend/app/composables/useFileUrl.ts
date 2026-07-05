// Bangun URL lengkap ke file/gambar di backend dari path relatif (mis. "/storage/..").
// Menggantikan pola `backendUrl + path` yang sebelumnya disalin di banyak halaman.
export const useFileUrl = () => {
  const config = useRuntimeConfig();
  const backendUrl =
    (config.public.sanctum?.baseUrl as string) || "http://127.0.0.1:8000";

  return (path?: string | null): string => {
    if (!path) return "";
    if (path.startsWith("http")) return path;
    return `${backendUrl}${path.startsWith("/") ? path : `/${path}`}`;
  };
};
