<template>
  <div class="hello">
    <p class="banzaii">{{ banzaii }}</p>
    <p class="banzaii">{{ vonzaii }}</p>
    <p class="banzaii">{{ color }} </p>
    <p class="banzaii">{{ shmite }} </p>
  </div>
</template>

<script>
export default {
  name: 'Banzaii',
  props: {
    banzaii: String,
    vonzaii: String
  },
  data: function() {
    return {
      color: 'white',
      shmite: 'shmite'
    }
  },
}
</script>
<!-- Add "scoped" attribute to limit CSS to this component only -->

<style scoped>
h3 {
  margin: 40px 0 0;
}
ul {
  list-style-type: none;
  padding: 0;
}
li {
  display: inline-block;
  margin: 0 10px;
}
a {
  color: #42b983;
}
.banzaii {
  font-family:Verdana, Geneva, Tahoma, sans-serif;
  font-style: italic;
  font-size: 16pt;
}
</style>