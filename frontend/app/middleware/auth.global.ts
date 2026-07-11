interface AuthUser {
  id: number;
  name: string;
  email: string;
  role: string;
}

export default defineNuxtRouteMiddleware((to, from) => {
  if (!to.path.startsWith("/admin")) return;

  const { user, isAuthenticated } = useSanctumAuth<AuthUser>();

  if (!isAuthenticated.value) {
    return navigateTo("/login"); // Lempar ke halaman login
  }

  // Login saja tidak cukup - hanya role admin yang boleh masuk panel admin.
  if (user.value?.role !== "admin") {
    return navigateTo("/");
  }
});
