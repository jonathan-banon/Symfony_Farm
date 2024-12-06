import { createRouter, createWebHistory } from 'vue-router';
import Home from '../js/components/App.vue'; 
import Login from '../js/components/login/App.vue'; 


const routes = [
    { path: '/', component: Home },
    { path: '/login', component: Login },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
