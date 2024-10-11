<template>
  <div class="pakui-wrapper">
    <header class="pakui-header">
      <Logo />
      <div class="pakui-text">
        <h1 class="pakui-title">
          PAK Custom Payment Gateways for WooCommerce
        </h1>
        <h2 class="pakui-subtitle">
          {{ texts.settings }}
        </h2>
      </div>
    </header>
    <main class="pakui-main">
      <div class="pakui-gateway-list-header">
        <h3>{{ texts.gateways }}</h3>
        <div class="actions">
          <a
            class="pakui-button-icon pakui-help"
            :href="config.url_help"
            target="_blank"
          >
            <span class="pakui-icon-question" />
          </a>
          <button
            class="pakui-button pakui-create pakui-primary"
            @click="createGateway"
          >
            {{ texts.create_gateway }}
          </button>
        </div>
      </div>
      <GatewayList
        :gateways="gateways"
        @configure="configureGateway"
        @edit="editGateway"
        @trash="trashGateway"
      />
      <div
        v-if="gateways.length === 0"
        class="pakui-gateway-list-empty pakui-block"
      >
        <span class="pakui-icon-face-smile-wink" />
        <div class="pakui-gateway-list-empty-message">
          <p>{{ texts.gateway_list_empty_greeting }}</p>
          <p>{{ texts.gateway_list_empty_prompt }}</p>
        </div>
      </div>
    </main>
    <footer class="pakui-footer">
      <ul>
        <li>
          {{ texts.credits_author_part1 }}
          <a
            :href="config.url_author"
            target="_blank"
          >{{ texts.credits_author_part2 }}</a>.
        </li>
        <li>
          {{ texts.credits_contribution_part1 }}
          <a
            :href="config.url_contribution"
            target="_blank"
          >{{ texts.credits_contribution_part2 }}</a>
        </li>
        <li>
          {{ texts.credits_feedback_part1 }}
          <a
            :href="config.url_feedback"
            target="_blank"
          >{{ texts.credits_feedback_part2 }}</a>
        </li>
      </ul>
    </footer>
    <EditFormDialog :handle="editFormDialogHandle" />
    <ConfirmationDialog :handle="confirmationDialogHandle" />
    <MessageDialog :handle="messageDialogHandle" />
    <ProgressOverlay :is-open="isInProgress" />
  </div>
</template>

<script lang="ts" setup>
import { reactive, ref } from 'vue';
import ConfirmationDialog from './components/ConfirmationDialog.vue';
import DialogHelper from './helpers/DialogHelper';
import DialogResult from './enums';
import EditFormData from './models/EditFormData';
import EditFormDialog from './components/EditFormDialog.vue';
import EditFormDialogInput from './models/EditFormDialogInput';
import Gateway from './models/Gateway';
import GatewayList from './components/GatewayList.vue';
import Logo from './components/LogoImage.vue';
import MessageDialog from './components/MessageDialog.vue';
import ProgressOverlay from './components/ProgressOverlay.vue';
import deleteGateway from './api/deleteGateway';
import getConfig from './api/getConfig';
import saveGateway from './api/saveGateway';

const config = getConfig();
const confirmationDialogHandle = reactive(new DialogHelper<string, undefined>());
const editFormDialogHandle = reactive(new DialogHelper<EditFormDialogInput, EditFormData>());
const gateways = reactive<Gateway[]>(config.gateways.map((item) => new Gateway(item.id, item.title, item.description)));
const isInProgress = ref(false);
const messageDialogHandle = reactive(new DialogHelper<string, undefined>());
const { texts } = config;

const handleEditFormDialogResult = async (result: DialogHelper<EditFormDialogInput, EditFormData>) => {
  if (result.getResult() !== DialogResult.Ok) {
    return;
  }

  const data = result.getOutput() as EditFormData;

  isInProgress.value = true;

  try {
    const savedGateway = await saveGateway(new Gateway(data.id, data.title, data.description));
    const gatewayToUpdate = gateways.find((item) => item.id === savedGateway.id);

    if (typeof gatewayToUpdate === 'undefined') {
      gateways.push(savedGateway);
    } else {
      gatewayToUpdate.title = savedGateway.title;
      gatewayToUpdate.description = savedGateway.description;
    }
  } catch {
    messageDialogHandle.show(texts.action_failed);
  }

  isInProgress.value = false;
};

const configureGateway = (gateway: Gateway) => {
  window.location.href = `admin.php?page=wc-settings&tab=checkout&section=${gateway.id}`;
};

const createGateway = async () => {
  const result = await editFormDialogHandle.show(new EditFormDialogInput(gateways));

  await handleEditFormDialogResult(result);
};

