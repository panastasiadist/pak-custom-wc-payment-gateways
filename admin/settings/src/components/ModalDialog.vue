<template>
  <Overlay
    class="pakui-modal-dialog"
    @keyup.esc="$emit('close')"
  >
    <div class="pakui-body">
      <header class="pakui-header">
        <div class="pakui-header-content">
          <slot name="header" />
        </div>
        <button
          class="pakui-button-icon close"
          @click="$emit('close')"
        >
          <span class="pakui-icon-xmark" />
        </button>
      </header>
      <main class="pakui-main">
        <slot />
      </main>
      <footer class="pakui-footer">
        <slot name="footer" />
      </footer>
    </div>
  </Overlay>
</template>

<script setup lang="ts">
import Overlay from './ModalOverlay.vue';

defineEmits<{
    (e: 'close'): void
}>();
</script>

<style scoped>
.pakui-header {
  align-items: center;
  border-bottom: 1px solid var(--pakui-border-color-slight);
  display: flex;
  padding: var(--pakui-gutter);
  position: relative;
}

[class*="pakui-icon-"] {
  height: 24px;
  width: 24px;
}

.pakui-header-content {
  -webkit-text-fill-color: transparent;
  background-clip: text;
  background-image: linear-gradient(90deg, var(--pakui-color-primary), var(--pakui-color-highlight));
  flex: 1;
  font-size: 14px;
  font-weight: 900;
}

.pakui-main {
  padding: var(--pakui-gutter);
}

.pakui-body {
  background: white;
  border-radius: 10px;
  box-shadow: 0 0 100px rgba(0, 0, 0, 0.2);
  overflow: hidden;
}

.pakui-footer {
  border-top: 1px solid var(--pakui-border-color-slight);
}
</style>

<style>
.pakui-modal-dialog .pakui-body {
  width: var(--pakui-dialog-width);
}

.pakui-modal-dialog .pakui-footer {
  display: flex;
}

.pakui-modal-dialog .pakui-footer .pakui-button {
  font-size: 14px;
  padding: 20px;
}

.pakui-modal-dialog .pakui-footer .pakui-button {
  width: 100%;
}

.pakui-footer .pakui-button {
  font-weight: bold;
}
</style>


