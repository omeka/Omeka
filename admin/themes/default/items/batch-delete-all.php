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

<form id="batch-edit-form" action="<?php echo html_escape(url('items/batch-edit-all-save')); ?>" method="post" accept-charset="utf-8">
    <section class="seven columns alpha">
        <fieldset class="panel">
            <h2><?php echo __('Search Filters'); ?></h2>
            <?php if ($params):
                echo item_search_filters($params); ?>
            <p class="explanation"><?php echo __('All items matching search filters above will be deleted [%d].', $totalRecords); ?></p>
            <?php else: ?>
            <p><?php echo __('No search filter.'); ?></p>
            <p class="explanation"><?php echo __('All items of the base will be deleted [%d].', $totalRecords); ?></p>
            <?php endif; ?>
            <p class="explanation"><?php echo __('Deletions will be processed in the background item by item, so you should check logs for success and errors.'); ?></p>
            <?php
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
