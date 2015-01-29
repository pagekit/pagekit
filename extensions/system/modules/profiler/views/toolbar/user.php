<a <?php if ($collector->getUser()) echo 'class="pf-parent" '; ?> title="User">
    <div class="pf-icon pf-icon-auth"></div>
    <?php if ($collector->getUser()) : ?>
    <?php echo $collector->getUser() ?>
    <?php elseif ($collector->isEnabled()) : ?>
        You are not authenticated.
    <?php else : ?>
        Authentication is disabled.
    <?php endif ?>
</a>

<?php if ($collector->getUser()) : ?>
<div class="pf-dropdown">

    <table class="pf-table pf-table-dropdown">
        <tbody>
            <tr>
                <td>Username</td>
                <td><?php echo $collector->getUser() ?></td>
            </tr>
            <tr>
                <td>Roles</td>
                <td><?php echo json_encode($collector->getRoles()) ?></td>
            </tr>
            <tr>
                <td>Authenticated</td>
                <td><?php echo $collector->isAuthenticated() ? 'yes' : 'no' ?></td>
            </tr>
            <tr>
                <td>Class</td>
                <td><?php echo $collector->getUserClass() ?: '-' ?></td>
            </tr>
        </tbody>
    </table>

</div>
<?php endif ?>