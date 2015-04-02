<div class="uk-form-row" v-component="blog-post" inline-template>

    <label for="form-post" class="uk-form-label">{{ 'Post' | trans }}</label>
    <div class="uk-form-controls">

        <select v-model="node.data.post">
            <option value="">- {{ 'Select Post' | trans }} -</option>
            <?php foreach($posts as $id => $post) : ?>
            <option value="<?= $id ?>"><?= $post ?></option>
            <?php endforeach ?>
        </select>
    </div>

</div>
