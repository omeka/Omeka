<?php echo $this->form('search-form', $options['form_attributes']); ?>
    <?php echo $this->formText('query', $request['query']); ?>
    <?php if ($options['show_advanced']): ?>
    <p><?php echo __('Search using this query type:'); ?></p>
    <?php echo $this->formRadio('query_type', $request['queryType'], null, $validQueryTypes); ?>
    <p><?php echo __('Search only these record types:'); ?></p>
    <?php foreach ($validRecordTypes as $key => $value): ?>
    <?php echo $this->formCheckbox('record_types[]', $key, in_array($key, $request['recordTypes']) ? array('checked' => true) : null); ?> <?php echo $value; ?><br>
    <?php endforeach; ?>
    <?php endif; ?>
    <?php echo $this->formSubmit(null, $options['submit_value']); ?>
</form>
