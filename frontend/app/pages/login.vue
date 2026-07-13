<template>
    <div
        class="min-h-screen flex items-center justify-center bg-gradient-to-br from-emerald-50 via-gray-50 to-teal-50 dark:from-gray-950 dark:via-gray-900 dark:to-emerald-950 px-4"
    >
        <div class="w-full max-w-md">
            <!-- Gerbang passcode - tampil dulu kalau admin mengaktifkan
                 "Passcode URL Login" di Pengaturan, sebelum form login asli
                 ditampilkan. -->
            <div
                v-if="gatePending"
                class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-md border border-gray-100 dark:border-gray-700/50 shadow-xl rounded-2xl p-6 text-center text-sm text-gray-500 dark:text-gray-400"
            >
                Memuat...
            </div>

            <div
                v-else-if="gateEnabled && !gateUnlocked"
                class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-md border border-gray-100 dark:border-gray-700/50 shadow-xl rounded-2xl p-6 space-y-4"
            >
                <div>
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white">
                        Akses Terbatas
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Masukkan passcode untuk membuka halaman login.
                    </p>
                </div>

                <UInput
                    v-model="gatePasscode"
                    type="password"
                    placeholder="Passcode"
                    icon="i-heroicons-lock-closed"
                    size="lg"
                    class="w-full"
                    :disabled="gateVerifying"
                    @keyup.enter="handleVerifyGate"
                />

                <p v-if="gateError" class="text-xs text-red-500">
                    {{ gateError }}
                </p>

                <button
                    type="button"
                    :disabled="gateVerifying || !gatePasscode"
                    @click="handleVerifyGate"
                    class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-700 dark:hover:bg-emerald-600 text-white text-sm font-semibold rounded-xl shadow-lg shadow-emerald-500/20 dark:shadow-none transition-all duration-200 flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
                >
                    <UIcon
                        v-if="gateVerifying"
                        name="i-heroicons-arrow-path"
                        class="animate-spin h-5 w-5"
                    />
                    <span>{{ gateVerifying ? "Memeriksa..." : "Buka" }}</span>
                </button>
            </div>

            <UAuthForm
                v-else
                title="Masuk Akun"
                description="Silakan masuk untuk mengakses dashboard."
                :fields="fields"
                :loading="loading"
                align="bottom"
                :ui="{
                    base: 'bg-white/80 dark:bg-gray-800/80 backdrop-blur-md border border-gray-100 dark:border-gray-700/50 shadow-xl rounded-2xl p-6',
                    title: 'text-xl font-semibold text-gray-800 dark:text-white',
                    description:
                        'text-sm text-gray-500 dark:text-gray-400 mb-4',
                }"
                @submit="onSubmit"
            >
                <template #submit>
                    <button
                        type="submit"
                        :disabled="loading"
                        class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-700 dark:hover:bg-emerald-600 text-white text-sm font-semibold rounded-xl shadow-lg shadow-emerald-500/20 dark:shadow-none transition-all duration-200 flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
                    >
                        <UIcon
                            v-if="loading"
                            name="i-heroicons-arrow-path"
                            class="animate-spin h-5 w-5"
                        />
                        <span>{{
                            loading ? "Memproses..." : "Masuk ke Portal"
                        }}</span>
                    </button>
                </template>
            </UAuthForm>
            <div class="flex items-center gap-2 py-4">
                <UIcon name="i-lucide-arrow-left" class="h-4 w-4" />
                <NuxtLink
                    to="/"
                    class="text-sm text-gray-500 dark:text-gray-400"
                >
                    Kembali ke Beranda
                </NuxtLink>
            </div>

            <p
                class="text-center text-xs text-gray-400 dark:text-gray-500 mt-6"
            >
                &copy; {{ new Date().getFullYear() }} YHIE.org
            </p>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref } from "vue";

definePageMeta({
    layout: false,
    middleware: ["sanctum:guest"],
});

const { login } = useSanctumAuth();
const toast = useToast();
const client = useSanctumClient();
const loading = ref(false);

// ── Gerbang passcode /login ──────────────────────────────────────────────
// "Terkunci" secara default sampai terbukti tidak aktif, supaya form login
// tidak sempat kelihatan sekilas (flash) sebelum status gerbang diketahui.
const GATE_SESSION_KEY = "yhie_login_gate_ok";
const gateUnlocked = ref(
    import.meta.client &&
        sessionStorage.getItem(GATE_SESSION_KEY) === "1",
);
const gatePending = ref(!gateUnlocked.value);
const gateEnabled = ref(false);
const gatePasscode = ref("");
const gateVerifying = ref(false);
const gateError = ref("");

onMounted(async () => {
    if (gateUnlocked.value) return;

    try {
        const res = await client<{ enabled: boolean }>(
            "/api/login-gate/status",
        );
        gateEnabled.value = !!res?.enabled;
    } catch {
        // Kalau status gerbang gagal diambil, jangan sampai admin terkunci
        // dari halaman login-nya sendiri - anggap gerbang tidak aktif.
        gateEnabled.value = false;
    } finally {
        gatePending.value = false;
    }
});

const handleVerifyGate = async () => {
    if (!gatePasscode.value) return;
    gateError.value = "";
    gateVerifying.value = true;

    try {
        await client("/api/login-gate/verify", {
            method: "POST",
            body: { passcode: gatePasscode.value },
        });
        gateUnlocked.value = true;
        sessionStorage.setItem(GATE_SESSION_KEY, "1");
    } catch (err: any) {
        gateError.value =
            err?.data?.message || "Passcode salah, silakan coba lagi.";
        gatePasscode.value = "";
    } finally {
        gateVerifying.value = false;
    }
};

const fields = [
    {
        name: "email",
        type: "email",
        label: "Alamat Email",
        placeholder: "nama@email.com",
        required: true,
        icon: "i-heroicons-envelope",
    },
    {
        name: "password",
        type: "password",
        label: "Kata Sandi",
        placeholder: "Masukkan kata sandi Anda",
        required: true,
        icon: "i-heroicons-lock-closed",
    },
];

async function onSubmit(payload: any) {
    loading.value = true;

    // UAuthForm mengirim SubmitEvent, data form ada di payload.data (Vue Proxy)
    const formData = payload.data;

    try {
        await login({
            email: formData.email,
            password: formData.password,
        });

        toast.add({
            title: "Berhasil Masuk",
            description:
                "Selamat datang kembali di Portal Hafiz Indonesia Emas.",
            icon: "i-heroicons-check-circle",
            color: "emerald",
            timeout: 3000,
        });
    } catch (error: any) {
        console.error("Login gagal:", error);

        const errorMessage =
            error.response?._data?.message ||
            "Email atau kata sandi yang Anda masukkan salah.";

        toast.add({
            title: "Gagal Masuk",
            description: errorMessage,
            icon: "i-heroicons-exclamation-circle",
            color: "red",
            timeout: 4000,
        });
    } finally {
        loading.value = false;
    }
}
</script>
