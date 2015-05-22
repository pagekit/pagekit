# vue-resource

Resource plugin for Vue.js (v0.11).

The plugin provides services for making web requests and handle responses using a XMLHttpRequest or JSONP.

## Usage

### HTTP
```javascript

  new Vue({

      ready: {

        // GET request
        this.$http.get('/someUrl', function (data, status, request) {

            // set data on vm
            this.$set('someData', data)

        }).error(function (data, status, request) {
            // handle error
        })

      }

  })

```

### Resource
```javascript

  new Vue({

      ready: {

        var resource = this.$resource('someItem/:id');

        // get item
        resource.get({id: 1}, function (data, status, request) {
            this.$set('item', item)
        })

        // save item
        resource.save({id: 1}, {item: this.item}, function (data, status, request) {
            // handle success
        }).error(function (data, status, request) {
            // handle error
        })

        // delete item
        resource.delete({id: 1}, function (data, status, request) {
            // handle success
        }).error(function (data, status, request) {
            // handle error
        })

      }

  })


```
