/*
 * Add json validation script to prototype Validation object
 * jsonlint file is used for parsing the input (v)
 *
 * Validation rules are located at js/prototype/validation.js.
 * Help: http://blog.kyp.fr/how-to-validate-magento-configuration-values-format/
 *       http://inchoo.net/magento/programming-magento/validate-your-input-magento-style/
 */

Validation.add('validate-json', 'JSON is not valid', function (v) {
    return Validation.get('IsEmpty').test(v) || validateJson(v)
});

function validateJson(v) {
    try {
        jsonTest = jsonlint.parse(v);
        if (jsonTest) {
            return true;
        }
    } catch (err) {
        return false;
    }

}