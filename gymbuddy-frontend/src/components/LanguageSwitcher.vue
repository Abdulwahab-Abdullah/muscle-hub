<template>
  <div class="language-switcher">
    <button
      @click="toggleLanguage"
      class="btn btn-sm btn-outline-primary"
      :title="currentLocale === 'en' ? 'العربية' : 'English'"
    >
      <i class="fas fa-language me-1"></i>
      {{ currentLocale === "en" ? "ع" : "EN" }}
    </button>
  </div>
</template>

<script setup lang="ts">
import { computed } from "vue";
import { useI18n } from "vue-i18n";
import { useRouter, useRoute } from "vue-router";

const { locale } = useI18n();
const router = useRouter();
const route = useRoute();

const currentLocale = computed(() => locale.value);

const toggleLanguage = () => {
  const newLocale = locale.value === "en" ? "ar" : "en";
  locale.value = newLocale;
  localStorage.setItem("locale", newLocale);

  // تحديث اتجاه الصفحة
  document.documentElement.dir = newLocale === "ar" ? "rtl" : "ltr";
  document.documentElement.lang = newLocale;

  // تحديث المسار
  const currentPath = route.path;
  const newPath = currentPath.replace(/^\/(en|ar)/, `/${newLocale}`);

  if (currentPath !== newPath) {
    router.push(newPath);
  }
};
</script>

<style scoped>
.language-switcher {
  display: inline-block;
}

.language-switcher button {
  min-width: 60px;
  font-weight: 600;
  transition: all 0.3s ease;
}

.language-switcher button:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}
</style>
