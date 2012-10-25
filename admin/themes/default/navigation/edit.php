<?php
queue_js_file(array('jquery.mjs.nestedSortable', 'navigation'));
$pageTitle = __('Edit Navigation');
echo head(array('title'=>$pageTitle, 'content_class' => 'vertical-nav', 'bodyclass'=>'settings primary')); ?>

<form action="<?php echo $this->form->getAction() ?>"
      enctype="<?php echo $this->form->getEnctype() ?>"
      method="<?php echo $this->form->getMethod() ?>"
      id="<?php echo $this->form->getId() ?>"
      class="<?php echo $this->form->getAttrib('class') ?>" >

<?php echo common('appearance-nav'); ?>

<?php echo flash(); ?>

<section class="seven columns alpha">

<h2><?php echo __('Navigation'); ?></h2>

    <p class="description"><?php echo __('Check the links you would like to display in the main navigation. You can click and drag the links into your preferred display order.'); ?></p>

    <?php echo $this->form->displayNavigationLinks(); ?>
    
    <?php echo $this->form->getElement(Omeka_Form_Navigation::HIDDEN_ELEMENT_ID); ?>

        <div class="add-new-item"><?php echo __('Add a Link to the Navigation'); ?></div>
        
        <div class="drawer-contents">

            <label for="new_nav_link_label"><?php echo __('Link Label'); ?></label>
            <input type="text" id="new_nav_link_label" name="new_nav_link_label" />
            <label for="new_nav_link_uri"><?php echo __('Link URI'); ?></label>
            <input type="text" id="new_nav_link_uri" name="new_nav_link_uri" />
            <a href="" id="new_nav_link_button_link" class="blue button"><?php echo __('Add Link'); ?></a>
        
        </div>
    
</section>

<section class="three columns omega">

    <div id="save" class="panel">
        <?php echo $this->formSubmit('submit', __('Save Changes'), array('class'=>'submit big green button')); ?>
        <?php echo $this->form->getDisplayGroup(Omeka_Form_Navigation::HOMEPAGE_SELECT_DISPLAY_ELEMENT_ID); ?>
    </div>

</section>

</form>
<script type="text/javascript">
    Omeka.addReadyCallback(Omeka.Navigation.updateNavLinkEditForms);
    Omeka.addReadyCallback(Omeka.Navigation.addNewNavLinkForm);
    Omeka.addReadyCallback(Omeka.Navigation.updateForNewLinks);
    Omeka.addReadyCallback(Omeka.Navigation.setUpFormSubmission);
</script>
<?php echo foot(); ?>
