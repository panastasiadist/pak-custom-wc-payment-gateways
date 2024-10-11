export default class FormFieldDefinition {
    constructor(
        public id: string,
        public title: string,
        public validator: (value: string) => string,
    ) {}
}
