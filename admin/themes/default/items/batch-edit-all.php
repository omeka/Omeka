<?php
$title = __('Batch Edit All Searched Items');
if (!$isPartial):
    echo head(
        array(
            'title' => $title,
            'bodyclass' => 'items batch-edit',
        )
    );
endif;
?>
<div title="<?php echo $title; ?>">

<form id="batch-edit-form" action="<?php echo html_escape(url('items/batch-edit-save')); ?>" method="post" accept-charset="utf-8">
    <section class="seven columns alpha">
        <fieldset class="flash">
            <?php
            $filters = item_search_filters($params);
            if ($filters):
                $message = __('Changes will be applied to all %d items matching the search filters above.', $totalRecords);
            else:
                $message = __('Changes will be applied to all %d items.', $totalRecords);
            endif;
            echo $filters;
            ?>
            <p><?php echo $message; ?></p>
            <p><?php echo __('Changes will be processed in the background item by item.'); ?></p>
            <?php
            echo $this->formHidden('all', true);
            echo $this->formHidden('params', json_encode($params));
            ?>
        </fieldset>

        <?php echo common('batch-edit-common', array(), 'items'); ?>

        <?php fire_plugin_hook('admin_items_batch_edit_form', array('view' => $this)); ?>
    </section>

    <section class="three columns omega">
        <div id="save" class="panel">
            <input type="submit" class="big green button" value="<?php echo __('Save Changes'); ?>">
        </div>
    </section>

    <?php
    $hash = new Zend_Form_Element_Hash('batch_edit_hash');
    $hash->removeDecorator('Label');
    $hash->removeDecorator('HtmlTag');
    echo $hash;
    ?>
</form>

</div>
<?php
if (!$isPartial):
    echo foot();
endif;
