import Gateway from '../models/Gateway';

export default interface Configuration {
  action_gateway_delete: string;
  action_gateway_save: string;
  api: <I, O>(action: string, data: I) => Promise<O>;
  gateway_id_prefix: string;
  gateways: Gateway[];
  nonce: string;
  texts: {
    action_failed: string;
    cancel: string;
    close: string;
    configure: string;
    confirm: string;
    confirmation: string;
    create: string;
    create_gateway: string;
    credits_author_part1: string;
    credits_author_part2: string;
    credits_contribution_part1: string;
    credits_contribution_part2: string;
    credits_feedback_part1: string;
    credits_feedback_part2: string;
    delete: string;
    description: string;
    edit: string;
    edit_gateway: string;
    field_required: string;
    gateway_id_duplicate: string;
    gateway_list_empty_greeting: string;
    gateway_list_empty_prompt: string;
    gateway_removal_message: string;
    gateways: string;
    message: string;
    progress_message: string;
    settings: string;
    title: string;
    update: string;
  };
  url_ajax: string;
  url_author: string;
  url_contribution: string;
  url_feedback: string;
  url_help: string;
}
