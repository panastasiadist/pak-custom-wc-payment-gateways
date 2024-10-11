<template>
  <div class="pakui-modal-overlay">
    <slot />
  </div>
</template>

<style scoped>
.pakui-modal-overlay {
  align-content: center;
  align-items: center;
  background-color: rgb(0, 0, 0, 0.6);
  bottom: 0;
  display: flex;
  justify-content: center;
  left: 0;
  padding: var(--pakui-gutter);
  position: fixed;
  right: 0;
  top: 0;
  z-index: 9999;
}
</style>
