
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

import { Form, HasError, AlertError } from 'vform'

Vue.component('pagination', require('laravel-vue-pagination'));

Vue.component(HasError.name, HasError)
Vue.component(AlertError.name, AlertError)

window.Form=Form;

import VueRouter from 'vue-router'
Vue.use(VueRouter)



import {routes} from './routes.js';


const router = new VueRouter({
    mode:'history',
    routes // short for `routes: routes`
});

var moment = require('moment');

Vue.filter('uptext',function(text){
   return text.charAt(0).toUpperCase() + text.slice(1);
});
Vue.filter('myDate',function (created) {
    return moment(created).format('MMMM Do YYYY');
});

window.Fire=new Vue();

import VueProgressBar from 'vue-progressbar';

Vue.use(VueProgressBar, {
    color:'rgb(143,255,199)',
    failedColor: 'red',
    thickness: '5px',
});

import Swal from 'sweetalert2';
window.Swal=Swal;

const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000
});
window.Toast=Toast;

import Gate from './Gate.js';
Vue.prototype.$gate=new Gate(window.user);

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i);
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

Vue.component('notFound', require('./components/NotFound.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
    router,
    data:{
        search:''
    },
    methods:{
        searchit:_.debounce(()=>{
            Fire.$emit('searching');
            // console.log('search...')
        },1000),
        printme(){
            window.print()
        }
    }
});
