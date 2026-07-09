<template>
  <div class="space-y-6 flex flex-col h-full mx-auto w-full">
    <AdminHeader
      title="Transaksi"
      icon="i-lucide-credit-card"
      description="Kelola transaksi pembayaran pendaftaran program."
      :showCreate="false"
    >
      <template #actions>
        <div class="flex items-center gap-3">
          <UInput
            v-model="searchInput"
            icon="i-lucide-search"
            placeholder="Cari nama pendaftar, email, atau telepon..."
            size="lg"
            class="min-w-0 sm:w-72"
            @keyup.enter="applySearch"
          />

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
          <UTable :data="transactions" :columns="columns" :loading="pending" class="w-full min-w-max" />
        </div>
      </div>
    </div>

    <div v-if="totalItems > 0" class="flex flex-col sm:flex-row justify-between items-center gap-4 pt-2">
      <span class="text-sm text-gray-500">
        Menampilkan <span class="font-medium text-gray-900 dark:text-white">{{ fromItem }}</span>
        - <span class="font-medium text-gray-900 dark:text-white">{{ toItem }}</span>
        dari <span class="font-medium text-gray-900 dark:text-white">{{ totalItems }}</span> transaksi
      </span>

      <UPagination v-model:page="page" :total="totalItems" :items-per-page="15" @update:model-value="changePage" />
    </div>

    <div v-else-if="!pending && totalItems === 0" class="flex flex-col items-center justify-center py-12 text-center ring-1 ring-gray-200 dark:ring-gray-800 rounded-lg bg-white dark:bg-gray-900">
      <UIcon name="i-lucide-file-search" class="w-12 h-12 text-gray-400 mb-4" />
      <h3 class="text-lg font-medium text-gray-900 dark:text-white">Belum ada transaksi</h3>
      <p class="text-gray-500 mt-1 max-w-sm">Coba ubah kata kunci pencarian atau tunggu transaksi baru.</p>
      <div class="mt-4 flex gap-3">
        <UButton color="neutral" variant="soft" @click="clearSearch">Bersihkan Filter</UButton>
      </div>
    </div>

    <AdminDeleteModal
      v-model:open="isDeleteOpen"
      :id="selectedForDelete?.id"
      :title="selectedForDelete?.id ? `Transaksi #${selectedForDelete.id}` : undefined"
      endpoint="api/transactions"
      @success="handleDeleteSuccess"
      @error="handleDeleteError"
    />
  </div>
</template>

<script setup lang="ts">
import { h, resolveComponent } from "vue";
import { useClipboard } from "@vueuse/core";
import type { TableColumn } from "@nuxt/ui";
import type { Row } from "@tanstack/vue-table";

definePageMeta({ layout: "admin" });

const UButton = resolveComponent("UButton");
const UDropdownMenu = resolveComponent("UDropdownMenu");
const UBadge = resolveComponent("UBadge");

const {
  page,
  searchInput,
  transactions,
  totalItems,
  fromItem,
  toItem,
  pending,
  applySearch,
  clearSearch,
  changePage,
  refresh,
} = useTransactions();

const toast = useToast();
const client = useSanctumClient();

export interface TransactionRow {
  id?: number | string;
  amount: number;
  payment_status?: string;
  transaction_receipt?: string | null;
  created_at?: string;
  program_registration?: any;
}

const { copy } = useClipboard();

const isDeleteOpen = ref(false);
const selectedForDelete = ref<TransactionRow | null>(null);

function triggerDelete(row: Row<TransactionRow>) {
  selectedForDelete.value = row.original;
  isDeleteOpen.value = true;
}

function handleDeleteSuccess() {
  toast.add({ title: "Terhapus", description: "Transaksi dihapus", color: "success" });
  refresh();
}

function handleDeleteError(message: string) {
  toast.add({ title: "Gagal", description: message || "Error", color: "red", icon: "i-lucide-triangle-alert" });
}

