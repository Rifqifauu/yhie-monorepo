<template>
    <div class="min-h-screen bg-[radial-gradient(circle_at_top,rgba(16,185,129,0.12),transparent_32%),linear-gradient(180deg,rgba(248,250,252,0.9),rgba(236,253,245,0.6))] dark:bg-[radial-gradient(circle_at_top,rgba(16,185,129,0.12),transparent_32%),linear-gradient(180deg,rgba(2,6,23,0.95),rgba(4,12,28,0.92))]">
        <AdminTopbar
            title="Artikel"
            description="Kelola berita, pengumuman, dan artikel tanpa perlu memahami detail teknis."
        />

        <div class="space-y-6 px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <UCard
                    v-for="card in summaryCards"
                    :key="card.label"
                    class="overflow-hidden border-0 shadow-lg shadow-slate-900/5 dark:shadow-black/20"
                >
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">
                                {{ card.label }}
                            </p>
                            <p class="mt-2 text-2xl font-semibold tracking-tight text-slate-900 dark:text-white">
                                {{ card.value }}
                            </p>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                {{ card.helper }}
                            </p>
                        </div>
                        <div class="flex h-11 w-11 items-center justify-center rounded-2xl" :class="card.iconBg">
                            <UIcon :name="card.icon" class="h-5 w-5" />
                        </div>
                    </div>
                </UCard>
            </div>

            <UCard class="overflow-hidden border-0 shadow-lg shadow-slate-900/5 dark:shadow-black/20">
                <template #header>
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-emerald-700 dark:text-emerald-300">
                                Manajemen konten
                            </p>
                            <h2 class="mt-2 text-xl font-semibold text-slate-900 dark:text-white">
                                Daftar artikel
                            </h2>
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                                Cari, edit, terbitkan, atau hapus artikel dalam satu layar.
                            </p>
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <UButton color="neutral" variant="soft" icon="i-lucide-rotate-cw" @click="refresh">
                                Muat ulang
                            </UButton>
                            <UButton color="primary" icon="i-lucide-plus" @click="openCreate">
                                Tambah artikel
                            </UButton>
                        </div>
                    </div>
                </template>

                <div class="grid gap-3 lg:grid-cols-[1fr_220px_auto]">
                    <UInput
                        v-model="searchInput"
                        icon="i-lucide-search"
                        placeholder="Cari judul artikel..."
                        size="lg"
                        @keyup.enter="applySearch"
                    />

                    <select
                        v-model="category"
                        class="h-12 rounded-xl border border-slate-200 bg-white px-4 text-sm text-slate-700 shadow-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:border-white/10 dark:bg-slate-950/70 dark:text-slate-200"
                    >
                        <option value="">Semua kategori</option>
                        <option v-for="option in categories" :key="option.value" :value="option.value">
                            {{ option.label }}
                        </option>
                    </select>

                    <div class="flex gap-2">
                        <UButton color="primary" variant="soft" icon="i-lucide-search" @click="applySearch">
                            Cari
                        </UButton>
                        <UButton color="neutral" variant="ghost" icon="i-lucide-eraser" @click="clearFilters">
                            Reset
                        </UButton>
                    </div>
                </div>
            </UCard>

            <UAlert
                v-if="flashMessage"
                :title="flashTitle"
                :description="flashMessage"
                :color="flashType === 'error' ? 'red' : 'green'"
                variant="soft"
                :icon="flashType === 'error' ? 'i-lucide-triangle-alert' : 'i-lucide-circle-check'"
            />

            <UAlert
                v-else-if="error"
                title="Gagal memuat artikel"
                description="Coba muat ulang halaman atau periksa koneksi ke backend."
                color="red"
                variant="soft"
                icon="i-lucide-triangle-alert"
            />

            <div v-if="pending && !articles.length" class="space-y-4">
                <UCard v-for="n in 3" :key="n" class="animate-pulse border-0 shadow-lg shadow-slate-900/5 dark:shadow-black/20">
                    <div class="grid gap-4 lg:grid-cols-[140px_minmax(0,1fr)_110px]">
                        <USkeleton class="aspect-4/3 rounded-2xl" />
                        <div class="space-y-3">
                            <USkeleton class="h-5 w-40" />
                            <USkeleton class="h-4 w-3/4" />
                            <USkeleton class="h-4 w-2/3" />
                            <USkeleton class="h-4 w-1/2" />
                        </div>
                        <div class="flex gap-2 lg:flex-col">
                            <USkeleton class="h-10 w-full rounded-xl" />
                            <USkeleton class="h-10 w-full rounded-xl" />
                        </div>
                    </div>
                </UCard>
            </div>

            <div v-else-if="!filteredArticles.length" class="rounded-3xl border border-dashed border-emerald-200 bg-white/80 px-6 py-16 text-center shadow-sm dark:border-emerald-900/40 dark:bg-slate-950/50">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300">
                    <UIcon name="i-lucide-file-text" class="h-7 w-7" />
                </div>
                <h3 class="mt-5 text-lg font-semibold text-slate-900 dark:text-white">
                    Belum ada artikel yang cocok
                </h3>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                    Coba ubah kata kunci pencarian, filter kategori, atau tambahkan artikel baru.
                </p>
                <div class="mt-6 flex justify-center gap-3">
                    <UButton color="neutral" variant="soft" @click="clearFilters">
                        Bersihkan filter
                    </UButton>
                    <UButton color="primary" icon="i-lucide-plus" @click="openCreate">
                        Tambah artikel
                    </UButton>
                </div>
            </div>

            <div v-else class="space-y-4">
                <UCard
                    v-for="article in filteredArticles"
                    :key="article.id"
                    class="overflow-hidden border-0 shadow-lg shadow-slate-900/5 transition hover:-translate-y-0.5 hover:shadow-xl hover:shadow-slate-900/10 dark:shadow-black/20"
                >
                    <div class="grid gap-4 lg:grid-cols-[140px_minmax(0,1fr)_auto]">
                        <div class="relative overflow-hidden rounded-2xl bg-slate-100 dark:bg-slate-900">
                            <img
                                v-if="coverOf(article)"
                                :src="coverOf(article)"
                                :alt="titleOf(article)"
                                class="h-full w-full object-cover"
                            >
                            <div v-else class="flex aspect-4/3 items-center justify-center bg-linear-to-br from-emerald-100 to-amber-50 text-emerald-500 dark:from-emerald-950 dark:to-slate-900 dark:text-emerald-400">
                                <UIcon name="i-lucide-image-off" class="h-10 w-10" />
                            </div>
                        </div>

                        <div class="min-w-0 space-y-3">
                            <div class="flex flex-wrap items-center gap-2">
                                <UBadge color="primary" variant="soft">
                                    {{ categoryLabel(article.category) }}
                                </UBadge>
                                <UBadge :color="statusColor(article.is_published)" variant="soft">
                                    {{ statusText(article.is_published) }}
                                </UBadge>
                                <UBadge color="neutral" variant="subtle">
                                    {{ imagesOf(article).length }} gambar
                                </UBadge>
                            </div>

                            <div class="space-y-1">
                                <h3 class="truncate text-lg font-semibold text-slate-900 dark:text-white">
                                    {{ titleOf(article) }}
                                </h3>
                                <p class="text-sm text-slate-500 dark:text-slate-400">
                                    EN: {{ article.title_en }}
                                </p>
                            </div>

                            <p class="line-clamp-3 text-sm leading-6 text-slate-600 dark:text-slate-300">
                                {{ excerptOf(article) }}
                            </p>

                            <div class="flex flex-wrap gap-x-5 gap-y-2 text-xs text-slate-500 dark:text-slate-400">
                                <span class="inline-flex items-center gap-1.5">
                                    <UIcon name="i-lucide-user" class="h-3.5 w-3.5" />
                                    {{ article.author?.name || 'Admin YHIE' }}
                                </span>
                                <span class="inline-flex items-center gap-1.5">
                                    <UIcon name="i-lucide-calendar-days" class="h-3.5 w-3.5" />
                                    {{ formatDate(article.created_at) }}
                                </span>
                                <span class="inline-flex items-center gap-1.5">
                                    <UIcon name="i-lucide-link" class="h-3.5 w-3.5" />
                                    {{ article.slug_id }}
                                </span>
                            </div>
                        </div>

                        <div class="flex gap-2 lg:flex-col lg:justify-start">
                            <UButton color="primary" variant="soft" icon="i-lucide-pen-line" @click="openEdit(article)">
                                Edit
                            </UButton>
                            <UButton color="red" variant="soft" icon="i-lucide-trash-2" @click="askDelete(article)">
                                Hapus
                            </UButton>
                        </div>
                    </div>
                </UCard>
            </div>

            <div v-if="totalPages > 1" class="flex flex-wrap items-center justify-center gap-2 pt-2">
                <UButton :disabled="page === 1" color="neutral" variant="ghost" icon="i-lucide-chevron-left" @click="changePage(page - 1)">
                    Sebelumnya
                </UButton>

                <button
                    v-for="item in pageItems"
                    :key="item"
                    class="min-w-10 rounded-xl px-4 py-2 text-sm font-medium transition"
                    :class="item === page ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/20' : item === 'ellipsis' ? 'cursor-default text-slate-400 dark:text-slate-500' : 'bg-white text-slate-700 shadow-sm hover:bg-emerald-50 dark:bg-slate-950/70 dark:text-slate-200 dark:hover:bg-white/5'"
                    :disabled="item === 'ellipsis'"
                    @click="item !== 'ellipsis' && changePage(item)"
                >
                    {{ item === 'ellipsis' ? '...' : item }}
                </button>

                <UButton :disabled="page === totalPages" color="neutral" variant="ghost" icon="i-lucide-chevron-right" @click="changePage(page + 1)">
                    Berikutnya
                </UButton>
            </div>
        </div>

        <UModal v-model:open="isFormOpen" :close="false" :dismissible="false">
            <template #content>
                <div class="flex max-h-[88vh] flex-col overflow-hidden">
                    <div class="shrink-0 border-b border-slate-200/70 px-6 py-5 dark:border-white/10">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-emerald-700 dark:text-emerald-300">
                                    {{ isEditing ? 'Edit artikel' : 'Artikel baru' }}
                                </p>
                                <h3 class="mt-2 text-2xl font-semibold text-slate-900 dark:text-white">
                                    {{ isEditing ? 'Perbarui artikel' : 'Tambah artikel baru' }}
                                </h3>
                                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                                    Isi data inti saja. Slug dan penulis diproses otomatis oleh sistem.
                                </p>
                            </div>

                            <button
                                class="flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-500 transition hover:border-red-200 hover:text-red-600 dark:border-white/10 dark:bg-slate-950/60 dark:text-slate-300"
                                @click="closeForm"
                            >
                                <UIcon name="i-lucide-x" class="h-4 w-4" />
                            </button>
                        </div>
                    </div>

                    <form class="min-h-0 flex-1 overflow-y-auto px-6 py-5" @submit.prevent="submitForm">
                        <div class="grid gap-5 lg:grid-cols-2">
                            <label class="space-y-2">
                                <span class="text-sm font-medium text-slate-700 dark:text-slate-200">
                                    Judul artikel (Indonesia)
                                </span>
                                <input v-model="form.title_id" type="text" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:border-white/10 dark:bg-slate-950/70 dark:text-white" placeholder="Masukkan judul artikel" />
                                <p v-if="fieldError('title_id')" class="text-xs text-red-500">
                                    {{ fieldError('title_id') }}
                                </p>
                            </label>

                            <label class="space-y-2">
                                <span class="text-sm font-medium text-slate-700 dark:text-slate-200">
                                    Judul artikel (English)
                                </span>
                                <input v-model="form.title_en" type="text" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:border-white/10 dark:bg-slate-950/70 dark:text-white" placeholder="Enter article title" />
                                <p v-if="fieldError('title_en')" class="text-xs text-red-500">
                                    {{ fieldError('title_en') }}
                                </p>
                            </label>

                            <label class="space-y-2">
                                <span class="text-sm font-medium text-slate-700 dark:text-slate-200">
                                    Isi artikel (Indonesia)
                                </span>
                                <textarea v-model="form.content_id" rows="10" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm leading-6 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:border-white/10 dark:bg-slate-950/70 dark:text-white" placeholder="Tulis isi artikel di sini"></textarea>
                                <p v-if="fieldError('content_id')" class="text-xs text-red-500">
                                    {{ fieldError('content_id') }}
                                </p>
                            </label>

                            <label class="space-y-2">
                                <span class="text-sm font-medium text-slate-700 dark:text-slate-200">
                                    Isi artikel (English)
                                </span>
                                <textarea v-model="form.content_en" rows="10" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm leading-6 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:border-white/10 dark:bg-slate-950/70 dark:text-white" placeholder="Write the English content here"></textarea>
                                <p v-if="fieldError('content_en')" class="text-xs text-red-500">
                                    {{ fieldError('content_en') }}
                                </p>
                            </label>
                        </div>

                        <div class="mt-5 grid gap-5 lg:grid-cols-[220px_1fr]">
                            <label class="space-y-2">
                                <span class="text-sm font-medium text-slate-700 dark:text-slate-200">
                                    Kategori
                                </span>
                                <select v-model="form.category" class="h-12 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm text-slate-700 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:border-white/10 dark:bg-slate-950/70 dark:text-slate-200">
                                    <option v-for="option in categories" :key="option.value" :value="option.value">
                                        {{ option.label }}
                                    </option>
                                </select>
                                <p v-if="fieldError('category')" class="text-xs text-red-500">
                                    {{ fieldError('category') }}
                                </p>
                            </label>

                            <div class="space-y-2">
                                <span class="text-sm font-medium text-slate-700 dark:text-slate-200">
                                    Status publikasi
                                </span>
                                <label class="flex cursor-pointer items-center justify-between gap-4 rounded-2xl border border-slate-200 bg-white px-4 py-3 dark:border-white/10 dark:bg-slate-950/70">
                                    <div>
                                        <p class="font-medium text-slate-900 dark:text-white">
                                            {{ form.is_published ? 'Terbitkan sekarang' : 'Simpan sebagai draf' }}
                                        </p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">
                                            Status ini bisa diubah kapan saja.
                                        </p>
                                    </div>
                                    <input v-model="form.is_published" type="checkbox" class="h-5 w-5 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500" />
                                </label>
                                <p v-if="fieldError('is_published')" class="text-xs text-red-500">
                                    {{ fieldError('is_published') }}
                                </p>
                            </div>
                        </div>

                        <div class="mt-5 space-y-3">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="text-sm font-medium text-slate-700 dark:text-slate-200">
                                        Gambar artikel
                                    </p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">
                                        Pilih satu atau beberapa gambar. Jika ada gambar baru, gambar lama akan diganti.
                                    </p>
                                </div>

                                <button type="button" class="text-xs font-semibold text-emerald-700 hover:text-emerald-600 dark:text-emerald-300 dark:hover:text-emerald-200" @click="clearImages">
                                    Kosongkan pilihan
                                </button>
                            </div>

                            <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50/70 p-4 dark:border-white/10 dark:bg-white/5">
                                <input
                                    :key="fileInputKey"
                                    type="file"
                                    multiple
                                    accept="image/*"
                                    class="block w-full cursor-pointer rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-600 file:mr-4 file:rounded-lg file:border-0 file:bg-emerald-600 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-emerald-500 dark:border-white/10 dark:bg-slate-950/70 dark:text-slate-300"
                                    @change="handleImages"
                                >

                                <div v-if="existingImages.length || imagePreviews.length" class="mt-4 grid gap-4 md:grid-cols-2">
                                    <div v-if="existingImages.length" class="rounded-2xl bg-white p-3 shadow-sm dark:bg-slate-950/70">
                                        <div class="mb-3 flex items-center justify-between gap-2">
                                            <p class="text-sm font-medium text-slate-900 dark:text-white">Gambar tersimpan</p>
                                            <UBadge color="neutral" variant="soft">{{ existingImages.length }}</UBadge>
                                        </div>
                                        <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
                                            <img v-for="image in existingImages" :key="image" :src="buildImageUrl(image)" alt="Existing article image" class="aspect-square rounded-xl object-cover" />
                                        </div>
                                    </div>

                                    <div v-if="imagePreviews.length" class="rounded-2xl bg-white p-3 shadow-sm dark:bg-slate-950/70">
                                        <div class="mb-3 flex items-center justify-between gap-2">
                                            <p class="text-sm font-medium text-slate-900 dark:text-white">Pratinjau baru</p>
                                            <UBadge color="primary" variant="soft">{{ imagePreviews.length }}</UBadge>
                                        </div>
                                        <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
                                            <img v-for="image in imagePreviews" :key="image" :src="image" alt="Preview image" class="aspect-square rounded-xl object-cover" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <UAlert
                            v-if="formError"
                            :title="formErrorTitle"
                            :description="formError"
                            color="red"
                            variant="soft"
                            icon="i-lucide-triangle-alert"
                            class="mt-5"
                        />
                    </form>

                    <div class="shrink-0 border-t border-slate-200/70 px-6 py-4 dark:border-white/10">
                        <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                            <UButton color="neutral" variant="soft" @click="closeForm">
                                Batal
                            </UButton>
                            <UButton color="primary" :loading="isSubmitting" icon="i-lucide-save" @click="submitForm">
                                {{ isEditing ? 'Simpan perubahan' : 'Simpan artikel' }}
                            </UButton>
                        </div>
                    </div>
                </div>
            </template>
        </UModal>

        <UModal v-model:open="isDeleteOpen" :close="false" :dismissible="false">
            <template #content>
                <div class="p-6 sm:p-7">
                    <div class="flex items-start gap-4">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-red-100 text-red-700 dark:bg-red-500/15 dark:text-red-300">
                            <UIcon name="i-lucide-trash-2" class="h-5 w-5" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-red-600 dark:text-red-300">
                                Hapus artikel
                            </p>
                            <h3 class="mt-2 text-xl font-semibold text-slate-900 dark:text-white">
                                {{ pendingDelete ? titleOf(pendingDelete) : 'Artikel' }}
                            </h3>
                            <p class="mt-2 text-sm leading-6 text-slate-500 dark:text-slate-400">
                                Tindakan ini akan menghapus data artikel dan gambar yang tersimpan. Langkah ini tidak bisa dibatalkan.
                            </p>
                        </div>
                    </div>

                    <div v-if="deleteError" class="mt-5 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-900/60 dark:bg-red-950/30 dark:text-red-300">
                        {{ deleteError }}
                    </div>

                    <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:justify-end">
                        <UButton color="neutral" variant="soft" @click="closeDelete">
                            Batal
                        </UButton>
                        <UButton color="red" :loading="isDeleting" icon="i-lucide-trash-2" @click="confirmDelete">
                            Hapus artikel
                        </UButton>
                    </div>
                </div>
            </template>
        </UModal>
    </div>
