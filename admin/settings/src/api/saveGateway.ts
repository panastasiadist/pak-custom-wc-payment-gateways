import Gateway from '../models/Gateway';
import callBackend from './callBackend';
import getConfig from './getConfig.ts';

export default async function saveGateway(gateway: Gateway): Promise<Gateway> {
  const { data } = await callBackend<Gateway, Gateway>(getConfig().action_gateway_save, gateway);
  return Gateway.createFromRecord({
    id: data.id,
    description: data.description,
    title: data.title
  });
}
