const ALLOWED_HTML = [
  // e.g. jQuery
  '<textarea></textarea>'
]

const ALLOWED_SCRIPTS_REGEXP = [
  // APIs should go here, e.g. YouTube
  new RegExp('^https://s\.ytimg\.com/')
]

const TrustedTypesAvailable = typeof TrustedTypes !== 'undefined';
const DefaultPolicy = TrustedTypesAvailable ? TrustedTypes.createPolicy('paws', {
  createHTML(i) {
    if (ALLOWED_HTML.includes(i)) { //check HTML against a whitelist here
      return s
    }
    throw new TypeError('Disallowed HTML')
  },
  createScriptURL(url) {
    if (ALLOWED_SCRIPTS_REGEXP.find((regexp) => regexp.test(url))) {
      return url
    }
    throw new TypeError('Disallowed script URL')
  }
}, true) : null;

// https://www.youtube.com/watch?v=po6GumtHRmU