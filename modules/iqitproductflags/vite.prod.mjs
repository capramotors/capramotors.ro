import baseConfig from "./vite.base.mjs";
import { defineConfig } from "vite";
import eslintPlugin from "vite-plugin-eslint";
import stylelintPlugin from "vite-plugin-stylelint";

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
  plugins: [eslintPlugin(), stylelintPlugin()],
});
