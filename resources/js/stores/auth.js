import Api from '@/apis/Api'
import axios from 'axios'
import { defineStore } from 'pinia'
import { useFlash } from '@/composables/useFlash'
import { useCartStore } from '@/stores/cart'
import router from "../router";

export const useAuthStore = defineStore(
  'auth', {
        state: () => ({
            user: null,
            token: null,
            authenticated: false,
        }),
        persist: true,
        getters: {
            isAuthenticated(state) {
                return state.user !== null
            },
            getToken(state) {
                return state.token
            },
            getUser(state) {
                return state.user
            },
            isTokenEmpty(state) {
                return state.token === null
            }
        },
        actions:{
            async login(payload) {
                            let errorStatus = ''

                            Api.post('login', payload)
                                .then((response) => {
                                    let result = response.data

                                    if (!result.error) {
                                        this.user = result.user
                                        this.token = result.authorisation.token

                                        const cartStore = useCartStore();
                                        cartStore.fetchCartItems();
                                        if (cartStore.getCart.length > 0) {
                                            router.push('cart')
                                        } else {
                                            router.push('/')
                                        }
                                    } else {
                                        useFlash().flash('error', 'Auth Error', result.message)
                                    }
                                })
                                .catch((error) => {
                                    if (!error.response) {
                                        // network error
                                        errorStatus = 'Network Error'
                                    } else {
                                        errorStatus = error.response.data.message
                                    }
                                    useFlash().flash('error', 'Auth Error', errorStatus)
                                })
                        },

            async logout() {
                        if (!this.user) {
                            useFlash().flash('error', 'Auth Error', 'User is not authenticated!')
                            return
                        }
                        console.log(this.token)
                        let errorStatus = ''
                        axios
                            .post('http://localhost/api/logout', {}, this.getHeadersConfig())
                            .then((response) => {

                                if (!response.data.error) {
                                    useFlash().flash('success', 'Log out', response.data.message)
                                    this.user = null
                                    this.token = null
                                    router.push('/')
                                }
                            })
                            .catch((error) => {
                                if (!error.response) {
                                    // network error
                                    errorStatus = 'Network Error'
                                } else {
                                    errorStatus = error.response.data.message
                                }
                                useFlash().flash('error', 'Auth Error', errorStatus)
                            })
                    },

            getHeadersConfig() {
                return {
                    headers: {
                        Accept: 'application/json',
                        Authorization: 'Bearer ' + this.token
                    }
                }
            }
        },

   // return { user, token, login, logout, isAuthenticated, getHeadersConfig }
  }
)
