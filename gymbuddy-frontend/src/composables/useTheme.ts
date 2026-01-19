// composables/useTheme.ts
import { ref, watch } from "vue";

export function useTheme() {
  const theme = ref(localStorage.getItem("theme") || "auto");

  const applyTheme = () => {
    document.documentElement.classList.remove("dark-mode", "light-mode");

    if (theme.value === "dark") {
      document.documentElement.classList.add("dark-mode");
    } else if (theme.value === "light") {
      document.documentElement.classList.add("light-mode");
    } else {
      // Auto mode
      const prefersDark = window.matchMedia(
        "(prefers-color-scheme: dark)"
      ).matches;
      document.documentElement.classList.add(
        prefersDark ? "dark-mode" : "light-mode"
      );
    }
  };

  watch(theme, (newTheme) => {
    localStorage.setItem("theme", newTheme);
    applyTheme();
  });

  return { theme, applyTheme };
}
