import { defineStore } from 'pinia';
import { computed, ref } from 'vue';
import { useFlash } from '@/composables/useFlash';
import { useAuthStore } from "./auth";
import axios from "axios";
import { useRouter } from 'vue-router';
import router from "../router";

export const useCartStore = defineStore('cart',{
    state: () => ({
        cart: [],
        taxValue: 18,
        itemCount: 0,
    }),
    getters: {
         getCart(state) {
             return state.cart;
         },
        getItemCount(state) {
             return state.itemCount;
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
       fetchCartItems() {
            let errorStatus = '';
           const authStore = useAuthStore();
            const config = this.getHeadersConfig();

           if (!authStore.isAuthenticated) {
               router.push("/");
           }else
           {
               axios.get('http://localhost/api/cart/getItems/' + authStore.getUser.id, config)
                   .then(response => {
                       if (response.data) {
                           this.cart = response.data;
                           this.itemCounts(response.data)
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

        async  addItemToCart(productId, qty) {
            const authStore = useAuthStore();
            let errorStatus = '';
            if (! authStore.token || ! authStore.user) {
               useFlash().flash('error', 'Auth Error', "User seems to be unauthenticated!");
                return;
            }
            const config = this.getHeadersConfig();
            let payloads = {
                "product_id": productId,
                "user_id": authStore.user.id,
                "quantity": qty
            };

            axios.post('http://localhost/api/cart/add', payloads, config)
                .then(response => {
                    if (! response.data.error) {
                       useFlash().flash('success', 'Add Item', response.data.message);
                     router.push("/cart");
                    } else {
                       useFlash().flash('error', 'Item Add Error', response.data.message);
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
        },
       async  removeAnItem(payload) {
            let errorStatus = '';
           const authStore = useAuthStore();

            const config = this.getHeadersConfig();
           if (!authStore.isAuthenticated) {
               await router.push("/login");
           }else {
               axios.post('http://localhost/api/cart/removeItem', payload, config)
                   .then(response => {
                       if (!response.data.error) {
                          useFlash().flash('success', 'Remove Order Item', response.data.message);
                           this.fetchCartItems();
                       } else {
                          useFlash().flash('error', 'Remove Item Error', response.data.message);
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

        async updateQty(payload) {
            let errorStatus = '';
            const authStore = useAuthStore();
            const config = this.getHeadersConfig();
            if (!authStore.isAuthenticated) {
                await router.push("/login");
            }else {
                axios.post('http://localhost/api/cart/updateCart', payload, config)
                    .then(response => {
                        if (!response.data.error) {
                            this.fetchCartItems();
                        } else {
                           useFlash().flash('error', 'Update Error', response.data.message);
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
        async checkoutACart(payload) {
                    let errorStatus = '';
                     const authStore = useAuthStore();
                    const config = this.getHeadersConfig();

                            if (!authStore.isAuthenticated) {
                                await router.push("/login");
                            }else
                            {
                                axios.post('http://localhost/api/cart/checkout', payload, config)
                                    .then(response => {
                                        if (! response.data.error) {
                                           useFlash().flash('success', 'Checkout', response.data.message);
                                            this.fetchCartItems();
                                            router.push("/");
                                        } else {
                                           useFlash().flash('error', 'Checkout Error', response.data.message);
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

        async clearACart(payload) {
                let errorStatus = '';
                 const authStore = useAuthStore();
                const config = this.getHeadersConfig();
                if (!authStore.isAuthenticated) {
                    await router.push("/login");
                }else {
                axios.post('http://localhost/api/cart/removeAll', payload, config)
                    .then(response => {
                        if (!response.data.error) {
                           useFlash().flash('success', 'Clear Order', response.data.message);
                            this.fetchCartItems();
                        } else {
                           useFlash().flash('error', 'Clear Order', response.data.message);
                        }
                    }).catch(error => {
                    if (!error.response) {
                        errorStatus = 'Network Error';
                    } else {
                        errorStatus = error.response.data.message;
                    }
                   useFlash().flash('error', 'Auth Error', errorStatus);
                });
            }
            },

        itemCounts (cart){
            if (cart.length === 0) {
                this.itemCount =  0;
            } else {
                 this.itemCount = cart.reduce((total, item) => {
                    return total + item.quantity
                }, 0);
            }
        },
    },

});
