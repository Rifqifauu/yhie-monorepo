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

  const idCard = ref<File | null>(null);
  const photo = ref<File | null>(null);

  const errors = ref<Record<string, string[]>>({});
  const generalError = ref("");
  const isSubmitting = ref(false);
  const submitSuccess = ref(false);
  const referenceId = ref("");

  const resetForm = () => {
    form.full_name = "";
    form.email = "";
    form.phone = "";
    form.gender = "";
    form.age = null;
    form.address = "";
    form.notes = "";
    idCard.value = null;
    photo.value = null;
    errors.value = {};
    generalError.value = "";
    isSubmitting.value = false;
    submitSuccess.value = false;
    referenceId.value = "";
  };

  const submitForm = async (
    programId: number | string,
    amount: number | string,
  ) => {
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
    if (!idCard.value) {
      errors.value.id_card = [t("registration.required")];
      return;
    }
    if (!photo.value) {
      errors.value.photo = [t("registration.required")];
      return;
    }

    isSubmitting.value = true;

    try {
      const formData = new FormData();
      formData.append("program_id", String(programId));
      formData.append("amount", String(amount || 0));
      formData.append("full_name", form.full_name.trim());
      formData.append("email", form.email.trim());
      formData.append("phone", form.phone.trim());
      formData.append("gender", form.gender);
      if (form.age) formData.append("age", String(form.age));
      if (form.address.trim()) formData.append("address", form.address.trim());
      if (form.notes.trim()) formData.append("notes", form.notes.trim());
      formData.append("id_card", idCard.value);
      formData.append("photo", photo.value);

      const response: any = await client("/api/program-registrations", {
        method: "POST",
        body: formData,
      });

      referenceId.value = response?.data?.transactions?.[0]?.reference_id || "";
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
    idCard,
    photo,
    errors,
    generalError,
    isSubmitting,
    submitSuccess,
    referenceId,
    resetForm,
    submitForm,
  };
};
