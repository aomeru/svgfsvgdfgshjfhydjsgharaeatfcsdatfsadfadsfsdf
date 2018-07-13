require('./bootstrap');
var Vue = require('vue');
window.Vue = Vue;
var Slug = require('slug');
Slug.defaults.mode = 'rfc3986';
require('./portalmenu');
