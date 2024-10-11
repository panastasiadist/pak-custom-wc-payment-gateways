export default class EditFormData {
    constructor(
        public id = '',
        public title = '',
        public description = '',
    ) {}

    updateFromSame(data?: EditFormData) {
        this.id = data?.id ?? '';
        this.title = data?.title ?? '';
        this.description = data?.description ?? '';
    }
}
