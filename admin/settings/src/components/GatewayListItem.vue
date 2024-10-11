<template>
  <div class="pakui-gateway-list-item pakui-block">
    <div class="pakui-main">
      <h4 class="pakui-title">
        {{ gateway.title }}
      </h4>
      <p class="pakui-id">
        {{ gateway.id }}
      </p>
      <p class="pakui-description">
        {{ gateway.description }}
      </p>
    </div>
    <div class="pakui-actions">
      <button
        class="pakui-action pakui-button-icon"
        @click="$emit('configure')"
      >
        <span class="pakui-icon-gears" />
        <span class="pakui-title">{{ texts.configure }}</span>
      </button>
      <button
        class="pakui-action pakui-button-icon"
        @click="$emit('edit')"
      >
        <span class="pakui-icon-pencil" />
        <span class="pakui-title">{{ texts.edit }}</span>
      </button>
      <button
        class="pakui-action pakui-button-icon"
        @click="$emit('trash')"
      >
        <span class="pakui-icon-trash" />
        <span class="pakui-title">{{ texts.delete }}</span>
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import Gateway from '../models/Gateway';
import getConfig from '../api/getConfig';

defineProps<{ gateway: Gateway }>();

defineEmits<{
    (e: 'configure'): void
    (e: 'edit'): void
    (e: 'trash'): void
}>();

const { texts } = getConfig();
</script>

<style scoped>
.pakui-title {
  color: inherit;
  font-size: 18px;
  font-weight: 900;
  margin-bottom: 0;
  margin-top: 0;
  overflow-wrap: anywhere;
}

.pakui-id {
  background: rgba(0, 0, 0, 0.1);
  border-radius: 4px;
  color: inherit;
  display: inline-block;
  font-size: 10px;
  font-weight: 600;
  margin-bottom: 0;
  margin-top: 6px;
  padding: 4px 6px;
}

.pakui-description {
  color: inherit;
  margin: 15px 0 0 0;
  overflow-wrap: anywhere;
  line-height: 1.6;
}

.pakui-actions {
  display: flex;
}

.pakui-action {
  background: transparent;
  border-radius: 4px;
  border: none;
  outline: none;
}

.pakui-action [class*='pakui-icon'] {
  height: 24px;
  width: 24px;
}

.pakui-action .pakui-title {
  display: block;
  font-size: 12px;
  font-weight: bold;
  margin-top: 4px;
  text-align: center;
}

@media (min-width: 768px) {
  .pakui-main {
    flex: 1;
  }

  .pakui-gateway-list-item {
    display: flex;
  }

  .pakui-action:not(:last-child) {
    margin-right: var(--pakui-gutter);
  }
}

@media (max-width: 767px) {
  .pakui-actions {
    border-top: 1px solid rgba(0, 0, 0, 0.06);
    margin-top: var(--pakui-gutter);
    padding-top: var(--pakui-gutter);
  }

  .pakui-action {
    flex: 1;
    width: 100%;
  }
}

.pakui-gateway-list-item {
  animation-fill-mode: forwards;
  transform-origin: center center;
}

.pakui-gateway-list-item:nth-child(odd) {
  animation: animation-bounce 0.4s ease-in;
}

.pakui-gateway-list-item:nth-child(even) {
  animation: animation-bounce-alt 0.4s ease-in;
}

</style>
