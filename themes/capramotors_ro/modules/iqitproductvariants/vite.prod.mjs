import baseConfig from "./vite.base.mjs";
import { defineConfig } from "vite";

export default defineConfig({
  ...baseConfig,
  build: {
    ...baseConfig.build,
    minify: "terser", 
    terserOptions: {
      format: {
        comments: false,
      },
      mangle: {
        reserved: ["$"],
      },
    },
  },
});
