import { createRouter, createWebHistory } from 'vue-router';
import ProductView from '@/views/ProductView.vue'
import LoginView from '../views/LoginView.vue'
import RegisterView from '../views/RegisterView.vue'
import CartView from '../views/CatView.vue'
import OrderView from '../views/OrderView.vue'
import ProductDetailsView from '@/views/ProductDetailsView.vue'

const routes = [
    {
        path: '/',
        name: 'products',
        component: ProductView
    },
    {
        path: '/products/:id',
        name: 'product_details',
        component: ProductDetailsView,
        props: true
    },
    {
        path: '/register',
        name: 'register',
        component: RegisterView
    },
    {
        path: '/login',
        name: 'login',
        component: LoginView
    },
    {
        path: '/cart',
        name: 'cart',
        component: CartView
    },
    {
        path: '/orders',
        name: 'orders',
        component: OrderView
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
