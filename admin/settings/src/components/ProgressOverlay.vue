<template>
  <Teleport to="body">
    <Transition name="fade">
      <div
        v-if="isOpen"
        class="pakui-progress-overlay"
      >
        <Overlay>
          <div class="pakui-progress-overlay-main">
            <LogoCube />
            <div class="pakui-progress-overlay-text">
              {{ texts.progress_message }}
            </div>
          </div>
        </Overlay>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup lang="ts">
import LogoCube from './LogoCube.vue';
import Overlay from './ModalOverlay.vue';
import getConfig from '../api/getConfig';

defineProps<{
    isOpen: boolean;
}>();

const { texts } = getConfig();
</script>

<style scoped>
.pakui-progress-overlay-main {
  text-align: center;
}

.pakui-progress-overlay-text {
  color: #ffffff;
  font-size: 20px;
  margin-top: 60px;
}

.pakui-progress-overlay-main .pakui-logo-cube {
  display: inline-block;
}
</style>
