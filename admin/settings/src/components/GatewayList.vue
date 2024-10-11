<template>
  <div class="pakui-gateway-list">
    <GatewayListItem
      v-for="gateway in gateways"
      :key="gateway.id"
      :gateway="gateway"
      @configure="$emit('configure', gateway)"
      @edit="$emit('edit', gateway)"
      @trash="$emit('trash', gateway)"
    />
  </div>
</template>

<script setup lang="ts">
import Gateway from '../models/Gateway';
import GatewayListItem from './GatewayListItem.vue';

defineProps<{ gateways: Gateway[] }>();
defineEmits<{
    (e: 'configure', gateway: Gateway): void
    (e: 'edit', gateway: Gateway): void
    (e: 'trash', gateway: Gateway): void
}>();
</script>

<style scoped>
.pakui-gateway-list-item:not(:first-child) {
  margin-top: 20px;
}
</style>
