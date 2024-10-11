import ApiResponse from '../interfaces/ApiResponse.ts';
import getConfig from './getConfig';

export default function callBackend<I, O>(action: string, data: I): Promise<ApiResponse<O>> {
  return getConfig().api(action, data);
}
