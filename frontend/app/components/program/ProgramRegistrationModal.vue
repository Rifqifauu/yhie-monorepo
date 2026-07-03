<template>
    <UModal v-model:open="isOpen" :close="false" :dismissible="true">
        <template #content>
            <!-- Success state -->
            <div v-if="submitSuccess" class="p-8 md:p-12 text-center">
                <div
                    class="mx-auto w-20 h-20 rounded-full bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center shadow-xl shadow-emerald-500/30 mb-6 animate-bounce-once"
                >
                    <UIcon name="i-lucide-check" class="w-10 h-10 text-white" />
                </div>
                <h3
                    class="text-2xl font-serif font-extrabold text-emerald-900 dark:text-emerald-50 mb-2"
                >
                    {{ t("registration.success") }}
                </h3>
                <p class="text-slate-600 dark:text-emerald-100/70 mb-8">
                    {{ t("registration.successDesc") }}
                </p>
                <button
                    @click="closeAndReset"
                    class="px-8 py-3 rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-400 hover:to-emerald-500 text-white font-bold shadow-lg hover:shadow-emerald-500/30 transition-all duration-300 transform hover:-translate-y-0.5"
                >
                    {{ t("registration.close") }}
                </button>
            </div>

            <!-- Form state -->
            <div v-else class="flex flex-col max-h-[85vh]">
                <!-- Modal Header -->
                <div
                    class="relative overflow-hidden px-6 pt-6 pb-5 border-b border-emerald-200/40 dark:border-emerald-800/40 shrink-0"
                >
                    <!-- Decorative blurs -->
                    <div
                        class="absolute -top-10 -right-10 w-32 h-32 bg-amber-400/15 rounded-full blur-2xl"
                    ></div>
                    <div
                        class="absolute -bottom-8 -left-8 w-28 h-28 bg-emerald-400/15 rounded-full blur-2xl"
                    ></div>

                    <div class="relative flex items-start justify-between">
                        <div>
                            <span
                                class="inline-flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-[0.25em] text-amber-600 dark:text-amber-400 mb-2"
                            >
                                <UIcon
                                    name="i-lucide-pen-line"
                                    class="w-3.5 h-3.5"
                                />
                                {{ t("registration.selectedProgram") }}
                            </span>
                            <h2
                                class="text-xl md:text-2xl font-serif font-extrabold text-emerald-950 dark:text-emerald-50 leading-tight"
                            >
                                {{ t("registration.modalTitle") }}
                            </h2>
                        </div>
                        <button
                            @click="closeAndReset"
                            class="w-8 h-8 flex items-center justify-center rounded-full bg-emerald-100/60 dark:bg-emerald-900/40 text-slate-500 dark:text-emerald-300 hover:bg-red-100 dark:hover:bg-red-900/30 hover:text-red-600 dark:hover:text-red-400 transition-all duration-300"
                        >
                            <UIcon name="i-lucide-x" class="w-4 h-4" />
                        </button>
                    </div>

                    <!-- Program Info Badge -->
                    <div
                        class="mt-4 flex items-center gap-3 p-3 rounded-2xl bg-emerald-50/80 dark:bg-emerald-900/30 border border-emerald-200/50 dark:border-emerald-800/50"
                    >
                        <div
                            class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center shadow-md shrink-0"
                        >
                            <UIcon
                                name="i-lucide-graduation-cap"
                                class="w-5 h-5 text-white"
                            />
                        </div>
                        <div class="min-w-0 flex-1">
                            <p
                                class="text-sm font-bold text-emerald-900 dark:text-emerald-50 truncate"
                            >
                                {{ programTitle }}
                            </p>
                            <p
                                class="text-xs font-semibold text-amber-600 dark:text-amber-400"
                            >
                                {{ programPrice }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Form Body -->
                <form
                    @submit.prevent="handleSubmit"
                    class="px-6 py-5 space-y-6 flex-1 min-h-0 overflow-y-auto custom-scrollbar"
                >
                    <!-- Personal Info Section -->
                    <div>
                        <div class="flex items-center gap-2 mb-4">
                            <div
                                class="w-7 h-7 rounded-lg bg-emerald-100 dark:bg-emerald-900/50 flex items-center justify-center"
                            >
                                <UIcon
                                    name="i-lucide-user"
                                    class="w-4 h-4 text-emerald-600 dark:text-emerald-400"
                                />
                            </div>
                            <h3
                                class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider"
                            >
                                {{ t("registration.personalInfo") }}
                            </h3>
                        </div>

                        <div class="space-y-4">
                            <!-- Full Name -->
                            <div>
                                <label
                                    class="block text-xs font-semibold text-slate-600 dark:text-emerald-200/70 mb-1.5"
                                >
                                    {{ t("registration.fullName") }}
                                    <span class="text-red-500">*</span>
                                </label>
                                <input
                                    v-model="form.full_name"
                                    type="text"
                                    required
                                    :placeholder="
                                        t('registration.fullNamePlaceholder')
                                    "
                                    class="w-full px-4 py-3 rounded-xl border border-emerald-200/70 dark:border-emerald-800/60 bg-white/60 dark:bg-emerald-900/20 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-emerald-300/40 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-400 dark:focus:border-emerald-500 transition-all duration-300 text-sm"
                                />
                                <p
                                    v-if="errors.full_name"
                                    class="mt-1 text-xs text-red-500"
                                >
                                    {{ errors.full_name[0] }}
                                </p>
                            </div>

                            <!-- Email & Phone (2-col) -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label
                                        class="block text-xs font-semibold text-slate-600 dark:text-emerald-200/70 mb-1.5"
                                    >
                                        {{ t("registration.email") }}
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        v-model="form.email"
                                        type="email"
                                        required
                                        placeholder="email@contoh.com"
                                        class="w-full px-4 py-3 rounded-xl border border-emerald-200/70 dark:border-emerald-800/60 bg-white/60 dark:bg-emerald-900/20 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-emerald-300/40 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-400 dark:focus:border-emerald-500 transition-all duration-300 text-sm"
                                    />
                                    <p
                                        v-if="errors.email"
                                        class="mt-1 text-xs text-red-500"
                                    >
                                        {{ errors.email[0] }}
                                    </p>
                                </div>
                                <div>
                                    <label
                                        class="block text-xs font-semibold text-slate-600 dark:text-emerald-200/70 mb-1.5"
                                    >
                                        {{ t("registration.phone") }}
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        v-model="form.phone"
                                        type="tel"
                                        required
                                        placeholder="08xxxxxxxxxx"
                                        class="w-full px-4 py-3 rounded-xl border border-emerald-200/70 dark:border-emerald-800/60 bg-white/60 dark:bg-emerald-900/20 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-emerald-300/40 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-400 dark:focus:border-emerald-500 transition-all duration-300 text-sm"
                                    />
                                    <p
                                        v-if="errors.phone"
                                        class="mt-1 text-xs text-red-500"
                                    >
                                        {{ errors.phone[0] }}
                                    </p>
                                </div>
                            </div>

                            <!-- Gender & Age (2-col) -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label
                                        class="block text-xs font-semibold text-slate-600 dark:text-emerald-200/70 mb-1.5"
                                    >
                                        {{ t("registration.gender") }}
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <div class="flex gap-3">
                                        <label
                                            class="flex-1 flex items-center gap-2.5 px-4 py-3 rounded-xl border cursor-pointer transition-all duration-300 text-sm"
                                            :class="
                                                form.gender === 'male'
                                                    ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-900/40 text-emerald-800 dark:text-emerald-200 ring-2 ring-emerald-500/30'
                                                    : 'border-emerald-200/70 dark:border-emerald-800/60 bg-white/60 dark:bg-emerald-900/20 text-slate-600 dark:text-emerald-100/60 hover:border-emerald-300 dark:hover:border-emerald-700'
                                            "
                                        >
                                            <input
                                                v-model="form.gender"
                                                type="radio"
                                                value="male"
                                                class="sr-only"
                                            />
                                            <UIcon
                                                name="i-lucide-user"
                                                class="w-4 h-4"
                                            />
                                            <span class="font-medium">{{
                                                t("registration.male")
                                            }}</span>
                                        </label>
                                        <label
                                            class="flex-1 flex items-center gap-2.5 px-4 py-3 rounded-xl border cursor-pointer transition-all duration-300 text-sm"
                                            :class="
                                                form.gender === 'female'
                                                    ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-900/40 text-emerald-800 dark:text-emerald-200 ring-2 ring-emerald-500/30'
                                                    : 'border-emerald-200/70 dark:border-emerald-800/60 bg-white/60 dark:bg-emerald-900/20 text-slate-600 dark:text-emerald-100/60 hover:border-emerald-300 dark:hover:border-emerald-700'
                                            "
                                        >
                                            <input
                                                v-model="form.gender"
                                                type="radio"
                                                value="female"
                                                class="sr-only"
                                            />
                                            <UIcon
                                                name="i-lucide-user"
                                                class="w-4 h-4"
                                            />
                                            <span class="font-medium">{{
                                                t("registration.female")
                                            }}</span>
                                        </label>
                                    </div>
                                    <p
                                        v-if="errors.gender"
                                        class="mt-1 text-xs text-red-500"
                                    >
                                        {{ errors.gender[0] }}
                                    </p>
                                </div>
                                <div>
                                    <label
                                        class="block text-xs font-semibold text-slate-600 dark:text-emerald-200/70 mb-1.5"
                                    >
                                        {{ t("registration.age") }}
                                    </label>
                                    <input
                                        v-model.number="form.age"
                                        type="number"
                                        min="1"
                                        max="150"
                                        :placeholder="
                                            t('registration.agePlaceholder')
                                        "
                                        class="w-full px-4 py-3 rounded-xl border border-emerald-200/70 dark:border-emerald-800/60 bg-white/60 dark:bg-emerald-900/20 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-emerald-300/40 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-400 dark:focus:border-emerald-500 transition-all duration-300 text-sm"
                                    />
                                    <p
                                        v-if="errors.age"
                                        class="mt-1 text-xs text-red-500"
                                    >
                                        {{ errors.age[0] }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Divider -->
                    <hr
                        class="border-emerald-200/40 dark:border-emerald-800/40"
                    />

                    <!-- Additional Info Section -->
                    <div>
                        <div class="flex items-center gap-2 mb-4">
                            <div
                                class="w-7 h-7 rounded-lg bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center"
                            >
                                <UIcon
                                    name="i-lucide-map-pin"
                                    class="w-4 h-4 text-amber-600 dark:text-amber-400"
                                />
                            </div>
                            <h3
                                class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider"
                            >
                                {{ t("registration.additionalInfo") }}
                            </h3>
                        </div>

                        <div class="space-y-4">
                            <!-- Address -->
                            <div>
                                <label
                                    class="block text-xs font-semibold text-slate-600 dark:text-emerald-200/70 mb-1.5"
                                >
                                    {{ t("registration.address") }}
                                </label>
                                <textarea
                                    v-model="form.address"
                                    rows="2"
                                    :placeholder="
                                        t('registration.addressPlaceholder')
                                    "
                                    class="w-full px-4 py-3 rounded-xl border border-emerald-200/70 dark:border-emerald-800/60 bg-white/60 dark:bg-emerald-900/20 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-emerald-300/40 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-400 dark:focus:border-emerald-500 transition-all duration-300 text-sm resize-none"
                                ></textarea>
                                <p
                                    v-if="errors.address"
                                    class="mt-1 text-xs text-red-500"
                                >
                                    {{ errors.address[0] }}
                                </p>
                            </div>

                            <!-- Notes -->
                            <div>
                                <label
                                    class="block text-xs font-semibold text-slate-600 dark:text-emerald-200/70 mb-1.5"
                                >
                                    {{ t("registration.notes") }}
                                </label>
                                <textarea
                                    v-model="form.notes"
                                    rows="2"
                                    :placeholder="
                                        t('registration.notesPlaceholder')
                                    "
                                    class="w-full px-4 py-3 rounded-xl border border-emerald-200/70 dark:border-emerald-800/60 bg-white/60 dark:bg-emerald-900/20 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-emerald-300/40 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-400 dark:focus:border-emerald-500 transition-all duration-300 text-sm resize-none"
                                ></textarea>
                                <p
                                    v-if="errors.notes"
                                    class="mt-1 text-xs text-red-500"
                                >
                                    {{ errors.notes[0] }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- General Error -->
                    <div
                        v-if="generalError"
                        class="p-3 rounded-xl bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800/50 text-sm text-red-700 dark:text-red-300 flex items-start gap-2"
                    >
                        <UIcon
                            name="i-lucide-alert-circle"
                            class="w-4 h-4 mt-0.5 shrink-0"
                        />
                        <span>{{ generalError }}</span>
                    </div>
                </form>

                <!-- Modal Footer -->
                <div
                    class="px-6 py-4 border-t border-emerald-200/40 dark:border-emerald-800/40 flex flex-col sm:flex-row gap-3 sm:justify-end shrink-0"
                >
                    <button
                        @click="closeAndReset"
                        type="button"
                        class="w-full sm:w-auto px-6 py-3 rounded-xl border border-emerald-300 dark:border-emerald-700 bg-white/40 dark:bg-emerald-950/30 text-slate-700 dark:text-emerald-200 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 font-semibold transition-all duration-300 text-sm"
                    >
                        {{ t("registration.cancel") }}
                    </button>
                    <button
                        @click="handleSubmit"
                        :disabled="isSubmitting"
                        class="w-full sm:w-auto px-8 py-3 rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-400 hover:to-emerald-500 disabled:from-emerald-300 disabled:to-emerald-400 disabled:cursor-not-allowed text-white font-bold shadow-lg hover:shadow-emerald-500/30 transition-all duration-300 transform hover:-translate-y-0.5 disabled:transform-none flex items-center justify-center gap-2 text-sm"
                    >
                        <UIcon
                            v-if="isSubmitting"
                            name="i-lucide-loader-2"
                            class="w-4 h-4 animate-spin"
                        />
                        <UIcon v-else name="i-lucide-send" class="w-4 h-4" />
                        <span>{{
                            isSubmitting
                                ? t("registration.submitting")
                                : t("registration.submit")
                        }}</span>
                    </button>
                </div>
            </div>
        </template>
    </UModal>
</template>
<script setup lang="ts">
const props = defineProps<{
    programId: number | string;
    programTitle: string;
    programPrice: string;
}>();

const isOpen = defineModel<boolean>({ default: false });
const { t } = useI18n(); // Tetap diperlukan di komponen jika <template> memakai `t()`

// Gunakan composable
const {
    form,
    errors,
    generalError,
    isSubmitting,
    submitSuccess,
    resetForm,
    submitForm,
} = useProgramRegistration();

const closeAndReset = () => {
    isOpen.value = false;
    // Delay reset sehingga animasi closing modal selesai dulu
    setTimeout(() => {
        resetForm();
    }, 300);
};

// Wrapper function untuk submit agar otomatis mengirim programId dari props
const handleSubmit = async () => {
    await submitForm(props.programId);
};
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(16, 185, 129, 0.2);
    border-radius: 999px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(16, 185, 129, 0.4);
}

@keyframes bounce-once {
    0% {
        transform: scale(0);
        opacity: 0;
    }
    50% {
        transform: scale(1.15);
    }
    70% {
        transform: scale(0.95);
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}
.animate-bounce-once {
    animation: bounce-once 0.6s ease-out forwards;
}
</style>
