<template>
  <div class="layout d-flex">
    <!-- Sidebar -->
    <nav
      class="sidebar"
      :class="{
        'mobile-mode': isMobile,
        collapsed: isCollapsed,
        hidden: isHidden,
      }"
    >
      <div class="sidebar-content">
        <!-- Hide Button -->
        <div
          class="hide-btn"
          @click="toggleHideSidebar"
          :title="t('sidebar.hideSidebar')"
        >
          <i
            class="fa-solid"
            :class="[locale == 'ar' ? 'fa-angles-right' : 'fa-angles-left']"
          ></i>
        </div>

        <!-- Logo -->
        <div class="logo-section">
          <i class="fa-solid fa-dumbbell logo-icon"></i>
          <span v-if="!isMobile && !isCollapsed" class="logo-text">
            Muscle <span class="logo-highlight">Hub</span>
          </span>
        </div>

        <!-- Main Menu -->
        <div class="menu-wrapper">
          <h6 v-if="!isMobile && !isCollapsed" class="menu-title">
            {{ t("sidebar.mainMenu") }}
          </h6>

          <ul class="nav-menu">
            <li v-for="item in menuItems" :key="item.path">
              <router-link
                :to="`/${locale}${item.path}`"
                class="nav-item"
                exact-active-class="active"
                :title="item.label"
              >
                <i class="nav-icon" :class="item.icon"></i>
                <span v-if="!isMobile && !isCollapsed" class="nav-label">{{
                  item.label
                }}</span>
              </router-link>
            </li>
          </ul>
        </div>

        <!-- Account Section -->
        <div class="account-section" v-if="userStore.isAuthenticated">
          <h6 v-if="!isMobile && !isCollapsed" class="menu-title">
            {{ t("sidebar.account") }}
          </h6>

          <ul class="nav-menu account-menu">
            <!-- Profile -->
            <li>
              <router-link
                :to="`/${locale}/profile`"
                class="nav-item"
                :class="{ active: $route.path.includes('/profile') }"
                :title="t('sidebar.myProfile')"
              >
                <i class="nav-icon fa-solid fa-user"></i>
                <span v-if="!isMobile && !isCollapsed">{{
                  t("sidebar.myProfile")
                }}</span>
              </router-link>
            </li>

            <!-- Settings -->
            <li>
              <router-link
                :to="`/${locale}/settings`"
                class="nav-item"
                :class="{ active: $route.path.includes('/settings') }"
                :title="t('sidebar.settings')"
              >
                <i class="nav-icon fa-solid fa-gear"></i>
                <span v-if="!isMobile && !isCollapsed">{{
                  t("sidebar.settings")
                }}</span>
              </router-link>
            </li>

            <!-- Logout -->
            <li>
              <button
                class="nav-item logout-btn"
                @click="handleLogout"
                :title="t('sidebar.logout')"
              >
                <i class="nav-icon fa-solid fa-right-from-bracket"></i>
                <span v-if="!isMobile && !isCollapsed">{{
                  t("sidebar.logout")
                }}</span>
              </button>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <!-- Show Sidebar Button (when hidden) -->
    <button
      v-if="isHidden"
      class="show-sidebar-btn"
      @click="toggleHideSidebar"
      :title="t('sidebar.showSidebar')"
    >
      <i
        class="fa-solid"
        :class="[locale == 'ar' ? 'fa-angles-left' : 'fa-angles-right']"
      ></i>
    </button>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted, watch } from "vue";
import { useI18n } from "vue-i18n";
import { useUserStore } from "../stores/userStore";
import api from "../lib/axios";
import Swal from "sweetalert2";
import router from "../router";

const { t, locale } = useI18n();
const userStore = useUserStore();
const isMobile = ref(false);
const isCollapsed = ref(false);
const isHidden = ref(false);

const emit = defineEmits<{
  (e: "sidebar-hidden", value: boolean): void;
}>();

watch(isHidden, (newValue) => {
  emit("sidebar-hidden", newValue);
});

const menuItems = computed(() => [
  {
    path: "/dashboard",
    label: t("sidebar.dashboard"),
    icon: "fa-solid fa-chart-line",
  },
  {
    path: "/exercise",
    label: t("sidebar.exercise"),
    icon: "fa-solid fa-dumbbell",
  },
  {
    path: "/my-workouts",
    label: t("sidebar.myWorkouts"),
    icon: "fa-solid fa-calendar-days",
  },
  {
    path: "/nutrition",
    label: t("sidebar.nutrition"),
    icon: "fa-solid fa-apple-whole",
  },
  {
    path: "/calculate-calories",
    label: t("sidebar.calories"),
    icon: "fa-solid fa-fire",
  },
  {
    path: "/progress",
    label: t("sidebar.progress"),
    icon: "fa-solid fa-chart-simple",
  },
  {
    path: "/ai-chat",
    label: t("sidebar.aiAssistant"),
    icon: "fa-solid fa-robot",
  },
]);

function handleResize() {
  isMobile.value = window.innerWidth < 768;
}

function toggleHideSidebar() {
  isHidden.value = !isHidden.value;
}

async function handleLogout() {
  try {
    await api.post("/auth/logout");
    userStore.logout();
    Swal.fire({
      icon: "success",
      title: t("sidebar.loggedOutSuccess"),
      timer: 2000,
      showConfirmButton: false,
    });
    router.push(`/${locale.value}/auth`);
  } catch (error) {
    Swal.fire({
      icon: "error",
      title: t("sidebar.logoutFailed"),
    });
  }
}

onMounted(() => {
  handleResize();
  window.addEventListener("resize", handleResize);
});

onUnmounted(() => {
  window.removeEventListener("resize", handleResize);
});
</script>
