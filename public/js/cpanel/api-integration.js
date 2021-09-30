const ApiIntegration = {
  template: '<div><div v-html="htmlcontent"></div></div>',
  data: function () {
    return {
      htmlcontent: ''
    }
  },
  methods: {
    getContent() {
      let requestUrl = '/control-panel/api-integration';
      const headers = { 'X-Requested-With': 'XMLHttpRequest' };
      axios
          .post(requestUrl,
            Qs.stringify({
              page : this.$route.name
            }),{headers})
          .then(response => {
            if (response.data.data === true) {
              location.reload();
            } else {
              this.htmlcontent = response.data.data;
            }
          })
    }
  },
  created: function(){
    $('.main__loader').show();
    this.getContent();
  },
  updated: function(){
    $('.main__loader').hide();
  }
}
