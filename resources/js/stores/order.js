import { defineStore } from 'pinia';
import { computed, ref } from 'vue';
import { useFlash } from '@/composables/useFlash';
import { useAuthStore } from "./auth";
import axios from "axios";
import { useRouter } from 'vue-router';
import router from "../router";

export const useOrderStore = defineStore('order',{
    state: () => ({
        orders: [],
        ordersCount: 0,
    }),
    getters: {
         getOrders(state) {
             return state.orders;
         },
        getOrderCount(state) {
             return state.ordersCount;
        }
    },
    actions: {

        getHeadersConfig() {
            const authStore = useAuthStore();
            return {
                headers: {
                    Accept: 'application/json',
                    Authorization: 'Bearer ' + authStore.getToken
                }
            }
        },
        fetchOrderItems() {
            let errorStatus = '';
           const authStore = useAuthStore();
            const config = this.getHeadersConfig();

           if (!authStore.isAuthenticated) {
               router.push("/");
           }else
           {
               axios.get('http://localhost/api/order/' + authStore.getUser.id, config)
                   .then(response => {
                       if (response.data) {
                           this.orders = response.data.order;
                           this.ordersCounts(response.data.order)
                       }
                   }).catch(error => {
                   if (!error.response) {
                       // network error
                       errorStatus = 'Network Error';
                   } else {
                       errorStatus = error.response.data.message;
                   }
                  useFlash().flash('error', 'Auth Error', errorStatus);
               });
           }

        },

        ordersCounts (cart){
            if (cart.length === 0) {
                this.ordersCount =  0;
            } else {
                 this.ordersCount = cart[0].order_items.reduce((total, item) => {
                    return total + item.quantity
                }, 0);
            }
        },
    },

});
