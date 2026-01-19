<template>
  <div class="profile-bar">
    <!-- Left part: Page Title -->
    <h4 class="page-title">
      {{ pageTitle }}
    </h4>

    <!-- Right part: Icons + User Info -->
    <div class="right-section">
      <!-- Language Toggle -->
      <div class="language-wrapper">
        <LanguageSwitcher />
      </div>

      <!-- Dark/Light Toggle -->
      <div class="dark-mode-wrapper">
        <DarkModeToggle />
      </div>

      <!-- User Info -->
      <div class="user-info">
        <div class="image">
          <img v-if="userPhoto" :src="userPhoto" alt="User Image" />
          <div v-else class="photo-placeholder">
            {{ userInitials }}
          </div>
        </div>
        <div class="details">
          <h5>{{ userStore.userName }}</h5>
          <p>{{ userStore.userEmail }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { computed, ref, onMounted, onUnmounted, watch } from "vue";
import { useRoute } from "vue-router";
import { useUserStore } from "../stores/userStore";
import DarkModeToggle from "./DarkModeToggle.vue";
import LanguageSwitcher from "./LanguageSwitcher.vue";

const userStore = useUserStore();
const route = useRoute();
const pageTitle = computed(() => route.meta.title || "Dashboard");

// User Photo
const userPhoto = ref<string | null>(null);

// User Initials for placeholder
const userInitials = computed(() => {
  const name = userStore.userName || "User";
  return name
    .split(" ")
    .map((n: string) => n[0])
    .join("")
    .toUpperCase()
    .slice(0, 2);
});

// Load photo from localStorage
const loadUserPhoto = () => {
  const saved = localStorage.getItem("profilePhoto");
  userPhoto.value = saved || null;
};

// Handle custom event for same-tab updates
const handlePhotoUpdate = (event: CustomEvent) => {
  if (event.detail?.newPhotoUrl !== undefined) {
    userPhoto.value = event.detail.newPhotoUrl;
  } else {
    loadUserPhoto();
  }
};

// Listen for storage changes (other tabs)
const handleStorageChange = (e: StorageEvent) => {
  if (e.key === "profilePhoto") {
    loadUserPhoto();
  }
};

onMounted(() => {
  loadUserPhoto();

  // تبويب نفس الصفحة
  window.addEventListener(
    "profilePhotoUpdated",
    handlePhotoUpdate as EventListener,
  );

  // تبويب آخر
  window.addEventListener("storage", handleStorageChange);
});

onUnmounted(() => {
  window.removeEventListener(
    "profilePhotoUpdated",
    handlePhotoUpdate as EventListener,
  );
  window.removeEventListener("storage", handleStorageChange);
});

// Watch user name changes to update initials if needed
watch(
  () => userStore.userName,
  () => {
    loadUserPhoto();
  },
);
</script>

<style scoped lang="scss">
.profile-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 1.5rem;
  background-color: var(--background-color);
  border-bottom: 1px solid var(--border-color);
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.page-title {
  margin: 0;
  font-size: 1.5rem;
  font-weight: 600;
  color: var(--text-color);
}

.right-section {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.language-wrapper,
.dark-mode-wrapper {
  display: flex;
  align-items: center;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.5rem 1rem;
  background-color: var(--card-background);
  border-radius: 12px;
  transition: all 0.3s ease;

  &:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  }

  .image {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    overflow: hidden;
    border: 2px solid var(--primary-color);

    img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
  }

  .photo-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(
      135deg,
      var(--primary-color),
      var(--secondary-color)
    );
    color: white;
    font-weight: bold;
    font-size: 1.1rem;
  }

  .details {
    display: flex;
    flex-direction: column;
    gap: 0.2rem;

    h5 {
      margin: 0;
      font-size: 1rem;
      font-weight: 600;
      color: var(--text-color);
    }

    p {
      margin: 0;
      font-size: 0.85rem;
      color: var(--text-muted);
    }
  }
}

/* RTL Support */
[dir="rtl"] .right-section {
  flex-direction: row-reverse;
}

/* Mobile adjustments */
@media (max-width: 768px) {
  .profile-bar {
    flex-direction: column;
    gap: 1rem;
    padding: 1rem;
  }

  .page-title {
    font-size: 1.25rem;
  }

  .right-section {
    width: 100%;
    justify-content: space-between;
  }

  .user-info {
    .details h5 {
      font-size: 0.9rem;
    }

    .details p {
      font-size: 0.75rem;
    }
  }
}
</style>
