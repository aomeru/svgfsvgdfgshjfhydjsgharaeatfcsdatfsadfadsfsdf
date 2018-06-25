require('./bootstrap');
var Vue = require('vue');
window.Vue = Vue;
var Slug = require('slug');
Slug.defaults.mode = 'rfc3986';

// Vue.component('example-component', require('./components/ExampleComponent.vue'));

// const app = new Vue({
//     el: '#app',
//     created()
//     {
//         console.log('created');
//     }
// });

$(function(){
    //console.log('hello world')
});
require('./portalmenu');
