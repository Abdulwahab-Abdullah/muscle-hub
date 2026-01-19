<template>
  <div :dir="currentDirection" :lang="currentLocale">
    <!-- Guest -->
    <div v-if="!userStore.isAuthenticated">
      <NavBarHomePage />
      <GoUpButton />
      <router-view />
      <FooterComponent />
    </div>

    <!-- Logged In -->
    <div v-else class="app-layout">
      <NavBarLoggedInPage @sidebar-hidden="handleSidebarHidden" />

      <div class="main-content" :class="{ 'sidebar-hidden': isSidebarHidden }">
        <ProfileBar />
        <div class="page-content">
          <router-view />
        </div>
      </div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container">
      <Toast v-if="currentTip" :tip="currentTip" @close="handleTipClose" />
    </div>
  </div>
</template>

<script lang="ts" setup>
import { ref, onMounted, watch, computed } from "vue";
import { useRoute } from "vue-router";
import { useI18n } from "vue-i18n";
import NavBarHomePage from "./components/NavBarHomePage.vue";
import NavBarLoggedInPage from "./components/NavBarLoggedInPage.vue";
import ProfileBar from "./components/ProfileBar.vue";
import GoUpButton from "./components/GoUpBtn.vue";
import FooterComponent from "./components/FooterComponent.vue";
import Toast from "./components/Toast.vue";
import { useUserStore } from "./stores/userStore";
import api from "./lib/axios";

const userStore = useUserStore();
const route = useRoute();
const { locale } = useI18n();
const isSidebarHidden = ref(false);

const currentLocale = computed(() => locale.value);
const currentDirection = computed(() =>
  locale.value === "ar" ? "rtl" : "ltr",
);

function handleSidebarHidden(hidden: boolean) {
  isSidebarHidden.value = hidden;
}

// ===== Toast System =====
const tipsQueue = ref<any[]>([]);
const currentTip = ref<any | null>(null);

const fetchTips = async () => {
  if (!userStore.isAuthenticated) return;
  try {
    const res = await api.get("/tips");
    const now = new Date();

    const shownTips: number[] = JSON.parse(
      localStorage.getItem("shownTips") || "[]",
    );

    tipsQueue.value = res.data.data
      .filter((tip: any) => {
        const tipPage = tip.page?.toString().toLowerCase();
        const routeName = route.name?.toString().toLowerCase();
        const visibleNow = new Date(tip.visible_at) <= now;
        const userMatch = !tip.user_id || tip.user_id === userStore.user?.id;
        const notShownBefore = !shownTips.includes(tip.id);

        return (
          userMatch && tipPage === routeName && visibleNow && notShownBefore
        );
      })
      .sort(
        (a: any, b: any) =>
          new Date(a.visible_at).getTime() - new Date(b.visible_at).getTime(),
      );

    showNextTip();
  } catch (err) {
    console.error("Failed to fetch tips:", err);
  }
};

const showNextTip = () => {
  if (tipsQueue.value.length === 0) {
    currentTip.value = null;
    return;
  }

  currentTip.value = tipsQueue.value.shift();
};

const handleTipClose = () => {
  if (!currentTip.value) return;

  const shownTips: number[] = JSON.parse(
    localStorage.getItem("shownTips") || "[]",
  );
  shownTips.push(currentTip.value.id);
  localStorage.setItem("shownTips", JSON.stringify(shownTips));

  currentTip.value = null;
  setTimeout(() => showNextTip(), 300);
};

watch(
  () => userStore.isAuthenticated,
  (isAuth) => {
    if (isAuth) fetchTips();
  },
);

// تحديث الاتجاه عند تغيير اللغة
watch(locale, (newLocale) => {
  document.documentElement.dir = newLocale === "ar" ? "rtl" : "ltr";
  document.documentElement.lang = newLocale;
});

onMounted(() => {
  fetchTips();

  // تطبيق اللغة المحفوظة عند التحميل
  const savedLocale = localStorage.getItem("locale") || "en";
  locale.value = savedLocale as "en" | "ar";
  document.documentElement.dir = savedLocale === "ar" ? "rtl" : "ltr";
  document.documentElement.lang = savedLocale;
});
</script>

<style scoped>
.app-layout {
  display: flex;
  min-height: 100vh;
}

.main-content {
  margin-left: 280px;
  display: flex;
  flex-direction: column;
  flex-grow: 1;
  min-height: 100vh;
  transition: margin 0.25s ease;
}

.main-content.sidebar-hidden {
  margin-left: 0;
}

/* RTL Support */
[dir="rtl"] .main-content {
  margin-left: 0;
  margin-right: 280px;
}

[dir="rtl"] .main-content.sidebar-hidden {
  margin-right: 0;
}

.page-content {
  flex-grow: 1;
  padding: 1.5rem;
}

@media (max-width: 767px) {
  .main-content {
    margin-left: 75px;
  }
  .main-content.sidebar-hidden {
    margin-left: 0;
  }

  [dir="rtl"] .main-content {
    margin-left: 0;
    margin-right: 75px;
  }

  [dir="rtl"] .main-content.sidebar-hidden {
    margin-right: 0;
  }
}

/* Toast container */
.toast-container {
  position: fixed;
  top: 20px;
  right: 20px;
  z-index: 9999;
}

[dir="rtl"] .toast-container {
  right: auto;
  left: 20px;
}
</style>
