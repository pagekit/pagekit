Vue.component('blog-post', {

    inherit: true,

    watch: {
        'node.data.post': function(id) {
            this.$set('node.data.url', '@blog/id?id='+id)
        }
    }

});
