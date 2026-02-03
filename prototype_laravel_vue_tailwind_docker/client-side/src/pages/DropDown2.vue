<template>
  <div class="p-4">
    <DataTable
      :value="products"
      paginator
      :rows="5"
      :rowsPerPageOptions="[5, 10, 20]"
      tableStyle="min-width: 50rem"
    >
      <Column field="id" header="ID" sortable></Column>
      <Column field="name" header="Name" sortable></Column>
      <Column field="price" header="Price" sortable>
        <template #body="{ data }">
          ${{ data.price }}
        </template>
      </Column>
      <Column field="category" header="Category"></Column>
      <Column header="Actions">
        <template #body>
          <Button icon="pi pi-pencil" class="p-button-rounded p-button-success p-mr-2" />
          <Button icon="pi pi-trash" class="p-button-rounded p-button-danger" />
        </template>
      </Column>
    </DataTable>
  </div>
</template>

<script>
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Button from 'primevue/button'

export default {
  components: {
    DataTable,
    Column,
    Button
  },
  data() {
    return {
      products: [
        { id: '1', name: 'Product 1', price: 100, category: 'Electronics' },
        { id: '2', name: 'Product 2', price: 200, category: 'Books' },
        { id: '3', name: 'Product 3', price: 150, category: 'Electronics' },
        { id: '4', name: 'Product 4', price: 300, category: 'Clothing' },
        { id: '5', name: 'Product 5', price: 250, category: 'Books' }
      ]
    }
  }
}
</script>

<style>
@import 'primevue/resources/themes/saga-blue/theme.css';
@import 'primevue/resources/primevue.min.css';
@import 'primeicons/primeicons.css';
</style>
