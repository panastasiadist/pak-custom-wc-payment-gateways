import js from '@eslint/js';
import eslintPluginVue from 'eslint-plugin-vue';
import ts from 'typescript-eslint';
import stylisticJs from '@stylistic/eslint-plugin';

const additionalRules = {
  plugins: {
    '@stylistic/js': stylisticJs,
  },
  rules: {
    '@stylistic/js/arrow-parens': ['error', 'always'],
    '@stylistic/js/semi': ['error', 'always'],
    'camelcase': ['error'],
    'dot-notation': ['error'],
    'id-length': ['error'],
    'no-use-before-define': ['error'],
    'prefer-destructuring': ['error'],
    'sort-imports': ['error'],
    'sort-keys': ['error'],
    'sort-vars': ['error'],
  },
};

export default ts.config(
  js.configs.recommended,
  ...ts.configs.recommended,
  ...eslintPluginVue.configs['flat/recommended'],
  {
    files: ['src/*.vue', 'src/**/*.vue'],
    languageOptions: {
      parserOptions: {
        parser: '@typescript-eslint/parser',
      },
    },
    ...additionalRules,
  },
  {
    files: ['src/*.{js,ts}', 'src/**/*.{js,ts}'],
    ...additionalRules,
  },
);
