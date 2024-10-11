<template>
  <Teleport to="body">
    <Transition name="fade">
      <Dialog
        v-if="handle.isOpen"
        class="pakui-message-dialog"
        @close="close"
      >
        <template #header>
          {{ texts.message }}
        </template>
        {{ handle.getInput() }}
        <template #footer>
          <button
            class="pakui-button pakui-primary"
            @click="close"
          >
            {{ texts.close }}
          </button>
        </template>
      </Dialog>
    </Transition>
  </Teleport>
</template>

<script setup lang="ts">
import Dialog from './ModalDialog.vue';
import DialogHelper from '../helpers/DialogHelper';
import DialogResult from '../enums';
import getConfig from '../api/getConfig';

const props = defineProps<{
    handle: DialogHelper<string, undefined>
}>();

const { texts } = getConfig();

const close = () => props.handle.close(DialogResult.Ok);
</script>
