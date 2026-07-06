<template>
  <div class="space-y-6 flex flex-col h-full mx-auto w-full">
    <AdminHeader
      title="Sertifikat"
      icon="i-lucide-award"
      description="Kelola penerbitan sertifikat kelulusan peserta program."
      :showCreate="false"
    >
      <template #actions>
        <div class="flex items-center gap-3">
          <UInput
            v-model="searchInput"
            icon="i-lucide-search"
            placeholder="Cari nomor sertifikat atau nama pendaftar..."
            size="lg"
            class="min-w-0 sm:w-72"
            @keyup.enter="applySearch"
          />

          <UButton color="primary" icon="i-lucide-plus" @click="openIssueModal">
            Terbitkan Sertifikat
          </UButton>
          <UButton color="neutral" variant="soft" icon="i-lucide-rotate-cw" @click="refresh">
            Muat ulang
          </UButton>
          <UButton color="neutral" variant="ghost" icon="i-lucide-eraser" @click="clearSearch">Reset</UButton>
        </div>
      </template>
    </AdminHeader>

    <div class="flex-1 bg-white dark:bg-gray-900 ring-1 ring-gray-200 dark:ring-gray-800 rounded-lg shadow-sm overflow-hidden flex flex-col">
      <div class="overflow-x-auto scrollbar-hide">
        <div class="min-w-max">
          <UTable :data="certificates" :columns="columns" :loading="pending" class="w-full min-w-max" />
        </div>
      </div>
    </div>

    <div v-if="totalItems > 0" class="flex flex-col sm:flex-row justify-between items-center gap-4 pt-2">
      <span class="text-sm text-gray-500">
        Menampilkan <span class="font-medium text-gray-900 dark:text-white">{{ fromItem }}</span>
        - <span class="font-medium text-gray-900 dark:text-white">{{ toItem }}</span>
        dari <span class="font-medium text-gray-900 dark:text-white">{{ totalItems }}</span> sertifikat
      </span>

      <UPagination v-model:page="page" :total="totalItems" :items-per-page="15" @update:model-value="changePage" />
    </div>

    <div v-else-if="!pending && totalItems === 0" class="flex flex-col items-center justify-center py-12 text-center ring-1 ring-gray-200 dark:ring-gray-800 rounded-lg bg-white dark:bg-gray-900">
      <UIcon name="i-lucide-award" class="w-12 h-12 text-gray-400 mb-4" />
      <h3 class="text-lg font-medium text-gray-900 dark:text-white">Belum ada sertifikat</h3>
      <p class="text-gray-500 mt-1 max-w-sm">Terbitkan sertifikat pertama untuk peserta yang sudah selesai program.</p>
    </div>

    <!-- Modal Terbitkan Sertifikat -->
    <UModal v-model:open="isIssueOpen" title="Terbitkan Sertifikat">
      <template #body>
        <div class="space-y-4">
          <div>
            <label class="text-sm text-gray-500 mb-1.5 block">Cari Pendaftar</label>
            <UInput
              v-model="registrationSearch"
              icon="i-lucide-search"
              placeholder="Cari nama, email, atau telepon..."
              class="w-full"
            />

            <div class="mt-2 max-h-56 overflow-y-auto rounded-lg ring-1 ring-gray-200 dark:ring-gray-800 divide-y divide-gray-100 dark:divide-gray-800">
              <div v-if="searchingRegistrations" class="p-4 text-center text-sm text-gray-500">
                Mencari...
              </div>
              <div v-else-if="registrationOptions.length === 0" class="p-4 text-center text-sm text-gray-500">
                Ketik nama, email, atau telepon pendaftar.
              </div>
              <button
                v-for="option in registrationOptions"
                :key="option.id"
                type="button"
                class="w-full text-left px-3 py-2.5 text-sm transition-colors hover:bg-gray-50 dark:hover:bg-gray-800"
                :class="selectedRegistration?.id === option.id ? 'bg-emerald-50 dark:bg-emerald-500/10' : ''"
                @click="selectedRegistration = option"
              >
                <p class="font-medium text-gray-900 dark:text-white">{{ option.full_name }}</p>
                <p class="text-xs text-gray-500">{{ option.email }} &middot; {{ option.program?.title_id || "-" }}</p>
              </button>
            </div>

            <p v-if="selectedRegistration" class="mt-2 text-sm text-emerald-700 dark:text-emerald-400">
              Dipilih: {{ selectedRegistration.full_name }}
            </p>
          </div>

          <div>
            <label class="text-sm text-gray-500 mb-1.5 block">ID Akun Moodle/SSO (opsional)</label>
            <UInput v-model="externalUserId" type="number" placeholder="Kosongkan jika belum ada" class="w-full" />
          </div>
        </div>
      </template>

      <template #footer>
        <div class="flex justify-end gap-3 w-full">
          <UButton color="neutral" variant="ghost" @click="isIssueOpen = false">Batal</UButton>
          <UButton color="primary" :loading="issuing" :disabled="!selectedRegistration" @click="issueCertificate">
            Terbitkan
          </UButton>
        </div>
      </template>
    </UModal>
  </div>
</template>

<script setup lang="ts">
import { h, resolveComponent } from "vue";
import type { TableColumn } from "@nuxt/ui";
import type { Row } from "@tanstack/vue-table";
import type { CertificateItem } from "~/composables/useCertificates";

definePageMeta({ layout: "admin" });

