<template>
  <div>
    <div class="flex flex-col gap-2 text-[14px]">
      <div class="p-1 pb-0 bg-white text-black w-full flex justify-between gap-1 border-b border-gray-300">
        <!-- Активный таб -->
        <RouterLink to="/users" class=" rounded-t-md py-4 px-6 cursor-pointer justify-center items-center flex flex-1 relative"
        :class="$route.path === '/users' ? 'bg-deepblue text-white shadow-lg' : 'bg-gray-100 hover:bg-fog text-midnight'">
            <AppIcon name="Users" size="16" class="mr-2" />
            <span class="font-semibold text-sm">Users</span>
            <!-- Индикатор активного таба -->
        </RouterLink>
        <!-- Неактивные табы -->
        <RouterLink to="/users/roles" class="flex-1 flex justify-center items-center rounded-t-md py-4 px-6 cursor-pointer transition-colors duration-200"
        :class="$route.path === '/users/roles' ? 'bg-deepblue text-white shadow-lg' : 'bg-gray-100 hover:bg-fog text-midnight'">
            <AppIcon name="Shield" size="16" class="mr-2" />
            <span class="font-medium text-sm">Roles</span>
        </RouterLink>
        <RouterLink to="/users/permissions" class="flex-1 flex justify-center items-center rounded-t-md py-4 px-6 cursor-pointer transition-colors duration-200"
        :class="$route.path === '/users/permissions' ? 'bg-deepblue text-white shadow-lg' : 'bg-gray-100 hover:bg-fog text-midnight'">
            <AppIcon name="Key" size="16" class="mr-2" />
            <span class="font-medium text-sm">Permissions</span>
        </RouterLink>
      </div>

      <router-view/>

    </div>
  </div>
</template>

<script>

export default{
  name: "UsersLayout",
}
</script>

<style scoped>


</style>
