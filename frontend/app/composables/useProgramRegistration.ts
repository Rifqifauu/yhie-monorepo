// composables/useProgramRegistration.ts
export const useProgramRegistration = () => {
  const { t } = useI18n();
  const client = useSanctumClient();

  // Form state
  const form = reactive({
    full_name: "",
    email: "",
    phone: "",
    gender: "" as "male" | "female" | "",
    age: null as number | null,
    address: "",
    notes: "",
  });

  const errors = ref<Record<string, string[]>>({});
  const generalError = ref("");
  const isSubmitting = ref(false);
  const submitSuccess = ref(false);

  const resetForm = () => {
    form.full_name = "";
    form.email = "";
    form.phone = "";
    form.gender = "";
    form.age = null;
    form.address = "";
    form.notes = "";
    errors.value = {};
    generalError.value = "";
    isSubmitting.value = false;
    submitSuccess.value = false;
  };

  const submitForm = async (programId: number | string) => {
    errors.value = {};
    generalError.value = "";

    // Client-side validation
    if (!form.full_name.trim()) {
      errors.value.full_name = [t("registration.required")];
      return;
    }
    if (!form.email.trim()) {
      errors.value.email = [t("registration.required")];
      return;
    }
    if (!form.phone.trim()) {
      errors.value.phone = [t("registration.required")];
      return;
    }
    if (!form.gender) {
      errors.value.gender = [t("registration.required")];
      return;
    }

    isSubmitting.value = true;

    try {
      await client("/api/program-registrations", {
        method: "POST",
        body: {
          program_id: programId,
          full_name: form.full_name.trim(),
          email: form.email.trim(),
          phone: form.phone.trim(),
          gender: form.gender,
          age: form.age || undefined,
          address: form.address.trim() || undefined,
          notes: form.notes.trim() || undefined,
        },
      });

      submitSuccess.value = true;
    } catch (err: any) {
      if (err?.response?.status === 422 || err?.status === 422) {
        const data = err?.response?._data || err?.data;
        if (data?.errors) {
          errors.value = data.errors;
        } else {
          generalError.value = data?.message || t("registration.error");
        }
      } else {
        generalError.value = t("registration.error");
      }
    } finally {
      isSubmitting.value = false;
    }
  };

  return {
    form,
    errors,
    generalError,
    isSubmitting,
    submitSuccess,
    resetForm,
    submitForm,
  };
};
