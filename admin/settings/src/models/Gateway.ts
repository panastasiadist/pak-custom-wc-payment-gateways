export default class Gateway {
    constructor(
        public id: string,
        public title: string,
        public description: string,
    ) {
        if (!(id && title && description)) {
            throw new Error('At least one of the provided parameters is empty');
        }
    }

    public static createFromRecord(data: Record<string, string>): Gateway {
        return new Gateway(data.id ?? '', data.title ?? '', data.description ?? '');
    }
}
