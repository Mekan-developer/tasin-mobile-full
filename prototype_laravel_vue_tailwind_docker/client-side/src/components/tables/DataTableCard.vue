<template>
  <div class="w-full p-2 flex flex-col gap-2">
    <!-- Карточка -->
    <div class="flex gap-2 justify-end w-full">
      <button @click="$emit('create')" class="bg-my-gradient hover:bg-my-gradient-reverse hover:scale-102 active:scale-98 text-white font-medium py-3 px-6 rounded-sm transition duration-300">Добавить</button>
    </div>

    <div class="bg-white dark:bg-midnight shadow-sm rounded-sm border border-neutral-200 dark:border-neutral-800 overflow-hidden">
      <!-- Таблица -->
      <div class="w-full overflow-x-auto">
        <table class="min-w-full text-left">
          <thead class="bg-my-gradient hover:bg-my-gradient-reverse text-white">
            <tr>
              <th
                v-for="col in columns"
                :key="col.key"
                class="px-4 sm:px-5 py-3 text-xs font-semibold uppercase tracking-wide"
              >
                <button
                  v-if="col.sortable !== false"
                  class="inline-flex items-center gap-1"
                  @click="toggleSort(col.key)"
                >
                  <span>{{ col.label }}</span>
                  <span v-if="sort.key === col.key">
                    <svg
                      v-if="sort.dir === 'asc'"
                      xmlns="http://www.w3.org/2000/svg"
                      class="size-3.5"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M7 14l5-5 5 5"
                      />
                    </svg>
                    <svg
                      v-else
                      xmlns="http://www.w3.org/2000/svg"
                      class="size-3.5"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M7 10l5 5 5-5"
                      />
                    </svg>
                  </span>
                </button>
                <span v-else>{{ col.label }}</span>
              </th>
              <th
                class="px-4 sm:px-5 py-3 text-xs font-semibold uppercase tracking-wide"
              >
                Действия
              </th>
            </tr>
          </thead>

          <tbody>
            <tr v-if="pagedRows.length === 0">
              <td
                :colspan="columns.length + 1"
                class="px-4 sm:px-5 py-10 text-center text-sm text-neutral-500 dark:text-neutral-400"
              >
                <div class="flex flex-col items-center gap-3">
                  <p>Данных пока нет</p>
                  <button
                    @click="$emit('create')"
                    class="px-4 py-2 text-sm rounded-sm bg-blue-600 text-white hover:bg-blue-500"
                  >
                    Создать первую запись
                  </button>
                </div>
              </td>
            </tr>

            <tr
              v-for="row in pagedRows"
              :key="row[idKey]"
              class="border-t border-neutral-200 dark:border-neutral-800 hover:bg-neutral-50/60 dark:hover:bg-neutral-800/40"
            >
              <td
                v-for="col in columns"
                :key="col.key + '_' + row[idKey]"
                class="px-4 sm:px-5 py-3.5 text-sm text-neutral-800 dark:text-neutral-200"
              >
                {{ formatCell(row[col.key], col) }}
              </td>
              <td class="px-4 sm:px-5 py-3.5">
                <div class="flex items-center gap-2">
                  <button
                    @click="$emit('edit', row)"
                    class="px-2.5 py-1.5 text-white text-xs rounded-sm border border-neutral-300 dark:border-neutral-700 hover:bg-neutral-50 dark:hover:bg-neutral-800"
                  >
                    Изменить
                  </button>
                  <button
                    @click="$emit('delete', row)"
                    class="px-2.5 py-1.5 text-xs rounded-sm border border-red-300 text-red-600 hover:bg-red-50 dark:border-red-700 dark:hover:bg-red-900/30"
                  >
                    Удалить
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Пагинация -->
      <div
        v-if="filteredRows.length > pageSize"
        class="flex items-center justify-between px-4 sm:px-5 py-3 border-t border-neutral-200 dark:border-neutral-800 text-sm text-neutral-600 dark:text-neutral-400"
      >
        <div>Страница {{ page }} из {{ totalPages }}</div>
        <div class="flex items-center gap-2">
          <button
            @click="prevPage"
            :disabled="page === 1"
            class="px-3 py-1 rounded-sm border disabled:opacity-50"
          >
            Назад
          </button>
          <button
            @click="nextPage"
            :disabled="page === totalPages"
            class="px-3 py-1 rounded-sm border disabled:opacity-50"
          >
            Вперёд
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "DataTableCard",
  props: {
    title: { type: String, default: "Название таблицы" },
    rows: { type: Array, default: () => [] },
    columns: { type: Array, default: () => [] },
    idKey: { type: String, default: "id" },
    pageSize: { type: Number, default: 5 },
  },
  data() {
    return {
      sort: { key: null, dir: "asc" },
      search: "",
      page: 1,
    };
  },
  computed: {
    filteredRows() {
      if (!this.search) return this.rows;
      const q = this.search.toLowerCase();
      return this.rows.filter((row) =>
        Object.values(row).some((val) =>
          String(val).toLowerCase().includes(q)
        )
      );
    },
    sortedRows() {
      if (!this.sort.key) return this.filteredRows;
      const dir = this.sort.dir === "asc" ? 1 : -1;
      return [...this.filteredRows].sort((a, b) => {
        const va = a[this.sort.key];
        const vb = b[this.sort.key];
        return String(va).localeCompare(String(vb)) * dir;
      });
    },
    pagedRows() {
      const start = (this.page - 1) * this.pageSize;
      return this.sortedRows.slice(start, start + this.pageSize);
    },
    totalPages() {
      return Math.ceil(this.filteredRows.length / this.pageSize) || 1;
    },
  },
  methods: {
    toggleSort(key) {
      if (this.sort.key === key) {
        this.sort.dir = this.sort.dir === "asc" ? "desc" : "asc";
      } else {
        this.sort.key = key;
        this.sort.dir = "asc";
      }
    },
    nextPage() {
      if (this.page < this.totalPages) this.page++;
    },
    prevPage() {
      if (this.page > 1) this.page--;
    },
    formatCell(value, col) {
      if (col.format) return col.format(value);
      return value ?? "—";
    },
  },
  watch: {
    search() {
      this.page = 1; // сброс страницы при поиске
    },
  },
  emits: ["create", "edit", "delete"],
};
</script>
