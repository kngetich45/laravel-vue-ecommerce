<template>
    <Header/>
    <div>
        <ProductList></ProductList>
    </div>
</template>

<script>
import ProductList from '@/components/ProductList.vue';
import Header from "@/components/Navmenu/header.vue";
export default {
    name: 'ProductView',
    components: {Header, ProductList}
}
</script>

