<template>
  <Teleport to="body">
    <Transition name="fade">
      <Dialog
        v-if="handle.isOpen"
        class="pakui-edit-form-dialog"
        @close="cancel"
      >
        <template #header>
          <span v-if="isUpdateMode">{{ texts.edit_gateway }}</span>
          <span v-else>{{ texts.create_gateway }}</span>
        </template>
        <div class="pakui-field">
          <label for="gatewayTitle">{{ texts.title }}</label>
          <input
            id="gatewayTitle"
            v-model="data.title"
            type="text"
            required
            @keyup.enter="save"
          >
          <span
            v-if="errors.title"
            class="pakui-error"
          >{{ errors.title }}</span>
        </div>
        <div class="pakui-field">
          <label for="gatewayDescription">{{ texts.description }}</label>
          <input
            id="gatewayDescription"
            v-model="data.description"
            type="text"
            required
            @keyup.enter="save"
          >
          <span
            v-if="errors.description"
            class="pakui-error"
          >{{ errors.description }}</span>
        </div>
        <template #footer>
          <button
            class="pakui-button pakui-secondary"
            @click="cancel"
          >
            {{ texts.cancel }}
          </button>
          <button
            class="pakui-button pakui-primary"
            :disabled="!isSaveEnabled"
            @click="save"
          >
            <span v-if="isUpdateMode">{{ texts.update }}</span>
            <span v-else>{{ texts.create }}</span>
          </button>
        </template>
      </Dialog>
    </Transition>
  </Teleport>
</template>

<script setup lang="ts">
import { computed, onMounted, reactive, ref, watch } from 'vue';
import Dialog from './ModalDialog.vue';
import DialogHelper from '../helpers/DialogHelper';
import DialogResult from '../enums';
import EditFormData from '../models/EditFormData';
import EditFormDialogInput from '../models/EditFormDialogInput';
import getConfig from '../api/getConfig';
import { v4 } from 'uuid';

const props = defineProps<{
  handle: DialogHelper<EditFormDialogInput, EditFormData>;
}>();

const data = reactive(new EditFormData());
const errors = ref<Record<string, string>>({});
const isUpdateMode = computed(() => typeof props.handle.getInput()?.data !== 'undefined');
const isSaveEnabled = computed(() => {
  const inputData = props.handle.getInput()?.data;
  const hasNoErrors = Object.values(errors.value).filter((err) => err.length > 0).length === 0;
  const hasDataChanged = data.title !== inputData?.title || data.description !== inputData?.description;

  return hasNoErrors && (!isUpdateMode.value || hasDataChanged);
});

const { texts } = getConfig();

const validate = () => {
  errors.value = {};

  if (data.id === '') {
    errors.value.id = texts.field_required;
  } else {
    const idIsDuplicate = props.handle.getInput()?.gateways.findIndex((gateway) => gateway.id === data.id) !== -1;
    const isNotSelfId = props.handle.getInput()?.data?.id !== data.id;

    if (idIsDuplicate && isNotSelfId) {
      errors.value.id = texts.gateway_id_duplicate;
    }
  }

  errors.value.title = data.title.length === 0 ? texts.field_required : '';
  errors.value.description = data.description.length === 0 ? texts.field_required : '';
};

onMounted(() => validate());

watch(
  () => props.handle.isOpen,
  (isOpen) => {
    if (isOpen) {
      data.updateFromSame(props.handle.getInput()?.data);

      if (!isUpdateMode.value) {
        data.id = getConfig().gateway_id_prefix + v4().replace(/-+/g, '');
      }
    }
  },
);

watch(
  () => data,
  () => validate(),
  { deep: true },
);

const cancel = () => props.handle.close(DialogResult.Cancel);
const save = () => !isSaveEnabled.value || props.handle.close(DialogResult.Ok, data);
</script>

<style scoped>
.pakui-field label {
  display: block;
  font-size: 12px;
  font-weight: bold;
  text-transform: uppercase;
}

.pakui-error {
  color: red;
  display: inline-block;
  margin-top: 4px;
}

.pakui-field:not(:first-child) {
  margin-top: var(--pakui-gutter);
}

.pakui-field input {
  background: rgba(0, 0, 0, 0.02);
  border: 2px solid var(--pakui-border-color-slight);
  margin-top: 4px;
  outline: none;
  padding: 4px;
  transition: border 0.2s linear;
  width: 100%;
}

.pakui-field input:focus {
  border-color: var(--pakui-color-highlight);
  box-shadow: none;
  outline: none;
}
</style>

<style>
.pakui-edit-form-dialog .pakui-modal .pakui-body {
  max-width: 500px;
  width: 100%;
}
</style>