const editGateway = async (gateway: Gateway) => {
  const result = await editFormDialogHandle.show(
    new EditFormDialogInput(gateways, new EditFormData(gateway.id, gateway.title, gateway.description)),
  );

  await handleEditFormDialogResult(result);
};

const trashGateway = async (gateway: Gateway) => {
  const result = await confirmationDialogHandle.show(texts.gateway_removal_message);

  if (result.getResult() === DialogResult.Ok) {
    isInProgress.value = true;

    try {
      const deletedGatewayId = await deleteGateway(gateway.id);
      gateways.splice(
        gateways.findIndex((item) => item.id === deletedGatewayId),
        1,
      );
    } catch {
      messageDialogHandle.show(texts.action_failed);
    }

    isInProgress.value = false;
  }
};
</script>

<style scoped>
/* -----------------------------------------------------------------------------
 * Common
 * -------------------------------------------------------------------------- */
.pakui-wrapper {
  color: #000000;
  display: inline-block;
  max-width: 700px;
  padding: var(--pakui-gutter);
  text-align: left;
}

/* -----------------------------------------------------------------------------
 * Header: Structure
 * -------------------------------------------------------------------------- */

.pakui-header {
  align-items: center;
  background: white;
  border-radius: 10px;
  display: flex;
  padding: var(--pakui-gutter);
  text-align: left;
}

/* -----------------------------------------------------------------------------
 * Header: Content
 * -------------------------------------------------------------------------- */

.pakui-header .pakui-title,
.pakui-header .pakui-subtitle {
  font-size: 12px;
}

.pakui-header .pakui-title {
  font-weight: 300;
  margin: 0;
}

.pakui-header .pakui-subtitle {
  font-weight: 900;
  margin: 10px 0 0;
  padding: 0;
}

.pakui-header .pakui-text {
  margin-left: var(--pakui-gutter);
}

@media (min-width: 480px) {
  .pakui-header .pakui-title {
    font-size: 20px;
  }

  .pakui-header .pakui-subtitle {
    font-size: 18px;
  }
}

/* -----------------------------------------------------------------------------
 * Main: Common
 * -------------------------------------------------------------------------- */

.pakui-main,
.pakui-main .pakui-gateway-list {
  margin-top: var(--pakui-gutter);
}

.pakui-gateway-list-header {
  align-items: center;
  display: flex;
  font-size: 12px;
  font-weight: bold;
}

.pakui-gateway-list-header h3 {
  color: var(--pakui-text-color-slight);
  flex: 1;
  font-size: inherit;
  font-weight: inherit;
  text-transform: uppercase;
}

.pakui-gateway-list-header .actions {
  display: flex;
}

.pakui-gateway-list-header .pakui-create {
  border-radius: 5px;
  font-weight: inherit;
}

.pakui-gateway-list-header .pakui-help {
  align-items: center;
  border-radius: 50%;
  border: 1px solid var(--pakui-border-color-slight);
  display: flex;
  justify-content: center;
  margin-right: 10px;
  width: 32px;
}

.pakui-gateway-list-header .pakui-help [class*='pakui-icon-'] {
  height: 16px;
  width: 16px;
}

/* -----------------------------------------------------------------------------
 * Main: Empty Gateway List
 * -------------------------------------------------------------------------- */

.pakui-gateway-list-empty {
  background-image: linear-gradient(
    45deg,
    #ffffff 25%,
    #fafafa 25%,
    #fafafa 50%,
    #ffffff 50%,
    #ffffff 75%,
    #fafafa 75%,
    #fafafa 100%
  );
  background-size: 56px 56px;
  border-radius: 10px;
  font-size: 14px;
  padding: calc(2 * var(--pakui-gutter)) var(--pakui-gutter);
  text-align: center;
}

.pakui-icon-face-smile-wink {
  background: var(--pakui-color-primary);
  height: 64px;
  width: 64px;
}

.pakui-gateway-list-empty-message {
  margin-top: var(--pakui-gutter);
}

.pakui-gateway-list-empty-message p:not(:first-child) {
  margin-top: 15px;
}

/* -----------------------------------------------------------------------------
 * Footer
 * -------------------------------------------------------------------------- */

.pakui-footer {
  border-top: 1px dashed var(--pakui-border-color-slight);
  color: rgba(0, 0, 0, 0.4);
  font-size: 12px;
  margin-top: var(--pakui-gutter);
  padding-top: var(--pakui-gutter);
}

.pakui-footer ul {
  margin: 0;
  padding: 0;
}

.pakui-footer li {
  margin-bottom: 0;
}

.pakui-footer li:not(:first-child) {
  margin-top: 10px;
}

.pakui-footer a {
  color: var(--pakui-color-primary);
  font-weight: inherit;
}

.pakui-footer a:hover {
  color: var(--pakui-color-highlight);
}
</style>
