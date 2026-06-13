export default defineNuxtRouteMiddleware((to, from) => {
  const { isAuthenticated } = useSanctumAuth();

  if (to.path.startsWith("/admin") && !isAuthenticated.value) {
    return navigateTo("/login"); // Lempar ke halaman login
  }
});