const fileUrl = useFileUrl();
function viewReceipt(row: Row<TransactionRow>) {
  const path = row.original.transaction_receipt;
  if (!path) {
    toast.add({ title: "Belum ada bukti transfer", description: "Pendaftar belum mengunggah bukti transfer.", color: "warning" });
    return;
  }
  window.open(fileUrl(path), "_blank");
}

const columns: TableColumn<TransactionRow>[] = [
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
    meta: { class: { th: "min-w-[240px]" } },
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
    cell: ({ row }) => h("span", { class: "text-sm text-gray-700 line-clamp-1" }, row.original.program_registration?.program?.title_id || "-"),
  },
  {
    accessorKey: "amount",
    header: "Jumlah",
    meta: { class: { th: "w-36 text-right" } },
    cell: ({ row }) => h("span", { class: "text-sm text-gray-700 text-right" }, `Rp ${row.original.amount ?? 0}`),
  },
  {
    id: "status",
    header: "Status Pembayaran",
    meta: { class: { th: "w-36" } },
    cell: ({ row }) => {
      const status = row.original.payment_status || "pending";
      const colorMap: Record<string, string> = { pending: "warning", completed: "success", failed: "error" };
      return h(UBadge, { color: colorMap[status] || "neutral", variant: "subtle", class: "capitalize" }, () => status);
    },
  },
  {
    accessorKey: "created_at",
    header: "Tanggal",
    meta: { class: { th: "w-40" } },
    cell: ({ row }) => {
      const dateStr = row.original.created_at;
      const formatted = dateStr ? new Date(dateStr).toLocaleString() : "-";
      return h("span", { class: "text-sm text-gray-500 whitespace-nowrap" }, formatted);
    },
  },
  {
    id: "actions",
    header: "",
    meta: { class: { th: "w-28", td: "text-right" } },
    cell: ({ row }) => {
      return h(UDropdownMenu, { content: { align: "end" }, items: getRowItems(row) }, () =>
        h(UButton, { icon: "i-lucide-more-vertical", color: "neutral", variant: "ghost", size: "sm" }),
      );
    },
  },
];

function getRowItems(row: Row<TransactionRow>) {
  return [
    [
      {
        label: "Lihat Bukti Transfer",
        icon: "i-lucide-eye",
        onSelect: () => viewReceipt(row),
      },
      {
        label: "Tandai Selesai",
        icon: "i-lucide-check",
        onSelect: async () => {
          try {
            await client(`/api/transactions/${row.original.id}`, { method: "PUT", body: { payment_status: "completed" } });
            toast.add({ title: "Berhasil", description: "Status diubah menjadi completed", color: "success", icon: "i-lucide-circle-check" });
            refresh();
          } catch (e: any) {
            toast.add({ title: "Gagal", description: e?.message || "Error", color: "red", icon: "i-lucide-triangle-alert" });
          }
        },
      },
      {
        label: "Tandai Gagal",
        icon: "i-lucide-x",
        onSelect: async () => {
          try {
            await client(`/api/transactions/${row.original.id}`, { method: "PUT", body: { payment_status: "failed" } });
            toast.add({ title: "Berhasil", description: "Status diubah menjadi failed", color: "success", icon: "i-lucide-circle-check" });
            refresh();
          } catch (e: any) {
            toast.add({ title: "Gagal", description: e?.message || "Error", color: "red", icon: "i-lucide-triangle-alert" });
          }
        },
      },
    ],
    [
      {
        label: "Copy ID",
        icon: "i-lucide-copy",
        onSelect: () => {
          const id = row.original.id ? String(row.original.id) : "";
          if (id) {
            copy(id);
            toast.add({ title: "ID disalin", description: `ID: ${id}`, color: "success" });
          }
        },
      },
    ],
    [
      {
        label: "Hapus",
        icon: "i-lucide-trash-2",
        color: "error",
        onSelect: () => triggerDelete(row),
      },
    ],
  ];
}
</script>
