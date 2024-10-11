import EditFormData from './EditFormData';
import Gateway from './Gateway';

export default class EditFormDialogInput {
    constructor(
        public readonly gateways: Gateway[],
        public readonly data?: EditFormData,
    ) {}
}
