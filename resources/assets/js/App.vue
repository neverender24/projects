<template>
    <div class="container-scroller">
        <nav-bar></nav-bar>
        <div class="container-fluid">
            <router-view></router-view>
        </div>
    </div>
</template>

<script>
import NavBar from "./components/NavBar.vue";
import SideBar from "./components/SideBar.vue";

export default {
    components: {
        NavBar,
        SideBar
    }
};
</script>
