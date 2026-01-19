// stores/userStore.ts
import { defineStore } from "pinia";

interface User {
  id: number;
  name: string;
  email: string;
  email_verified_at?: string | null;
  created_at?: string;
  updated_at?: string;
  [key: string]: any; // للسماح بخصائص إضافية
}

export const useUserStore = defineStore("user", {
  state: () => ({
    user: null as null | User,
  }),

  getters: {
    // ✅ التحقق من تسجيل الدخول
    isAuthenticated: (state) => !!state.user,

    // ✅ الحصول على اسم المستخدم
    userName: (state) => state.user?.name || "Guest",

    // ✅ الحصول على البريد الإلكتروني
    userEmail: (state) => state.user?.email || "",
  },

  actions: {
    // ✅ تعيين بيانات المستخدم وحفظها في localStorage
    setUser(data: User) {
      this.user = data;
      // حفظ في localStorage
      localStorage.setItem("user", JSON.stringify(data));
    },

    // ✅ تحميل بيانات المستخدم من localStorage عند بدء التطبيق
    loadUser() {
      const savedUser = localStorage.getItem("user");
      if (savedUser) {
        try {
          this.user = JSON.parse(savedUser);
        } catch (error) {
          console.error("Failed to parse saved user:", error);
          localStorage.removeItem("user");
        }
      }
    },

    // ✅ تسجيل الخروج وحذف البيانات
    logout() {
      this.user = null;
      localStorage.removeItem("user");
    },

    // ✅ مسح بيانات المستخدم (alias for logout)
    clearUser() {
      this.logout();
    },
  },
});
