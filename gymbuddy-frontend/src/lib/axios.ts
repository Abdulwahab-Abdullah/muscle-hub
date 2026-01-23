import axios from "axios";

const api = axios.create({
  baseURL: "http://localhost:8000/api",
  withCredentials: true,
  headers: {
    "Content-Type": "application/json",
    Accept: "application/json",
  },
});

// إضافة التوكن واللغة في كل request
api.interceptors.request.use(
  (config) => {
    // 🔥 استثني routes الي ما تحتاج توكن
    const publicRoutes = ["/auth/login", "/auth/register"];
    const isPublicRoute = publicRoutes.some((route) =>
      config.url?.includes(route),
    );

    if (isPublicRoute) {
      // console.log("📢 Public route - no token needed");
    } else {
      // console.log("🍪 All cookies:", document.cookie);

      // جرب الكوكي أول
      let token: string | null =
        document.cookie
          .split("; ")
          .find((row) => row.startsWith("auth_token="))
          ?.split("=")[1] ?? null;

      // إذا ما لقى في الكوكي، جرب localStorage
      if (!token) {
        token = localStorage.getItem("auth_token");
        // console.log("🔑 Token from localStorage:", token);
      } else {
        // console.log("🔑 Token from cookie:", token);
      }

      if (token) {
        config.headers.Authorization = `Bearer ${token}`;
        // console.log("✅ Authorization header set");
      } else {
        console.warn("⚠️ No token found!");
      }
    }

    // ✅ إضافة اللغة من localStorage
    const currentLocale = localStorage.getItem("locale") || "en";
    config.headers["Accept-Language"] = currentLocale;
    // console.log("🌍 Language set to:", currentLocale);

    return config;
  },
  (error) => {
    return Promise.reject(error);
  },
);

// التعامل مع 401
api.interceptors.response.use(
  (response) => response,
  (error) => {
    console.error(
      "❌ Response error:",
      error.response?.status,
      error.response?.data,
    );

    if (error.response?.status === 401) {
      console.warn("🚫 Unauthorized! Redirecting to login...");
      localStorage.removeItem("user");
      localStorage.removeItem("auth_token");
      window.location.href = "/auth";
    }
    return Promise.reject(error);
  },
);

export default api;
