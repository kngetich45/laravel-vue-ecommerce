

<template>
  <div
    class="product-card m-3 max-w-sm bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700"
  >
    <router-link :to="{ name: 'product_details', params: { id: product.id } }">
      <img class="p-2 rounded-t-lg w-[300px]" :src="product.image_url" alt="product image" />
    </router-link>
    <div class="px-5 pb-5">
      <h5 class="text-xl font-semibold tracking-tight text-gray-900 dark:text-white">
        <router-link :to="{ name: 'product_details', params: { id: product.id } }">
          {{ product.title }}
        </router-link>
      </h5>
      <div class="flex items-center justify-between">
        <span class="text-3xl font-bold text-gray-900 dark:text-white"
          >${{ product.price }}</span
        >
        <a
          @click.prevent="handAddToCart"
          class="text-white bg-purple-600 hover:bg-purple-800 focus:ring-4 focus:outline-none focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-800 cursor-pointer"
        >
          Add to cart
        </a>
      </div>
    </div>
  </div>
</template>

<script>
import { useCartStore } from '@/stores/cart'
import { useAuthStore } from '@/stores/auth'
import { useFlash } from '@/composables/useFlash'
import router from "../router";
export default {
  name: 'SingleProduct',
  props:['product'],
  data() {
    return {
      userStore: useAuthStore(),
      userCart: useCartStore(),
    };
  },
  methods:{
    ...useFlash(),
    handAddToCart() {
        if (this.userStore.isAuthenticated) {
          this.userCart.addItemToCart(this.product.id, 1)
        } else {
          this.flash('warning', 'Unauthenticated', 'Please sign in first!')
          setTimeout(() => {
            router.push({ path: 'login' })
          }, 1500)
        }
    }

  }
}
</script>
