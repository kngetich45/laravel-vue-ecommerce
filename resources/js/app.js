import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import { createPinia } from "pinia";
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate'

const pinia = createPinia()
const app = createApp(App);


app.use(router);
app.use(pinia)
pinia.use(piniaPluginPersistedstate)
app.mount('#app');
