<script>
export default {
    props: ['cartItem','cartId'],
}




</script>

<template>
    <div class="flex items-center hover:bg-gray-100 -mx-8 px-6 py-5">
        <div class="flex w-2/5">
          <div class="w-50">
            <img class="h-24 w-24" :src="this.cartItem.product.image_url" alt="">
          </div>
          <div class="flex flex-col justify-between ml-4 flex-grow">
            <span class="font-bold">{{ this.cartItem.product.title }}</span>
            <span class="font-normal text-sm">{{ this.cartItem.product.description }}</span>
          </div>
        </div>

        <div class="flex justify-center w-1/5">
            <span class="font-normal text-sm">{{ this.cartItem.quantity }}</span>
        </div>
        <span class="text-center w-1/5 font-semibold text-sm">$ {{ this.cartItem.product.price }}</span>
        <span class="text-center w-1/5 font-semibold text-sm">$ {{ (this.cartItem.product.price) * (this.cartItem.quantity) }}</span>
      </div>
</template>

<style scoped>

</style>>
