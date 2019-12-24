/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./../../bootstrap');

import Vue from 'vue';

import Global from './../../mixins/global';

import Form from './../../plugins/form';
import BulkAction from './../../plugins/bulk-action';

import LeafletMap from './../../components/LeafletMap';

Vue.use(LeafletMap);

const app = new Vue({
    el: '#app',

    mixins: [
        Global
    ],

    components: {
        [LeafletMap.name]: LeafletMap,
    },

    data: function () {
        return {
            form: new Form('route'),
            bulk_action: new BulkAction('routes')
        }
    }
});
