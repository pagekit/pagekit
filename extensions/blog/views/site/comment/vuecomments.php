
<div id="comments" class="js-comments uk-margin">

    <div v-if="comments.access.view">

        <div v-if="comments.entries.length">

            <h2 class="uk-h3">{{ 'Comments' | trans }} ({{ comments.entries.length }})</h2>

            <ul class="uk-comment-list">

                <li id="comment-{{ comment.id }}" v-repeat="comment: comments.entries">

                    <article class="uk-comment">

                        <header class="uk-comment-header">

                            <img class="uk-comment-avatar uk-border-circle" width="50" height="50" alt="" v-gravatar="comment.email">

                            <h3 class="uk-comment-title">{{ comment.author }}</h3>

                            <ul class="uk-comment-meta uk-subnav uk-subnav-line">
                                <li>
                                    <time>{{ comment.created | date 'medium' }}</time>
                                </li>
                                <li>
                                    <a href="#comment-{{ comment.id }}">#</a>
                                </li>
                            </ul>

                        </header>

                        <div class="uk-comment-body">

                            <p>{{ comment.content }}</p>

                            <p><a class="js-reply" href="#comment-form" v-on="click: replyTo(comment.id)" v-if="comments.access.post"><i class="uk-icon-reply"></i> {{ 'Reply' | trans }}</a></p>

                        </div>

                    </article>

                </li>
            </ul>

        </div>

        <div id="comment-0" class="uk-margin" v-if="comments.enabled">

            <form id="comment-form" class="uk-form uk-form-stacked uk-margin" method="post" action="<?= $view->url('@blog/site/comment', ['post_id' => $post->getId()]) ?>" v-on="submit: save" v-if="comments.access.post">

                <h3 v-if="!newcomment.parent_id">{{ 'Leave a comment' | trans }}</h3>
                <h3 v-if="newcomment.parent_id">{{ 'Leave a Reply' | trans }}</h3>

                <p v-if="comments.user"><?= __('Logged in as %name%', ['%name%' => $app['user']->getName()]) ?></p>

                <div v-if="!comments.user">

                    <p>{{ 'Your email address will not be published.' | trans }}</p>

                    <div class="uk-form-row">
                        <label for="form-name" class="uk-form-label">{{ 'Name' | trans }}</label>
                        <div class="uk-form-controls">
                            <input id="form-name" class="uk-form-width-large" type="text" v-model="newcomment.author" v-attr="required: comments.requireinfo">
                        </div>
                    </div>

                    <div class="uk-form-row">
                        <label for="form-email" class="uk-form-label">{{ 'Email' | trans }}</label>
                        <div class="uk-form-controls">
                            <input id="form-email" class="uk-form-width-large" type="email" v-model="newcomment.email" v-attr="required: comments.requireinfo">
                        </div>
                    </div>

                </div>

                <div class="uk-form-row">
                    <label for="form-comment" class="uk-form-label">{{ 'Comment' | trans }}</label>
                    <div class="uk-form-controls">
                        <textarea class="uk-form-width-large" v-model="newcomment.content" required rows="10"></textarea>
                    </div>
                </div>

                <p>
                    <input class="uk-button uk-button-primary" type="submit" value="<?= __('Submit comment') ?>" accesskey="s">
                    <a v-if="(newcomment.parent_id !== 0)" v-on="click: replyTo(0)">{{ 'Cancel' | trans }}</a>
                </p>

            </form>


            <p v-if="!comments.access.post && comments.user">{{ 'You are not allowed to post comments.' | trans }}</p>
            <p v-if="!comments.access.view && !comments.user">{{ 'Please login to leave a comment.' | trans }}</p>

        </div>

        <p v-if="!comments.enabled">{{ 'Comments are closed.' | trans }}</p>

    </div>

    <p v-if="!comments.access.view && comments.user">{{ 'You are not allowed to view comments.' | trans }}</p>
    <p v-if="!comments.access.view && !comments.user">{{ 'Please login to view comments.' | trans }}</p>

</div>


<script>

    (function($, element){

        if (!element) {
            return;
        }

        var comments = new Vue({

            el: element,

            data: {
                comments: $comments,
                newcomment : {
                    content: '',
                    parent_id: 0
                }
            },


            methods: {

                save: function (e) {

                    e.preventDefault();

                    this.$http.post($(e.target).attr('action'), { comment:this.newcomment, _csrf: $pagekit.csrf }, function(comment) {

                        this.comments.entries.push(comment);
                        this.newcomment.content = '';

                    }, 'json');
                },

                replyTo: function(id) {

                    var form = document.getElementById('comment-form'),
                        comment = document.getElementById('comment-'+id);

                    comment.appendChild(form);

                    this.newcomment.parent_id = id;

                }
            }
        });


    })(jQuery, document.getElementById('comments'));



</script>
