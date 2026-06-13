<template>
    <div
        class="rich-editor w-full rounded-lg border border-gray-200 dark:border-gray-800 overflow-hidden bg-white dark:bg-gray-900"
    >
        <UEditor
            v-slot="{ editor }"
            v-model="model"
            content-type="html"
            class="w-full"
            :class="editorClass"
            :placeholder="placeholder"
            :editable="!disabled"
            :starter-kit="{
                headings: { levels: [1, 2, 3, 4] },
                dropcursor: { color: 'var(--ui-primary)', width: 2 },
                link: { openOnClick: false },
                ...starterKit,
            }"
        >
            <UEditorToolbar
                :editor="editor"
                :items="toolbarItems"
                class="border-b border-gray-200 dark:border-gray-800 px-2 py-1.5 flex-wrap"
            />
        </UEditor>
    </div>
</template>

<script setup lang="ts">
import type { EditorToolbarItem } from "@nuxt/ui";
import type { StarterKitOptions } from "@tiptap/starter-kit";

const props = withDefaults(
    defineProps<{
        placeholder?: string;
        disabled?: boolean;
        editorClass?: string;
        starterKit?: Partial<StarterKitOptions>;
        items?: EditorToolbarItem[][];
    }>(),
    {
        placeholder: "Tulis konten di sini...",
        disabled: false,
        editorClass: "min-h-48",
    },
);

const model = defineModel<string>({ default: "" });

const defaultItems: EditorToolbarItem[][] = [
    [
        {
            icon: "i-lucide-heading",
            tooltip: { text: "Heading" },
            content: { align: "start" },
            items: [
                {
                    kind: "heading",
                    level: 1,
                    icon: "i-lucide-heading-1",
                    label: "Heading 1",
                },
                {
                    kind: "heading",
                    level: 2,
                    icon: "i-lucide-heading-2",
                    label: "Heading 2",
                },
                {
                    kind: "heading",
                    level: 3,
                    icon: "i-lucide-heading-3",
                    label: "Heading 3",
                },
                {
                    kind: "heading",
                    level: 4,
                    icon: "i-lucide-heading-4",
                    label: "Heading 4",
                },
            ],
        },
    ],
    [
        {
            kind: "mark",
            mark: "bold",
            icon: "i-lucide-bold",
            tooltip: { text: "Bold" },
        },
        {
            kind: "mark",
            mark: "italic",
            icon: "i-lucide-italic",
            tooltip: { text: "Italic" },
        },
        {
            kind: "mark",
            mark: "underline",
            icon: "i-lucide-underline",
            tooltip: { text: "Underline" },
        },
        {
            kind: "mark",
            mark: "strike",
            icon: "i-lucide-strikethrough",
            tooltip: { text: "Strikethrough" },
        },
        {
            kind: "mark",
            mark: "code",
            icon: "i-lucide-code",
            tooltip: { text: "Inline Code" },
        },
    ],
    [
        {
            kind: "bulletList",
            icon: "i-lucide-list",
            tooltip: { text: "Bullet List" },
        },
        {
            kind: "orderedList",
            icon: "i-lucide-list-ordered",
            tooltip: { text: "Numbered List" },
        },
        {
            kind: "blockquote",
            icon: "i-lucide-quote",
            tooltip: { text: "Blockquote" },
        },
        {
            kind: "codeBlock",
            icon: "i-lucide-square-code",
            tooltip: { text: "Code Block" },
        },
    ],
    [
        { kind: "link", icon: "i-lucide-link", tooltip: { text: "Link" } },
        {
            kind: "horizontalRule",
            icon: "i-lucide-separator-horizontal",
            tooltip: { text: "Divider" },
        },
    ],
    [
        { kind: "undo", icon: "i-lucide-undo-2", tooltip: { text: "Undo" } },
        { kind: "redo", icon: "i-lucide-redo-2", tooltip: { text: "Redo" } },
        {
            kind: "clearFormatting",
            icon: "i-lucide-remove-formatting",
            tooltip: { text: "Clear Formatting" },
        },
    ],
];

const toolbarItems = computed(() => props.items ?? defaultItems);
</script>
