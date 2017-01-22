/**
 * Core Components
 */
Vue.component('dropzonejs', require('./Vue/VueDropzone.vue'));
Vue.component('tinymce', require('./Vue/VueTinymce.vue'));

/**
 * Inputs
 */
Vue.component('form-text',   require('./FormText.vue'));
Vue.component('form-number', require('./FormNumber.vue'));
Vue.component('form-password',   require('./FormPassword.vue'));

Vue.component('form-slider', require('./FormSlider.vue'));
Vue.component('form-checkbox', require('./FormCheckbox.vue'));

Vue.component('form-select', require('./FormSelect.vue'));
Vue.component('form-selectize', require('./FormSelectize.vue'));

Vue.component('form-radio',  require('./FormRadioButton.vue'));
Vue.component('form-checkbox-group',  require('./FormCheckboxGroup.vue'));

Vue.component('form-textarea', require('./FormTextarea.vue'));
Vue.component('form-tinymce', require('./FormTinymce.vue'));

Vue.component('form-daterange', require('./FormDateRange.vue'));
Vue.component('form-date', require('./FormDate.vue'));

Vue.component('form-dropzone', require('./FormDropzone.vue'));

Vue.component('form-seo', require('./FormSeoData.vue'));
Vue.component('form-elfinder', require('./FormElfinder.vue'));

/**
 * Helpers
 */
Vue.component('form-loader', require('./FormLoader.vue'));
Vue.component('form-errors', require('./FormErrors.vue'));
Vue.component('form-save-button', require('./FormSaveButton.vue'));

