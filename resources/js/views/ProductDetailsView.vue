<template>
    <Header/>
    <div v-if="singleProduct !== null" class="flex flex-wrap justify-center mt-5">
        <div class="min-w-[450px]">
            <img class="object-cover w-[100%] max-w-[450px] rounded-t-lg md:rounded-none md:rounded-l-lg" :src="singleProduct.image_url" alt="product image">
        </div>

        <div class="flex flex-col pl-4 pr-4 leading-normal min-w-[300px]">
            <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-black">
                {{ singleProduct.title }}
            </h5>

            <p class="mb-3 font-normal text-small text-gray-700 dark:text-gray-400">
                {{ singleProduct.description }}
            </p>

            <div class="mt-3 flex items-center justify-between">
                <span class="text-3xl font-bold text-gray-900 dark:text-black">${{ singleProduct.price }}</span>
                <a @click.prevent="handAddToCart" class="cursor-pointer text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:outline-none focus:ring-purple-300 font-small rounded-lg text-sm px-5 py-2.5 text-center dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-800">
                    Add to cart
                </a>
            </div>
        </div>
    </div>
</template>

<script>
import { useAuthStore } from '@/stores/auth';
import { useCartStore } from '@/stores/cart';
import { useFlash } from '@/composables/useFlash';
import { useRouter } from 'vue-router';
import Api from "../apis/Api";
import Header from "../components/Navmenu/header.vue";
import router from "../router";

export default {
    name: 'SingleProductDetails',
    components: {Header},
    props:['id'],
    data(){
        return {
            singleProduct : [],
            userStore: useAuthStore(),
            userCart: useCartStore(),
        }
    },
    mounted(){
        this.fetchAProduct(this.id)
    },

    methods:{
        ...useFlash(),
        handAddToCart() {
            if (this.userStore.isAuthenticated) {
                this.userCart.addItemToCart(this.id, 1);
            } else {
                this.flash('warning', "Unauthenticated", "Please sign in first!");
                setTimeout(() =>  {
                    router.push("/login");
                }, 1500);
            }
        },
        fetchAProduct(id) {
            let errorStatus = ''

            Api.get('products/' + id)
                .then((response) => {
                    let result = response.data
                    if (!result.error) {
                        this.singleProduct = result.product

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
                    this.flash('error', 'Server Error', errorStatus)
                })
        }
    }
}

</script>


