<template>
  <Teleport to="body">
    <Transition name="fade">
      <Dialog
        v-if="handle.isOpen"
        class="pakui-confirmation-dialog"
        @close="cancel"
        @keyup.esc="cancel"
      >
        <template #header>
          {{ texts.confirmation }}
        </template>
        {{ handle.getInput() }}
        <template #footer>
          <button
            class="pakui-button pakui-secondary"
            @click="cancel"
          >
            {{ texts.cancel }}
          </button>
          <button
            class="pakui-button pakui-primary"
            @click="confirm"
          >
            {{ texts.confirm }}
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
  handle: DialogHelper<string, undefined>;
}>();

const { texts } = getConfig();

const cancel = () => props.handle.close(DialogResult.Cancel);
const confirm = () => props.handle.close(DialogResult.Ok);
</script>
