## vue-validator

Form Validation plugin for Vue.js (v.0.11).

The vue-validator ships with a couple of build-in validators.

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

The validators are applied through the _v-valid_ directive. 
Any change to the content will trigger form validation.

The validator will catch any _submit_ events and in turn triggers _valid_ and _invalid_ events on the form. 
The form validation state is published to the current scope using the forms _name_ attribute. 
Similarly, the _name_ attributes of the _input_ fields specify their properties.

```
<form name="form" v-on="valid: save">

    <input type="text" name="name" v-valid="required, alpha">
    <span v-show="form.name.required">Name cannot be blank.</span>
    <span v-show="form.name.alpha">Name contains invalid characters.</span>
    <span v-show="form.name.invalid">Name is invalid.</span>
    
</form>
```

### Custom validators

Custom validators may be added globally:
 
```
Vue.validators['myUrl'] = function(value) {
    // custom URL validation
}
```
 
or scoped to your component definition:

```
new Vue({

    validators: {

        myUrl: function(value) {
            // custom URL validation
        }

    }
    
})
```

It can then be used like this:


```
    <input type="text" name="url" v-valid="myUrl" />
    <span v-show="form.url.myUrl">URL is invalid.</span>
```

## Copyright and License

Copyright [YOOtheme](http://www.yootheme.com) GmbH under the [MIT license](LICENSE.md).