const UButton = resolveComponent("UButton");
const UDropdownMenu = resolveComponent("UDropdownMenu");

const {
  page,
  searchInput,
  certificates,
  totalItems,
  fromItem,
  toItem,
  pending,
  applySearch,
  clearSearch,
  changePage,
  refresh,
} = useCertificates();

const toast = useToast();
const client = useSanctumClient();

// --- Modal terbitkan sertifikat ---
const isIssueOpen = ref(false);
const registrationSearch = ref("");
const registrationOptions = ref<any[]>([]);
const selectedRegistration = ref<any>(null);
const externalUserId = ref<string>("");
const searchingRegistrations = ref(false);
const issuing = ref(false);

function openIssueModal() {
  registrationSearch.value = "";
  registrationOptions.value = [];
  selectedRegistration.value = null;
  externalUserId.value = "";
  isIssueOpen.value = true;
}

let searchDebounce: ReturnType<typeof setTimeout> | undefined;
watch(registrationSearch, (term) => {
  clearTimeout(searchDebounce);
  if (!term.trim()) {
    registrationOptions.value = [];
    return;
  }

  searchDebounce = setTimeout(async () => {
    searchingRegistrations.value = true;
    try {
      const response: any = await client("/api/program-registrations", {
        params: { search: term.trim() },
      });
      registrationOptions.value = response?.data?.data ?? [];
    } catch {
      registrationOptions.value = [];
    } finally {
      searchingRegistrations.value = false;
    }
  }, 400);
});

async function issueCertificate() {
  if (!selectedRegistration.value) return;

  issuing.value = true;
  try {
    await client("/api/certificates", {
      method: "POST",
      body: {
        program_registration_id: selectedRegistration.value.id,
        external_user_id: externalUserId.value ? Number(externalUserId.value) : undefined,
      },
    });
    toast.add({ title: "Berhasil", description: "Sertifikat berhasil diterbitkan", color: "success", icon: "i-lucide-circle-check" });
    isIssueOpen.value = false;
    refresh();
  } catch (e: any) {
    toast.add({ title: "Gagal", description: e?.data?.message || e?.message || "Error", color: "red", icon: "i-lucide-triangle-alert" });
  } finally {
    issuing.value = false;
  }
}

// --- Tabel ---
const columns: TableColumn<CertificateItem>[] = [
  {
    id: "no",
    header: "No",
    meta: { class: { th: "w-12 text-center", td: "text-center text-gray-500" } },
    cell: ({ row }) => {
      const itemsPerPage = 15;
      const number = (page.value - 1) * itemsPerPage + row.index + 1;
      return h("span", {}, number);
    },
  },
  {
    id: "registrant",
    header: "Pendaftar",
    meta: { class: { th: "min-w-[220px]" } },
    cell: ({ row }) => {
      const reg = row.original.program_registration || {};
      return h("div", { class: "flex flex-col" }, [
        h("span", { class: "font-medium text-gray-900 dark:text-white line-clamp-1" }, reg.full_name || "-"),
        h("span", { class: "text-xs text-gray-500 line-clamp-1" }, reg.email || ""),
      ]);
    },
  },
  {
    id: "program",
    header: "Program",
    meta: { class: { th: "min-w-[200px]" } },
    cell: ({ row }) => h("span", { class: "text-sm text-gray-700 line-clamp-1" }, row.original.program?.title_id || "-"),
  },
  {
    accessorKey: "certificate_number",
    header: "Nomor Sertifikat",
    meta: { class: { th: "w-56" } },
    cell: ({ row }) => h("span", { class: "text-sm font-mono text-gray-700" }, row.original.certificate_number),
  },
  {
    accessorKey: "issued_at",
    header: "Tanggal Terbit",
    meta: { class: { th: "w-36" } },
    cell: ({ row }) => {
      const dateStr = row.original.issued_at;
      const formatted = dateStr ? new Date(dateStr).toLocaleDateString("id-ID") : "-";
      return h("span", { class: "text-sm text-gray-500 whitespace-nowrap" }, formatted);
    },
  },
  {
    id: "actions",
    header: "",
    meta: { class: { th: "w-20", td: "text-right" } },
    cell: ({ row }) => {
      return h(UDropdownMenu, { content: { align: "end" }, items: getRowItems(row) }, () =>
        h(UButton, { icon: "i-lucide-more-vertical", color: "neutral", variant: "ghost", size: "sm" }),
      );
    },
  },
];

function getRowItems(row: Row<CertificateItem>) {
  return [
    [
      {
        label: "Salin Nomor",
        icon: "i-lucide-copy",
        onSelect: () => {
          useClipboard().copy(row.original.certificate_number);
          toast.add({ title: "Disalin", description: row.original.certificate_number, color: "success" });
        },
      },
    ],
    [
      {
        label: "Hapus",
        icon: "i-lucide-trash-2",
        color: "error",
        onSelect: async () => {
          if (!confirm("Hapus sertifikat ini?")) return;
          try {
            await client(`/api/certificates/${row.original.id}`, { method: "DELETE" });
            toast.add({ title: "Terhapus", description: "Sertifikat dihapus", color: "success" });
            refresh();
          } catch (e: any) {
            toast.add({ title: "Gagal", description: e?.message || "Error", color: "red", icon: "i-lucide-triangle-alert" });
          }
        },
      },
    ],
  ];
}
</script>
