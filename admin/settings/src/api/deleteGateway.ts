import callBackend from './callBackend';
import getConfig from './getConfig.ts';

export default async function deleteGateway(gatewayId: string): Promise<string> {
  const { data } = await callBackend<string, {
    gateway_id_deleted: string;
  }>(getConfig().action_gateway_delete, gatewayId);

  return data.gateway_id_deleted;
}
