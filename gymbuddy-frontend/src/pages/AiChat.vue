<template>
  <div class="ai-chat-page page-custom">
    <div class="chat-container">
      <div class="chat-header">
        <div class="header-content">
          <div class="bot-info">
            <div class="bot-avatar">
              <i class="fa-solid fa-robot"></i>
            </div>
            <div class="bot-details">
              <h2>{{ $t("aiChat.title") }}</h2>
              <p class="status">
                <span class="status-dot"></span>
                {{ $t("aiChat.status") }}
              </p>
            </div>
          </div>
          <button
            v-if="messages.length > 0"
            @click="confirmClearHistory"
            class="btn-clear"
            :title="$t('aiChat.clearChat')"
          >
            <i class="fa-solid fa-trash"></i>
            {{ $t("aiChat.clearChat") }}
          </button>
        </div>
      </div>

      <div class="messages-area" ref="messagesContainer">
        <div v-if="messages.length === 0" class="welcome-section">
          <div class="welcome-icon">
            <i class="fa-solid fa-dumbbell"></i>
          </div>
          <h3>{{ $t("aiChat.welcomeTitle") }}</h3>
          <p>{{ $t("aiChat.welcomeSubtitle") }}</p>

          <div class="suggestions-grid">
            <div
              v-for="(suggestion, index) in translatedSuggestions"
              :key="index"
              class="suggestion-card"
              @click="sendSuggestion(suggestion.text)"
            >
              <div class="suggestion-icon-wrapper">
                <i :class="suggestion.icon"></i>
              </div>
              <span>{{ suggestion.text }}</span>
            </div>
          </div>
        </div>

        <div v-else class="messages-list">
          <div
            v-for="msg in messages"
            :key="msg.id"
            class="message-wrapper"
            :class="msg.sender"
          >
            <div class="message-bubble">
              <div v-if="msg.sender === 'bot'" class="bot-avatar-small">
                <i class="fa-solid fa-robot"></i>
              </div>
              <div class="message-content">
                <p v-html="formatMessage(msg.message)"></p>
                <span class="message-time">
                  {{ formatTime(msg.created_at) }}
                </span>
              </div>
            </div>
          </div>

          <div v-if="isLoading" class="message-wrapper bot">
            <div class="message-bubble loading">
              <div class="bot-avatar-small">
                <i class="fa-solid fa-robot"></i>
              </div>
              <div class="typing-indicator">
                <span></span><span></span><span></span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="input-area">
        <form @submit.prevent="sendMessage" class="input-form">
          <div class="input-wrapper">
            <textarea
              v-model="userInput"
              @keydown.enter.exact.prevent="sendMessage"
              :placeholder="$t('aiChat.inputPlaceholder')"
              rows="1"
              class="message-input"
              :disabled="isLoading"
              ref="textareaRef"
            ></textarea>
            <button
              type="submit"
              class="btn-send"
              :disabled="!userInput.trim() || isLoading"
            >
              <i v-if="!isLoading" class="fa-solid fa-paper-plane"></i>
              <i v-else class="fa-solid fa-spinner fa-spin"></i>
            </button>
          </div>
        </form>
        <p class="input-hint">
          <i class="fa-solid fa-circle-info"></i>
          {{ $t("aiChat.inputHint") }}
        </p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, nextTick, watch, computed } from "vue";
import api from "../lib/axios";
import Swal from "sweetalert2";
import { useI18n } from "vue-i18n";

interface Message {
  id: number;
  user_id: number;
  message: string;
  sender: "user" | "bot";
  created_at: string;
}

const { t } = useI18n();
const messages = ref<Message[]>([]);
const userInput = ref("");
const isLoading = ref(false);
const messagesContainer = ref<HTMLElement | null>(null);
const textareaRef = ref<HTMLTextAreaElement | null>(null);

const translatedSuggestions = computed(() => [
  { text: t("aiChat.suggestions.workoutPlan"), icon: "fa-solid fa-dumbbell" },
  { text: t("aiChat.suggestions.muscleGain"), icon: "fa-solid fa-apple-whole" },
  {
    text: t("aiChat.suggestions.weightLoss"),
    icon: "fa-solid fa-weight-scale",
  },
  { text: t("aiChat.suggestions.beginners"), icon: "fa-solid fa-running" },
]);

const fetchHistory = async () => {
  try {
    const res = await api.get("/ai-chat/history");
    if (res.data.success) {
      messages.value = res.data.history;
      await nextTick();
      scrollToBottom();
    }
  } catch (error) {
    console.error("Failed to fetch chat history:", error);
  }
};

const sendMessage = async () => {
  if (!userInput.value.trim() || isLoading.value) return;
  const messageText = userInput.value.trim();
  userInput.value = "";
  if (textareaRef.value) textareaRef.value.style.height = "auto";
  isLoading.value = true;

  try {
    const res = await api.post("/ai-chat/send", { message: messageText });
    if (res.data.success) {
      messages.value.push(res.data.user_message);
      messages.value.push(res.data.bot_reply);
      await nextTick();
      scrollToBottom();
    }
  } catch (error: any) {
    console.error("Chat error:", error);
    Swal.fire({
      icon: "error",
      title: "Failed to send message",
      text: error.response?.data?.message || "Something went wrong",
      toast: true,
      position: "top-end",
      timer: 3000,
      showConfirmButton: false,
    });
  } finally {
    isLoading.value = false;
  }
};

const sendSuggestion = (text: string) => {
  userInput.value = text;
  sendMessage();
};

const confirmClearHistory = () => {
  Swal.fire({
    title: "Clear Chat History?",
    text: "This will delete all your conversation history",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#e74c3c",
    cancelButtonColor: "#95a5a6",
    confirmButtonText: "Yes, clear it",
    cancelButtonText: "Cancel",
  }).then(async (result) => {
    if (result.isConfirmed) await clearHistory();
  });
};

const clearHistory = async () => {
  try {
    const res = await api.post("/ai-chat/clear-history");
    if (res.data.success) {
      messages.value = [];
      Swal.fire({
        icon: "success",
        title: "Chat Cleared",
        text: "Your conversation history has been cleared",
        toast: true,
        position: "top-end",
        timer: 2000,
        showConfirmButton: false,
      });
    }
  } catch (error) {
    console.error("Failed to clear history:", error);
  }
};

const scrollToBottom = () => {
  if (messagesContainer.value) {
    messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
  }
};

const formatMessage = (text: string): string => {
  return text
    .replace(/\*\*(.*?)\*\*/g, "<strong>$1</strong>")
    .replace(/\n/g, "<br>");
};

const formatTime = (timestamp: string): string => {
  const date = new Date(timestamp);
  return date.toLocaleTimeString("en-US", {
    hour: "2-digit",
    minute: "2-digit",
  });
};

watch(userInput, () => {
  if (textareaRef.value) {
    textareaRef.value.style.height = "auto";
    textareaRef.value.style.height = textareaRef.value.scrollHeight + "px";
  }
});

onMounted(() => {
  fetchHistory();
});
</script>
