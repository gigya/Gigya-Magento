/*
 * Add json validation script to prototype Validation object
 * jsonlint file is used for parsing the input (v) (located at js/gigya/json
 *
 * Validation rules are located at js/prototype/validation.js.
 * Help: http://blog.kyp.fr/how-to-validate-magento-configuration-values-format/
 *       http://inchoo.net/magento/programming-magento/validate-your-input-magento-style/
 */

Validation.add('validate-json', 'JSON is not valid', function (v) {
    return Validation.get('IsEmpty').test(v) || validateJson(v)
});

function validateJson(v) {
    if (v.charAt(0) === '{') { // v is a json string
        try {
            jsonTest = jsonlint.parse(v);
            if (jsonTest) {
                return true;
            }
        } catch (err) {
            return false;
        }
    } else { // v is in key|val format
        return true;
    }

}
function isJson(str) {
  if (str.charAt(0) === '{') {
      return true;
  } else {
      return false;
  }
}