import js from "@eslint/js";
import tseslint from "@typescript-eslint/eslint-plugin";
import tsparser from "@typescript-eslint/parser";
import prettier from "eslint-config-prettier";
import prettierPlugin from "eslint-plugin-prettier";
import globals from "globals";

export default [
  js.configs.recommended,
  {
    files: ["**/*.ts", "**/*.tsx"],
    languageOptions: {
      parser: tsparser,
      parserOptions: {
        project: "./tsconfig.json",
      },
      ecmaVersion: "latest",
      sourceType: "module",
      globals: {
        ...globals.browser,
      }
    },
    plugins: {
      "@typescript-eslint": tseslint,
      prettier: prettierPlugin
    },
    rules: {
      ...tseslint.configs.recommended.rules,
      "prettier/prettier": "error",
      "no-console": "off",
      "@typescript-eslint/no-unused-vars": "warn"
    }
  },
  prettier
];