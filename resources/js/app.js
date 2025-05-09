

import './bootstrap';
import { createApp } from 'vue';
import { createStore } from 'vuex';


const store = createStore({
    state(){
        return {
            item: {}
        }
    }
})

const app = createApp({});

app.use(store);

import ExampleComponent from './components/ExampleComponent.vue';
app.component('example-component', ExampleComponent);

import LoginComponent from './components/Login.vue'
app.component('login-component', LoginComponent);

import HomeComponent from './components/Home.vue'
app.component('home-component', HomeComponent)

import MarcasComponent from './components/Marcas.vue'
app.component('marcas-component', MarcasComponent)

import InputConteinerComponent from './components/InputConteiner.vue'
app.component('input-conteiner-component', InputConteinerComponent)

import TableComponent from './components/Table.vue'
app.component('table-component', TableComponent)


import CardComponent from './components/Card.vue'
app.component('card-component', CardComponent)

import ModalComponent from './components/Modal.vue'
app.component('modal-component', ModalComponent)

import AlertComponent from './components/Alert.vue'
app.component('alert-component', AlertComponent)

import PaginateComponent from './components/Paginate.vue'
app.component('paginate-component', PaginateComponent)


app.mount('#app');
