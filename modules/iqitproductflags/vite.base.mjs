import { defineConfig } from "vite";
import fs from "fs";
import path from "path";

function getEntries(dir, folder, suffix = "", allowedExtensions = []) {
  return fs.readdirSync(dir)
    .filter(file => {
      const ext = path.extname(file);
      return allowedExtensions.includes(ext) && fs.statSync(path.join(dir, file)).isFile();
    })
    .reduce((entries, file) => {
      const baseName = path.basename(file, path.extname(file));
      entries[`${folder}/${baseName}${suffix}`] = path.resolve(dir, file);
      return entries;
    }, {});
}


const adminEntries = {
  ...getEntries("./_dev/back/ts", "admin", "_script", [".ts"]),
  ...getEntries("./_dev/back/scss", "admin", "_style", [".scss"]),
};

const frontEntries = {
  ...getEntries("./_dev/front/ts", "front", "_script", [".ts"]),
  ...getEntries("./_dev/front/scss", "front", "_style", [".scss"]),
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
});
