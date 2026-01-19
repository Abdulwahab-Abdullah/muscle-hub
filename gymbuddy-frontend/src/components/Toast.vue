<template>
  <div
    :class="['toast-card', typeClass, { show: visible }]"
    @mouseenter="pauseTimer"
    @mouseleave="resumeTimer"
  >
    <strong>{{ tip.title }}</strong>
    <p>{{ tip.content }}</p>
    <button class="close-btn" @click="close" :title="t('toast.close')">
      ×
    </button>
  </div>
</template>

<script lang="ts" setup>
import { ref, computed, onMounted, onBeforeUnmount } from "vue";
import { useI18n } from "vue-i18n";

const { t } = useI18n();

const props = defineProps<{
  tip: { id: number; title: string; content: string; type: string };
}>();

const emit = defineEmits<{ (e: "close"): void }>();

const visible = ref(false);
let timer: number | null = null;

const typeColors: Record<string, string> = {
  Nutrition: "nutrition",
  Workout: "workout",
  Motivation: "motivation",
  default: "default",
};

const typeClass = computed(
  () => typeColors[props.tip.type] || typeColors.default,
);

const close = () => {
  visible.value = false;
  if (timer) clearTimeout(timer);
  setTimeout(() => emit("close"), 200);
};

const pauseTimer = () => {
  if (timer) clearTimeout(timer);
};

const resumeTimer = () => {
  timer = window.setTimeout(() => close(), 6000);
};

onMounted(() => {
  visible.value = true;
  timer = window.setTimeout(() => close(), 6000);
});

onBeforeUnmount(() => {
  if (timer) clearTimeout(timer);
});
</script>

<style scoped>
.toast-card {
  background-color: #38b6ff;
  color: white;
  padding: 1rem 1.5rem;
  border-radius: 14px;
  box-shadow: 0 15px 30px rgba(0, 0, 0, 0.4);
  min-width: 280px;
  max-width: 360px;
  font-weight: 500;
  position: relative;
  opacity: 0;
  transform: translateX(120%) translateY(-20px) rotate(-4deg) scale(0.95);
  transition:
    opacity 0.3s ease,
    transform 0.3s ease;
  cursor: pointer;
}

.toast-card.show {
  opacity: 1;
  transform: translateX(0) translateY(0) rotate(0deg) scale(1);
}

.toast-card.nutrition {
  background-color: #27ae60;
}
.toast-card.workout {
  background-color: #2980b9;
}
.toast-card.motivation {
  background-color: #e67e22;
}
.toast-card.default {
  background-color: #34495e;
}

.close-btn {
  position: absolute;
  top: 6px;
  right: 10px;
  background: transparent;
  border: none;
  color: #fff;
  font-size: 1.1rem;
  cursor: pointer;
}

/* RTL Support */
[dir="rtl"] .close-btn {
  right: auto;
  left: 10px;
}

[dir="rtl"] .toast-card {
  transform: translateX(-120%) translateY(-20px) rotate(4deg) scale(0.95);
}

[dir="rtl"] .toast-card.show {
  transform: translateX(0) translateY(0) rotate(0deg) scale(1);
}
</style>
