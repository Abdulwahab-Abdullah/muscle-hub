<template>
  <button @click="toggleMode" class="dark-mode-btn" :title="modeTitle">
    <span v-if="mode === 'dark'">🌙</span>
    <span v-else-if="mode === 'light'">☀️</span>
    <span v-else>🌓</span>
  </button>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from "vue";

const mode = ref("auto"); // auto, light, dark

onMounted(() => {
  const saved = localStorage.getItem("theme-mode");
  if (saved) mode.value = saved;
  applyMode();
});

const modeTitle = computed(() => {
  if (mode.value === "dark") return "Dark Mode";
  if (mode.value === "light") return "Light Mode";
  return "Auto Mode";
});

const applyMode = () => {
  const root = document.documentElement;
  root.classList.remove("dark-mode", "light-mode");

  if (mode.value === "dark") {
    root.classList.add("dark-mode");
  } else if (mode.value === "auto") {
    if (window.matchMedia("(prefers-color-scheme: dark)").matches) {
      root.classList.add("dark-mode");
    } else {
      root.classList.add("light-mode");
    }
  } else {
    root.classList.add("light-mode");
  }
};

const toggleMode = () => {
  if (mode.value === "auto") mode.value = "light";
  else if (mode.value === "light") mode.value = "dark";
  else mode.value = "auto";

  localStorage.setItem("theme-mode", mode.value);
  applyMode();
};
</script>

<style scoped lang="scss">
.dark-mode-btn {
  background: var(--primary-color);
  color: #fff;
  border: none;
  border-radius: 8px;
  padding: 6px 12px;
  cursor: pointer;
  font-weight: bold;
  font-size: 1rem;
  transition: all 0.3s ease;

  &:hover {
    transform: scale(1.05);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
  }
}
</style>
