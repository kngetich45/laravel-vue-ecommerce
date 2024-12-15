

<template>
  <Header/>
  <div class="container mx-auto mt-10">
    <div class="flex shadow-md my-10">
      <div class="w-full bg-white px-10 py-10">
        <div class="flex justify-between border-b pb-8">
          <h1 class="font-semibold text-2xl">Orders</h1>
          <h2 class="font-semibold text-2xl"> Total {{ this.itemCount(orders) }} orders</h2>
        </div>
        <div class="flex mt-10 mb-5">
          <h3 class="font-semibold text-gray-600 text-xs uppercase w-2/5">Product Details</h3>
          <h3 class="font-semibold text-center text-gray-600 text-xs uppercase w-1/5">Quantity</h3>
          <h3 class="font-semibold text-center text-gray-600 text-xs uppercase w-1/5">Price</h3>
          <h3 class="font-semibold text-center text-gray-600 text-xs uppercase w-1/5">Total</h3>
        </div>
        <div v-if="orders.length > 0" class="w-full">
          <OrderItem v-for="cartItem in orders[0].order_items" :cartItem="cartItem" :cartId="orders[0].id" :key="cartItem.id" />
        </div>
          <div class="flex justify-between mt-5">
              <span class="font-semibold text-sm uppercase">Total Amount</span>
              <span class="font-semibold text-sm">$ {{this.subtotalAmount(orders)}}</span>
          </div>
      </div>

    </div>
  </div>
</template>
<script>
import { useOrderStore } from '@/stores/order';
import {mapState} from "pinia";
import Header from "../components/Navmenu/header.vue";
import OrderItem from "@/components/OrderItem.vue";
export default {
  name: 'OrderView',
  components:{OrderItem, Header},
  data(){
    return {
      taxValue: 18,
    }
  },
  mounted() {
    const useCart = useOrderStore()
    useCart.fetchOrderItems();
  },
  methods:{
          itemCount (cart){
            if (cart.length === 0) {
              return 0;
            } else {
              return cart[0].order_items.reduce((total, item) => {
                  return total + item.quantity
              }, 0);
            }
          },

        subtotalAmount(cart) {
          if (cart.length === 0) {
            return 0;
          } else {
            return cart[0].order_items.reduce((sum, a) => {
              return sum + (a.quantity * a.product.price)
            }, 0);
          }
        },

  },
  computed:{
    ...mapState(useOrderStore,{
      orders: "getOrders"
    }),

  }
}

</script>


