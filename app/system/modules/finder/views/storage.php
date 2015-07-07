<?php $view->script('panel-finder') ?>

<div id="storage">
    <panel-finder root="<?= htmlentities($root) ?>" mode="<?= $mode ?>"></panel-finder>
</div>

<script>
    new Vue({el: '#storage'});
</script>
