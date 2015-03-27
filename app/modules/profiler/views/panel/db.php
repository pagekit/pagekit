<h1>Queries</h1>

<?php if (!$collector->getQueryCount()) : ?>
    <p>
        <em>No queries.</em>
    </p>
<?php else : ?>

        <?php foreach ($collector->getQueries() as $i => $query) : ?>

            <pre><code><?php echo $query['sql'] ?></code></pre>

            <p class="pf-submenu">
                <span><?php echo printf('%0.2f', $query['executionMS'] * 1000) ?> ms</span>
                <span><?php echo json_encode($query['params'], 64 | 256) ?></span>
                <span data-toggler="<?php echo $i ?>-callstack">Callstack</span>
                <?php if (isset($query['explain'])) : ?>
                <span data-toggler="<?php echo $i ?>-explain">Explain</span>
                <?php endif ?>
            </p>

            <div id="<?php echo $i ?>-callstack" style="display: none;"><?php echo nl2br($query['callstack']) ?></div>

            <?php if (isset($query['explain'])) : ?>
            <div id="<?php echo $i ?>-explain" style="display: none;">

                <table class="pf-table pf-table-dropdown">
                    <thead>
                        <tr>
                            <?php foreach (array_keys($query['explain'][0]) as $label) : ?>
                            <th><?php echo $label ?></th>
                            <?php endforeach ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($query['explain'] as $row) : ?>
                        <tr>
                            <?php foreach ($row as $item) : ?>
                            <td><?php echo json_encode($item) ?></td>
                            <?php endforeach ?>
                        </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>

            </div>
            <?php endif ?>

        <?php endforeach ?>

    <script>

    (function() {

        var toggler = document.querySelector("#pk-profiler [data-panel='db']").querySelectorAll('[data-toggler]');

        [].forEach.call(toggler, function(el) {
            el.addEventListener('click', function() {
                var totoggle = document.getElementById(el.getAttribute('data-toggler'));

                [].forEach.call(toggler, function(toggle) {
                    if(toggle!=el) document.getElementById(toggle.getAttribute('data-toggler')).style.display = "none";
                });

                totoggle.style.display = totoggle.style.display=="none" ? "block" : "none";
            });
        });

        [].forEach.call(document.querySelectorAll("#pk-profiler [data-panel='db'] pre code"), function(el) {
            hljs.highlightBlock(el);
        });

    })();

    </script>
<?php endif ?>
