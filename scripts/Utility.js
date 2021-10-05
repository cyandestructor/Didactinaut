export default class Utility {
    static objectIsEmpty(object) {
        // Based on:
        // https://www.samanthaming.com/tidbits/94-how-to-check-if-object-is-empty/

        return Object.keys(object).length === 0 && object.constructor === Object;
    }

    static formDataToObject(formData, multiFields = null) {
        // Based on:
        // https://www.learnwithjason.dev/blog/get-form-values-as-json

        let object = Object.fromEntries(formData.entries());

        // If the data has multi-select values
        if (multiFields && Array.isArray(multiFields)) {
            multiFields.forEach((field) => {
            object[field] = formData.getAll(field);
            });
        }

        return object;
    }
}