</template>

<script setup lang="ts">
type ArticleRecord = {
    id: number | string;
    title_id: string;
    title_en: string;
    content_id: string;
    content_en: string;
    slug_id: string;
    slug_en: string;
    category: string;
    image?: string[] | string | null;
    is_published: boolean;
    created_at?: string;
    author?: { name?: string } | null;
    [key: string]: any;
};

type PaginatedResponse<T> = {
    data: T[];
    current_page: number;
    last_page: number;
    total: number;
    from: number | null;
    to: number | null;
};

type ApiResponse<T> = {
    data: PaginatedResponse<T>;
};

definePageMeta({ layout: 'admin' });

const client = useSanctumClient();
const config = useRuntimeConfig();

const categories = [
    { value: 'news', label: 'Berita' },
    { value: 'announcement', label: 'Pengumuman' },
    { value: 'event', label: 'Kegiatan' },
    { value: 'education', label: 'Edukasi' },
];

const page = ref(1);
const searchInput = ref('');
const search = ref('');
const category = ref('');
const flashMessage = ref('');
const flashType = ref<'success' | 'error'>('success');

const searchTerm = computed(() => search.value.trim());

watch([category, searchTerm], () => {
    page.value = 1;
});

const {
    data: apiResponse,
    status,
    error,
    refresh,
} = useAsyncData<ApiResponse<ArticleRecord>>(
    'admin-articles',
    () => client('/api/articles', {
        params: {
            page: page.value,
            per_page: 10,
            category: category.value || undefined,
            search: searchTerm.value || undefined,
        },
    }),
    { watch: [page, category, searchTerm] }
);

