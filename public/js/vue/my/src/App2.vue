<template>
  <img alt="Vue logo" src="./assets/logo.png">
  <HelloWorld msg="Welcome to Your Vue.js App"/>
  <Banzaii banzaii="Banzaii!!!" vonzaii="Vonzaii!!!"/>
</template>

<script>
import HelloWorld from './components/HelloWorld.vue'
import Banzaii from './components/Banzaii.vue'

export default {
  name: 'App2',
  components: {
    HelloWorld,
    Banzaii
  }
}
</script>

<style>
#app2 {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  margin-top: 60px;
}
</style>
