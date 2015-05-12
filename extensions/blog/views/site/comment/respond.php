<div class="js-respond uk-margin">

    <h3><?= __('Leave a comment') ?></h3>

    <?php if ($app['user']->hasAccess('blog: post comments')) : ?>

        <form class="uk-form uk-form-stacked" method="post" action="<?= $view->url('@blog/site/comment', ['post_id' => $post->getId()]) ?>">

            <?php if ($app['user']->isAuthenticated()) : ?>

                <p><?= __('Logged in as %name%', ['%name%' => $app['user']->getName()]) ?></p>

            <?php else : ?>

                <p><?= __('Your email address will not be published.') ?></p>

                <?php $req = $blog->config('comments.require_name_and_email') ?>

                <div class="uk-form-row">
                    <label for="form-name" class="uk-form-label"><?= __('Name') ?></label>
                    <div class="uk-form-controls">
                        <input id="form-name" class="uk-form-width-large" type="text" name="comment[author]"<?= $req ? ' required' : '' ?>>
                    </div>
                </div>
                <div class="uk-form-row">
                    <label for="form-email" class="uk-form-label"><?= __('Email') ?></label>
                    <div class="uk-form-controls">
                        <input id="form-email" class="uk-form-width-large" type="email" name="comment[email]"<?= $req ? ' required' : '' ?>>
                    </div>
                </div>

            <?php endif ?>

            <div class="uk-form-row">
                <label for="form-comment" class="uk-form-label"><?= __('Comment') ?></label>
                <div class="uk-form-controls">
                    <textarea id="form-comment" class="uk-form-width-large" name="comment[content]" required rows="10"></textarea>
                </div>
            </div>

            <p>
                <input class="uk-button uk-button-primary" type="submit" value="<?= __('Submit comment') ?>" accesskey="s">
                <a class="js-cancel-reply" href="#"><?= __('Cancel') ?></a>
            </p>

            <input type="hidden" name="comment[parent_id]" value="0">

            <?php $view->token() ?>

        </form>

    <?php elseif ($app['user']->isAuthenticated()) : ?>

        <p><?= __('You are not allowed to post comments.') ?></p>

    <?php else : ?>

        <p><?= __('Please login to leave a comment.') ?></p>

    <?php endif ?>

</div>