const paginator = computed<PaginatedResponse<ArticleRecord>>(() => apiResponse.value?.data ?? { data: [], current_page: 1, last_page: 1, total: 0, from: null, to: null });
const articles = computed(() => paginator.value.data ?? []);
const totalItems = computed(() => paginator.value.total ?? 0);
const totalPages = computed(() => paginator.value.last_page ?? 1);
const fromItem = computed(() => paginator.value.from ?? 0);
const toItem = computed(() => paginator.value.to ?? 0);
const pending = computed(() => status.value === 'pending');
const filteredArticles = computed(() => articles.value);

const categoryLabel = (value?: string) => categories.find((item) => item.value === value)?.label ?? value ?? '-';
const statusColor = (published?: boolean) => (published ? 'success' : 'neutral');
const statusText = (published?: boolean) => (published ? 'Terbit' : 'Draf');

const backendUrl = (config.public.sanctum?.baseUrl as string | undefined) || 'http://127.0.0.1:8000';

const buildImageUrl = (path?: string) => {
    if (!path) return '';
    if (path.startsWith('http')) return path;
    const base = backendUrl.endsWith('/') ? backendUrl.slice(0, -1) : backendUrl;
    const cleanPath = path.startsWith('/') ? path : `/${path}`;
    return `${base}${cleanPath}`;
};

