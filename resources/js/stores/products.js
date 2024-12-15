import Api from '@/apis/Api'
import { defineStore } from 'pinia'
import { useFlash } from '@/composables/useFlash'

export const useProductStore = defineStore('products', {

    state:() => ({
        products: [],
        singleProduct:[]
    }),
    getters: {
        getProducts(state) {
            return state.products
        },
        getSingleProduct(state) {
            return state.singleProduct
        }
    },
    actions: {
        async fetchProducts(payload) {
            let errorStatus = ''

            Api.post('products', payload)
                .then((response) => {
                    let result = response.data

                    if (!result.error) {
                        this.state.products = result.products
                    } else {
                        useFlash().flash('error', 'Download Error', result.message)
                    }
                })
                .catch((error) => {
                    if (!error.response) {
                        // network error
                        errorStatus = 'Network Error'
                    } else {
                        errorStatus = error.response.data.message
                    }
                    useFlash().flash('error', 'Server Error', errorStatus)
                })
        },
        async fetchAProduct(id) {
            let errorStatus = ''

            Api.get('products/' + id)
                .then((response) => {
                    let result = response.data

                    if (!result.error) {
                        this.state.singleProduct = result.product
                    } else {
                        useFlash().flash('error', 'Download Error', result.message)
                    }
                })
                .catch((error) => {
                    if (!error.response) {
                        // network error
                        errorStatus = 'Network Error'
                    } else {
                        errorStatus = error.response.data.message
                    }
                    useFlash().flash('error', 'Server Error', errorStatus)
                })
        }
    },
})
