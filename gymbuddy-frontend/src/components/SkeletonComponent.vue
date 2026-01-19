<template>
  <div class="skeleton-wrapper">
    <!-- Dashboard Stats -->
    <div v-if="type === 'dashboard-stats'" class="skeleton-stats">
      <div class="skeleton-card" v-for="i in 4" :key="i">
        <div class="skeleton-line skeleton-icon"></div>
        <div class="skeleton-line skeleton-title"></div>
        <div class="skeleton-line skeleton-value"></div>
      </div>
    </div>

    <!-- Chart -->
    <div v-else-if="type === 'chart'" class="skeleton-chart">
      <div class="skeleton-line skeleton-chart-title"></div>
      <div class="skeleton-chart-body"></div>
    </div>

    <!-- Table -->
    <div v-else-if="type === 'table'" class="skeleton-table">
      <div class="skeleton-line skeleton-table-header"></div>
      <div
        class="skeleton-line skeleton-table-row"
        v-for="i in rows"
        :key="i"
      ></div>
    </div>

    <!-- Simple Card -->
    <div v-else-if="type === 'card'" class="skeleton-card-simple">
      <div class="skeleton-line skeleton-card-title"></div>
      <div class="skeleton-line skeleton-card-text"></div>
      <div class="skeleton-line skeleton-card-text short"></div>
    </div>

    <!-- Exercise Cards -->
    <div v-else-if="type === 'exercise-card'" class="exercise-skeleton-grid">
      <div v-for="i in 6" :key="i" class="exercise-skeleton-card">
        <div class="skeleton-img"></div>
        <div class="skeleton-line skeleton-title"></div>
        <div class="skeleton-line skeleton-text"></div>
        <div class="skeleton-line skeleton-text short"></div>
        <div class="skeleton-btn"></div>
      </div>
    </div>

    <!-- Default -->
    <div v-else class="skeleton-default">
      <div class="skeleton-line" v-for="i in 3" :key="i"></div>
    </div>
  </div>
</template>

<script lang="ts" setup>
interface Props {
  type?:
    | "dashboard-stats"
    | "chart"
    | "table"
    | "card"
    | "exercise-card"
    | "default";

  rows?: number;
}

withDefaults(defineProps<Props>(), {
  type: "default",
  rows: 5,
});
</script>