const normalizeImages = (value: ArticleRecord['image']) => {
    if (!value) return [] as string[];
    if (Array.isArray(value)) return value.filter((item): item is string => typeof item === 'string');
    if (typeof value === 'string') {
        try {
            const parsed = JSON.parse(value);
            if (Array.isArray(parsed)) return parsed.filter((item): item is string => typeof item === 'string');
        } catch {
            return [value];
        }
    }
    return [] as string[];
};

const imagesOf = (article: ArticleRecord) => normalizeImages(article.image);
const coverOf = (article: ArticleRecord) => {
    const [first] = imagesOf(article);
    return first ? buildImageUrl(first) : '';
};
const titleOf = (article: ArticleRecord) => article.title_id || article.title_en || 'Artikel';
const excerptOf = (article: ArticleRecord) => stripHtml(article.content_id || article.content_en || '');

const formatDate = (value?: string) => {
    if (!value) return '-';
    return new Date(value).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
};

const stripHtml = (html: string) => html.replace(/<[^>]*>/g, '').replace(/\s+/g, ' ').trim();

const pageItems = computed(() => {
    const last = totalPages.value;
    const current = page.value;

    if (last <= 7) return Array.from({ length: last }, (_, index) => index + 1);

    const items: Array<number | 'ellipsis'> = [1];
    const start = Math.max(2, current - 1);
    const end = Math.min(last - 1, current + 1);

    if (start > 2) items.push('ellipsis');
    for (let index = start; index <= end; index += 1) items.push(index);
    if (end < last - 1) items.push('ellipsis');
    items.push(last);

    return items;
});

