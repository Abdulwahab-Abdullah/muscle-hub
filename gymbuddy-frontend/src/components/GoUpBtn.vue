<template>
  <div class="go-up" v-show="visible">
    <a href="#top"><i class="fa-solid fa-circle-up fa-2x"></i></a>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from "vue";

const visible = ref(false);

function handleScroll() {
  visible.value = window.scrollY > 50; // يظهر بعد النزول 50px فقط للتجربة
}

onMounted(() => {
  window.addEventListener("scroll", handleScroll);
  handleScroll(); // تحديث الظهور عند التحميل
});

onUnmounted(() => window.removeEventListener("scroll", handleScroll));
</script>

<style scoped>
.go-up {
  position: fixed;
  bottom: 20px;
  right: 20px;
  z-index: 10000;
  cursor: pointer;
}

.go-up a {
  display: flex;
  justify-content: center;
  align-items: center;
  width: 50px;
  height: 50px;
  color: white;
  border-radius: 50%;
  text-decoration: none;
}
</style>
