<template>

    {{> settings}}

    <div class="uk-form-row">

        <label for="form-post" class="uk-form-label">{{ 'Post' | trans }}</label>
        <div class="uk-form-controls">
            <select class="uk-form-width-large" v-model="node.data.variables.id">
                <option value="">- {{ 'Select Post' | trans }} -</option>
            </select>
        </div>

    </div>

</template>

<script>

    module.exports = {

        paramAttributes: ['node', 'form', 'type'],

        section: {
            name: 'blog-post',
            label: 'Settings',
            priority: 0,
            active: 'blog-post'
        }

    };

</script>
