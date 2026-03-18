<?php echo $this->form('search-form', $options['form_attributes']); ?>
    <?php 
        echo $this->formText('query', $filters['query'], [
            'title' => __('Query'), 
            'aria-label' => __('Query'), 
            'aria-labelledby' => 'search-form query'
        ]); 
    ?>
    <?php if ($options['show_advanced']): ?>
    <button id="advanced-search" type="button" aria-expanded="false" class="show-advanced has-tooltip button o-modal-trigger" data-modal-target="advanced-form">
        <span class="icon" aria-hidden="true"></span>
        <div id="advanced-search-tooltip" class="tooltip" popover="hint"><?php echo __('Search options') ?></div>
    </button>
    <dialog id="advanced-form" class="o-modal" aria-labelledby="advanced-options-heading">
        <button type="button" class="o-modal-close o-icon-close has-tooltip">
            <span class="icon" aria-hidden="true"></span>
            <div id="advanced-close-tooltip" class="tooltip" popover="hint"><?php echo __('Close') ?></div>
        </button>
        <h2 id="advanced-options-heading"><?php echo __('Search options'); ?></h2>
        <fieldset id="query-types">
            <legend><?php echo __('Search using this query type:'); ?></legend>
            <?php echo $this->formRadio('query_type', $filters['query_type'], null, $query_types); ?>
        </fieldset>
        <?php if ($record_types): ?>
        <fieldset id="record-types">
            <legend><?php echo __('Search only these record types:'); ?></legend>
            <?php foreach ($record_types as $key => $value): ?>
            <?php echo $this->formCheckbox('record_types[]', $key, ['checked' => in_array($key, $filters['record_types']), 'id' => 'record_types-' . $key]); ?> <?php echo $this->formLabel('record_types-' . $key, $value);?><br>
            <?php endforeach; ?>
        </fieldset>
        <?php elseif (is_admin_theme()): ?>
            <p><a href="<?php echo url('settings/edit-search'); ?>"><?php echo __('Go to search settings to select record types to use.'); ?></a></p>
        <?php endif; ?>
        <p><?php echo link_to_item_search(__('Advanced Search (Items only)')); ?></p>
    </dialog>
    <?php else: ?>
        <?php echo $this->formHidden('query_type', $filters['query_type']); ?>
        <?php foreach ($filters['record_types'] as $type): ?>
        <?php echo $this->formHidden('record_types[]', $type, ['id' => '']); ?>
        <?php endforeach; ?>
    <?php endif; ?>
    <?php 
        echo $this->formButton('submit_search', $options['submit_value'], [
            'type' => 'submit', 
            'title' => __('Submit'), 
            'class' => 'has-tooltip button', 
            'content' => '<span class="icon" aria-hidden="true"></span><div id="search-submit-tooltip" class="tooltip" popover="hint">' . __('Submit') . '</div>', 
            'escape' => false,
            ]
        ); 
    ?>
</form>
