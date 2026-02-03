<template>
  <div class="server-error-page">
    <div class="error-container">
      <i class="fas fa-server"></i>
      <h1>Сервер недоступен</h1>
      <p>Пожалуйста, попробуйте позже или обратитесь в поддержку</p>
      <button @click="retry">Попробовать снова</button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ServerErrorPage',
  methods: {
    retry() {
      this.$router.push('/'); // Перезагружаем текущую страницу
    }
  }
}
</script>

<style scoped>
.server-error-page {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background-color: #f5f5f5;
}

.error-container {
  text-align: center;
  padding: 2rem;
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.error-container i {
  font-size: 4rem;
  color: #e74c3c;
  margin-bottom: 1rem;
}

.error-container h1 {
  color: #333;
  margin-bottom: 1rem;
}

.error-container p {
  color: #666;
  margin-bottom: 1.5rem;
}

.error-container button {
  padding: 0.75rem 1.5rem;
  background-color: #3498db;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 1rem;
}

.error-container button:hover {
  background-color: #2980b9;
}
</style>
