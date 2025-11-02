import baseConfig from "./vite.base.mjs";
import { defineConfig } from "vite";

export default defineConfig({
  ...baseConfig,
  build: {
    ...baseConfig.build,
    sourcemap: "inline", 
    minify: "esbuild", // ✅ Faster dev build,
    esbuildOptions: {
      minifyIdentifiers: false, // 🚀 Nie zmienia nazw zmiennych (szybsze)
      minifyWhitespace: true, // 🚀 Usuwa zbędne spacje
    },
  },
  server: {
    fs: {
      strict: false
    }
  },
  css: {
    devSourcemap: true, // ✅ Wymusza source maps dla CSS
    preprocessorOptions: {
      scss: {
        sourceMap: true, // ✅ Kluczowe dla `sass-loader`
      },
    },
  },
});
