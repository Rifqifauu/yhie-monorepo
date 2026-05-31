export const useLanguage = () => {
  const currentLang = useState<"id" | "en">("lang", () => "id");

  const toggleLanguage = () => {
    currentLang.value = currentLang.value === "id" ? "en" : "id";
  };

  return {
    currentLang,
    toggleLanguage,
  };
};
