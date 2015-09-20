## vue-validator

Validator plugin for Vue.js (v.0.11).

The plugin ships with a couple of built-in validators.

* required
* pattern
* minLength
* maxLength
* min
* max
* numeric
* integer
* digits
* alpha
* alphaNum
* email
* url
* minLength
* maxLength
* length

It is model independent and uses the actual element value instead.

## Usage

The validators are applied through the `v-valid` directive. Any change to the content will trigger form validation.

The validator `valid` filter can be added to any form `submit` event. The form validation state is published to the current scope using the forms `name` attribute. Similarly, the `name` attributes of the `input` fields specify their properties.

```html
<form name="form" v-on="submit: save | valid">

    <input type="text" name="name" v-valid="required, alpha">
    <span v-show="form.name.required">Name cannot be blank.</span>
    <span v-show="form.name.alpha">Name contains invalid characters.</span>
    <span v-show="form.name.invalid">Name is invalid.</span>

</form>
```

### Custom validators

Custom validators may be added globally:

```javascript
Vue.validator.types['myUrl'] = function(value) {
    // custom URL validation
}
```

or scoped to your component definition:

```javascript
new Vue({

    validators: {

        myUrl: function(value) {
            // custom URL validation
        }

    }

})
```

It can then be used like this:

```html
<input type="text" name="url" v-valid="myUrl" />
<span v-show="form.url.myUrl">URL is invalid.</span>
```
