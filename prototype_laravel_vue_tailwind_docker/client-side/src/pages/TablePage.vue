<template>
    <div>
      <DataTableCard
        title="Кассы"
        :rows="items"
        :columns="cols"
        idKey="id"
        :pageSize="3"
        @create="onCreate"
        @edit="onEdit"
        @delete="onDelete"
      />
    </div>
</template>

<script>
import DataTableCard from "@/components/tables/DataTableCard.vue";

export default {
  components: { DataTableCard },
  data() {
    return {
      cols: [
        { key: "name", label: "Название" },
        { key: "currency", label: "Валюта" },
        { key: "balance", label: "Баланс" },
        { key: "updated_at", label: "Обновлено" },
      ],
      items: [
        { id: 1, name: "Основная касса", currency: "USD", balance: 15230.5, updated_at: "2025-08-30" },
        { id: 2, name: "Касса EUR", currency: "EUR", balance: 9200, updated_at: "2025-08-29" },
        { id: 3, name: "Касса RUB", currency: "RUB", balance: 500000, updated_at: "2025-08-25" },
        { id: 4, name: "Касса TRY", currency: "TRY", balance: 12000, updated_at: "2025-08-22" },
      ],
    };
  },
  methods: {
    onCreate() { alert("Создать запись"); },
    onEdit(row) { alert("Редактировать: " + row.name); },
    onDelete(row) { alert("Удалить: " + row.name); },
  },
};
</script>
