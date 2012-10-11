<div class="field two columns alpha">
    <?php echo $this->formLabel('collectors', __('Collectors')); ?>
</div>
<div class="inputs five columns omega">
    <?php echo $this->formTextarea('collectors', $collection->collectors, array('rows' => '10', 'cols' => '60')); 
?>
</div>