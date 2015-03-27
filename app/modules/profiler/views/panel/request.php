<h1>Request</h1>

<h2>Request GET Parameters</h2>

<?php $parameters = $collector->getRequestQuery(); ?>
<?php if ($parameters->all()) : ?>
    <?php include(__DIR__.'/../bag.php'); ?>
<?php else : ?>
    <p>
        <em>No GET parameters</em>
    </p>
<?php endif; ?>

<h2>Request POST Parameters</h2>

<?php $parameters = $collector->getRequestRequest(); ?>
<?php if ($parameters->all()) : ?>
    <?php include(__DIR__.'/../bag.php'); ?>
<?php else : ?>
    <p>
        <em>No POST parameters</em>
    </p>
<?php endif; ?>

<h2>Request Attributes</h2>

<?php $parameters = $collector->getRequestAttributes(); ?>
<?php if ($parameters->all()) : ?>
   <?php include(__DIR__.'/../bag.php'); ?>
<?php else : ?>
    <p>
        <em>No attributes</em>
    </p>
<?php endif; ?>

<h2>Request Cookies</h2>

<?php $parameters = $collector->getRequestCookies(); ?>
<?php if ($parameters->all()) : ?>
    <?php include(__DIR__.'/../bag.php'); ?>
<?php else : ?>
    <p>
        <em>No cookies</em>
    </p>
<?php endif; ?>

<h2>Request Headers</h2>
<?php $parameters = $collector->getRequestHeaders(); ?>
<?php include(__DIR__.'/../bag.php'); ?>

<h2>Request Content</h2>

<?php if ($collector->getContent() == false) : ?>
    <p><em>Request content not available (it was retrieved as a resource).</em></p>
<?php elseif ($collector->getContent()) : ?>
    <pre><?php echo $collector->getContent(); ?></pre>
<?php else : ?>
    <p><em>No content</em></p>
<?php endif; ?>

<h2>Request Server Parameters</h2>
<?php $parameters = $collector->getRequestServer(); ?>
<?php include(__DIR__.'/../bag.php'); ?>

<h2>Response Headers</h2>
<?php $parameters = $collector->getResponseHeaders(); ?>
<?php include(__DIR__.'/../bag.php'); ?>

<h2>Session Attributes</h2>

<?php if ($collector->getSessionAttributes()) : ?>
    <table class="pf-table">
        <thead>
            <tr>
                <th scope="col" style="width: 300px;">Key</th>
                <th scope="col">Value</th>
            </tr>
        </thead>
        <tbody>
            <?php $attributes = $collector->getSessionAttributes(); ?>
            <?php ksort($attributes); ?>
            <?php foreach ($attributes as $key => $value) : ?>
                <tr>
                    <td><?php echo $this->escape($key) ?></td>
                    <td><?php echo $this->escape(json_encode($value, 64 | 256)) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else : ?>
    <p>
        <em>No session attributes</em>
    </p>
<?php endif; ?>
