<?php $singular=\Helpers\StyleHelper::instance()->snakeCase($model_name); $plural=\Helpers\StyleHelper::instance()->snakeCase($name); ?>
<form action="<?= ('{' . '{' . '@' . 'create_' . $model_name . '}' . '}') ?>" method="post">
<?php foreach (($model->schema()?:[]) as $field_name=>$field): ?>
    <?php if ($field['pkey']): ?>
    
        <?= ('<' . 'check if="{' . '{' . '@' . $singular . '[' . $field_name . '] }' . '}">') ?><?= ('<') ?>input type="hidden" name="<?= ('{' . '{@'. $singular . '[\'' . $field_name . '\']}' . '}') ?>" <?= ('>') ?><?= ('<') ?>/check<?= ('>')."
" ?>
    
    <?php else: ?>
        <label for="<?= ($field_name) ?>"><?= ($field_name) ?></label>
        <input type="<?= (['int' => 'number', 'bool' => 'checkbox', 'string' => 'text' ][$model->db->type($field['pdo_type'])]) ?>" name="<?= ($field_name) ?>" id="<?= ($field_name) ?>" value="<?= ('{' . '{') ?> <?= ($singular . '[\'' . $field_name . '\']') ?> <?= ('}' . '}') ?>'" >
    
    <?php endif; ?>
<?php endforeach; ?>
</form>