const summaryCards = computed(() => {
    const pageArticles = filteredArticles.value;
    return [
        { label: 'Total artikel', value: totalItems.value.toString(), helper: 'Semua data yang cocok dengan filter', icon: 'i-lucide-newspaper', iconBg: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300' },
        { label: 'Tampil di halaman ini', value: `${fromItem.value || 0}-${toItem.value || 0}`, helper: 'Rentang artikel yang sedang dilihat', icon: 'i-lucide-list', iconBg: 'bg-sky-100 text-sky-700 dark:bg-sky-500/15 dark:text-sky-300' },
        { label: 'Terbit', value: pageArticles.filter((article) => article.is_published).length.toString(), helper: 'Jumlah artikel aktif pada halaman ini', icon: 'i-lucide-badge-check', iconBg: 'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300' },
        { label: 'Dengan gambar', value: pageArticles.filter((article) => imagesOf(article).length > 0).length.toString(), helper: 'Artikel yang sudah punya visual', icon: 'i-lucide-image', iconBg: 'bg-teal-100 text-teal-700 dark:bg-teal-500/15 dark:text-teal-300' },
    ];
});

const applySearch = () => {
    search.value = searchInput.value.trim();
    page.value = 1;
};

const clearFilters = () => {
    searchInput.value = '';
    search.value = '';
    category.value = '';
    page.value = 1;
    refresh();
};

const changePage = (target: number) => {
    if (target < 1 || target > totalPages.value) return;
    page.value = target;
    if (import.meta.client) {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
};

const isFormOpen = ref(false);
const isEditing = ref(false);
const activeArticleId = ref<number | string | null>(null);
const formError = ref('');
const fieldErrors = ref<Record<string, string[]>>({});
const isSubmitting = ref(false);
const existingImages = ref<string[]>([]);
const imagePreviews = ref<string[]>([]);
const imageFiles = ref<File[]>([]);
const fileInputKey = ref(0);
const removeImagesRequested = ref(false);

const isDeleteOpen = ref(false);
const isDeleting = ref(false);
const pendingDelete = ref<ArticleRecord | null>(null);
const deleteError = ref('');

const form = reactive({
    title_id: '',
    title_en: '',
    content_id: '',
    content_en: '',
    category: categories[0].value,
    is_published: false,
});

const flashTitle = computed(() => (flashType.value === 'error' ? 'Gagal' : 'Berhasil'));
const formErrorTitle = computed(() => (isEditing.value ? 'Gagal memperbarui artikel' : 'Gagal menyimpan artikel'));

const clearPreviewUrls = () => {
    imagePreviews.value.forEach((url) => URL.revokeObjectURL(url));
    imagePreviews.value = [];
};

const resetForm = () => {
    form.title_id = '';
    form.title_en = '';
    form.content_id = '';
    form.content_en = '';
    form.category = categories[0].value;
    form.is_published = false;
    activeArticleId.value = null;
    isEditing.value = false;
    formError.value = '';
    fieldErrors.value = {};
    existingImages.value = [];
    removeImagesRequested.value = false;
    clearPreviewUrls();
    imageFiles.value = [];
    fileInputKey.value += 1;
};

const openCreate = () => {
    resetForm();
    isFormOpen.value = true;
};

const openEdit = (article: ArticleRecord) => {
    resetForm();
    isEditing.value = true;
    activeArticleId.value = article.id;
    form.title_id = article.title_id ?? '';
    form.title_en = article.title_en ?? '';
    form.content_id = article.content_id ?? '';
    form.content_en = article.content_en ?? '';
    form.category = article.category || categories[0].value;
    form.is_published = Boolean(article.is_published);
    existingImages.value = normalizeImages(article.image);
    isFormOpen.value = true;
};

const closeForm = () => {
    isFormOpen.value = false;
    setTimeout(() => resetForm(), 250);
};

const handleImages = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const files = Array.from(target.files ?? []);
    clearPreviewUrls();
    imageFiles.value = files;
    imagePreviews.value = files.map((file) => URL.createObjectURL(file));
    removeImagesRequested.value = false;
};

const clearImages = () => {
    clearPreviewUrls();
    imageFiles.value = [];
    existingImages.value = [];
    fileInputKey.value += 1;
    removeImagesRequested.value = isEditing.value;
};

const fieldError = (field: string) => fieldErrors.value[field]?.[0] ?? '';

const buildPayload = () => {
    const payload = new FormData();
    payload.append('title_id', form.title_id.trim());
    payload.append('title_en', form.title_en.trim());
    payload.append('content_id', form.content_id.trim());
    payload.append('content_en', form.content_en.trim());
    payload.append('category', form.category);
    payload.append('is_published', form.is_published ? '1' : '0');

    if (isEditing.value && removeImagesRequested.value && imageFiles.value.length === 0) {
        payload.append('remove_images', '1');
    }

    imageFiles.value.forEach((file) => payload.append('image[]', file));

    return payload;
};

const submitForm = async () => {
    fieldErrors.value = {};
    formError.value = '';

    if (!form.title_id.trim()) {
        fieldErrors.value.title_id = ['Judul Indonesia wajib diisi.'];
        return;
    }
    if (!form.title_en.trim()) {
        fieldErrors.value.title_en = ['English title wajib diisi.'];
        return;
    }
    if (!form.content_id.trim()) {
        fieldErrors.value.content_id = ['Isi artikel Indonesia wajib diisi.'];
        return;
    }
    if (!form.content_en.trim()) {
        fieldErrors.value.content_en = ['Isi artikel English wajib diisi.'];
        return;
    }

    isSubmitting.value = true;

    try {
        const payload = buildPayload();

        if (isEditing.value && activeArticleId.value !== null) {
            payload.append('_method', 'PUT');
            await client(`/api/articles/${activeArticleId.value}`, {
                method: 'POST',
                body: payload,
            });
        } else {
            await client('/api/articles', {
                method: 'POST',
                body: payload,
            });
        }

        flashType.value = 'success';
        flashMessage.value = isEditing.value ? 'Artikel berhasil diperbarui.' : 'Artikel baru berhasil disimpan.';
        closeForm();
        await refresh();
    } catch (err: any) {
        const data = err?.response?._data || err?.data;
        if (err?.response?.status === 422 || err?.status === 422) {
            fieldErrors.value = data?.errors || {};
            formError.value = data?.message || 'Periksa kembali isian form.';
        } else {
            formError.value = data?.message || 'Terjadi kesalahan saat menyimpan data.';
        }

        flashType.value = 'error';
        flashMessage.value = formError.value;
    } finally {
        isSubmitting.value = false;
    }
};

const askDelete = (article: ArticleRecord) => {
    pendingDelete.value = article;
    deleteError.value = '';
    isDeleteOpen.value = true;
};

const closeDelete = () => {
    isDeleteOpen.value = false;
    deleteError.value = '';
    pendingDelete.value = null;
};

const confirmDelete = async () => {
    if (!pendingDelete.value) return;

    isDeleting.value = true;
    deleteError.value = '';

    try {
        await client(`/api/articles/${pendingDelete.value.id}`, { method: 'DELETE' });
        flashType.value = 'success';
        flashMessage.value = 'Artikel berhasil dihapus.';
        closeDelete();
        await refresh();
    } catch (err: any) {
        const data = err?.response?._data || err?.data;
        deleteError.value = data?.message || 'Gagal menghapus artikel.';
        flashType.value = 'error';
        flashMessage.value = deleteError.value;
    } finally {
        isDeleting.value = false;
    }
};

onBeforeUnmount(() => {
    clearPreviewUrls();
});
</script>
