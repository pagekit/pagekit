<table class="pf-table">
    <thead>
        <tr>
            <th>Key</th>
            <th>Value</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($parameters as $key => $value) : ?>
            <tr>
                <td><?php echo $this->escape($key) ?></td>
                <td><?php echo $this->escape(trim(json_encode($value, 64 | 256), '[]')) ?></td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>
