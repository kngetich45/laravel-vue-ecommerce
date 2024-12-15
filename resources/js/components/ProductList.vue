<script>
import SingleProduct from './SingleProduct.vue';
import ProductSearch from './ProductSearch.vue';
import { useFlash } from '@/composables/useFlash'
import Api from '@/apis/Api'

export default {
    name: 'ProductList',
    components: {ProductSearch, SingleProduct},
     data(){
        return {
            products : []
        }
     },
    mounted() {
        let payload = {
            'title': ''
        };
        this.fetchProducts(payload)
    },
    methods:{
      ...useFlash(),
        fetchProducts(payload) {
            let errorStatus = ''
            Api.post('products', payload)
                .then((response) => {
                    let result = response.data
                    if (!result.error) {
                        this.products = result.products
                    } else {
                      this.flash('error', 'Download Error', result.message)
                    }
                })
                .catch((error) => {
                    if (!error.response) {
                        // network error
                        errorStatus = 'Network Error'
                    } else {
                        errorStatus = error.response.data.message
                    }
                    useFlash('error', 'Server Error', errorStatus)
                })
        },

        handleSubmit(searchProduct){
            let payload = {
                'title': searchProduct
            };
            this.fetchProducts(payload)
        }
    }
}


</script>

<template>
    <div id="product_list">
        <div class="m-3 grid place-items-center">
            <ProductSearch @search-product="handleSubmit" />
        </div>

        <div class="flex flex-wrap justify-center mt-3">
            <SingleProduct v-for="product in products" :product="product" :key="product.id"></SingleProduct>
        </div>
    </div>

</template>
