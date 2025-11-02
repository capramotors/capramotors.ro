import { defineConfig } from "vite";
import eslintPlugin from "vite-plugin-eslint";
import stylelintPlugin from "vite-plugin-stylelint";
import fs from "fs";
import path from "path";

function getEntries(dir, folder) {
  const files = fs.readdirSync(dir)
    .filter(file => fs.statSync(path.join(dir, file)).isFile()) // Filtruje tylko pliki, pomija katalogi
    .reduce((entries, file) => {
      const baseName = path.basename(file, path.extname(file));
      entries[`${folder}/${baseName}`] = path.resolve(dir, file);
      return entries;
    }, {});

  return files;
}

const adminEntries = {
  ...getEntries("./_dev/back/ts", "admin"),
  ...getEntries("./_dev/back/scss", "admin"),
};

const frontEntries = {
  ...getEntries("./_dev/front/ts", "front"),
  ...getEntries("./_dev/front/scss", "front"),
};


export default defineConfig({
  root: "./_dev",
  build: {
    outDir: "../views/public",
    assetsInlineLimit: 0,
    cssCodeSplit: true,
    sourcemap: false,
    rollupOptions: {
      input: { ...adminEntries, ...frontEntries },
      output: {
        entryFileNames: "[name].js",
        assetFileNames: "[name].[ext]",
      },
    },
  },
  resolve: {
    extensions: [".ts", ".js", ".scss"],
  },
  css: {
    postcss: "./postcss.config.mjs",
    preprocessorOptions: {
      scss: {
      //  sourceMap: true, // ✅ Musi być wymuszone
      },
    },
  },
  plugins: [eslintPlugin(), stylelintPlugin()],
});
