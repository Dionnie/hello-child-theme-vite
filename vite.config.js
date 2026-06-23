import react from "@vitejs/plugin-react-swc";
import laravel from "laravel-vite-plugin";
import { defineConfig } from "vite";
export default defineConfig({
  server: {
    hmr: {
      protocol: "ws",
      host: "localhost",
    },
  },
  plugins: [
    laravel({
      input: ["src/js/theme.js", "src/css/theme.css"],
      refresh: [".//*.php"],
    }),
    react(),
  ],
  base:
    process.env.NODE_ENV === "production"
      ? "/wp-content/themes/dionnie/public/build/"
      : "/",
});
