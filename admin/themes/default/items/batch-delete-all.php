<?php
$title = __('Batch Delete All Searched Items');
if (!$isPartial):
    echo head(array(
        'title' => $title,
        'bodyclass' => 'items batch-edit',
    ));
endif;
?>
<div title="<?php echo $title; ?>">

<form id="batch-edit-form" action="<?php echo html_escape(url('items/batch-edit-save')); ?>" method="post" accept-charset="utf-8">
    <section class="seven columns alpha">
        <fieldset class="panel">
            <?php
            $filters = item_search_filters($params);
            if ($filters):
                $message = __('All %d items matching the search filters above will be deleted.', $totalRecords);
            else:
                $message = __('All %d items will be deleted.', $totalRecords);
            endif;
            echo $filters;
            ?>
            <p><?php echo $message; ?></p>
            <p><?php echo __('Deletions will be processed in the background item by item.'); ?></p>
            <?php
            echo $this->formHidden('all', true);
            echo $this->formHidden('params', json_encode($params));
            ?>
        </fieldset>
        <input type="hidden" name="delete" value="1">
    </section>

    <section class="three columns omega">
        <div  id="save" class="panel">
            <input type="submit" class="big red button" value="<?php echo __('Delete Items'); ?>">
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
