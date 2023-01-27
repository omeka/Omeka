<?php echo $this->form('search-form', $options['form_attributes']); ?>
    <?php 
        echo $this->formText('query', $filters['query'], array(
            'title' => __('Query'), 
            'aria-label' => __('Query'), 
            'aria-labelledby' => 'search-form query'
        )); 
    ?>
    <?php if ($options['show_advanced']): ?>
    <button id="advanced-search" type="button" class="show-advanced button" aria-label="<?php echo __('Options'); ?>" title="<?php echo __('Options'); ?>" aria-labelledby="search-form advanced-search">
        <span class="icon" aria-hidden="true"></span>
    </button>
    <div id="advanced-form">
        <fieldset id="query-types">
            <legend><?php echo __('Search using this query type:'); ?></legend>
            <?php echo $this->formRadio('query_type', $filters['query_type'], null, $query_types); ?>
        </fieldset>
        <?php if ($record_types): ?>
        <fieldset id="record-types">
            <legend><?php echo __('Search only these record types:'); ?></legend>
            <?php foreach ($record_types as $key => $value): ?>
            <?php echo $this->formCheckbox('record_types[]', $key, array('checked' => in_array($key, $filters['record_types']), 'id' => 'record_types-' . $key)); ?> <?php echo $this->formLabel('record_types-' . $key, $value);?><br>
            <?php endforeach; ?>
        </fieldset>
        <?php elseif (is_admin_theme()): ?>
            <p><a href="<?php echo url('settings/edit-search'); ?>"><?php echo __('Go to search settings to select record types to use.'); ?></a></p>
        <?php endif; ?>
        <p><?php echo link_to_item_search(__('Advanced Search (Items only)')); ?></p>
    </div>
    <?php else: ?>
        <?php echo $this->formHidden('query_type', $filters['query_type']); ?>
        <?php foreach ($filters['record_types'] as $type): ?>
        <?php echo $this->formHidden('record_types[]', $type, array('id' => '')); ?>
        <?php endforeach; ?>
    <?php endif; ?>
    <?php 
        echo $this->formButton('submit_search', $options['submit_value'], array(
            'type' => 'submit', 
            'title' => __('Submit'), 
            'class' => 'button', 
            'content' => '<span class="icon" aria-hidden="true"></span>', 
            'escape' => false,
            'aria-label' => __('Submit'),
            'aria-labelledby' => 'search-form submit_search'
            )
        ); 
    ?>
</form>
