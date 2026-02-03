<template>
    <div>
        <h2 class="text-lg font-semibold text-midnight">Welcome to Dashboard</h2>
        <p class="text-deepblue mt-2">Here goes your content...  test</p>
        <div class="flex gap-4">
        <AppIcon name="Home" />
        <AppIcon name="User" size="32" color="red" />
        <AppIcon name="Settings" size="40" color="#4f46e5" />
        </div>
    </div>
</template>

<script>
export default {
  name: "MainPage",
};
</script>