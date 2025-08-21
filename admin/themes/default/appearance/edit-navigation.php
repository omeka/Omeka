<?php
queue_js_file(['vendor/jquery.nestedSortable', 'navigation']);

$pageTitle = __('Appearance');
echo head(['title'=>$pageTitle, 'bodyclass'=>'settings']); ?>

<?php echo common('appearance-nav'); ?>
<?php echo flash(); ?>
<form action="<?php echo $this->form->getAction() ?>"
      enctype="<?php echo $this->form->getEnctype() ?>"
      method="<?php echo $this->form->getMethod() ?>"
      id="<?php echo $this->form->getId() ?>"
      class="<?php echo $this->form->getAttrib('class') ?>" >
<section class="seven columns alpha">
    <p class="explanation"><?php echo __('Check the links to display them ' 
    . 'in the main navigation. Click and drag the links into the preferred ' 
    . 'display order.'); ?></p>
    <?php echo $this->form->displayNavigationLinks(); ?>
    <?php echo $this->partial('settings/edit-navigation-link.php', ['template' => true]); ?>
    <?php echo $this->form->getElement(Omeka_Form_Navigation::HIDDEN_ELEMENT_ID); ?>
        <div class="add-new" id="add-new-heading"><?php echo __('Add a Link to the Navigation'); ?></div>
        <div id="add-new-options" class="drawer-contents opened">
            <div id="new-link-success" class="sr-only flash" style="display: none" aria-live="polite"><?php echo __('New Link Added. Total links: '); ?><span class="link-count"></span></div>
            <label id="new_nav_link_label_label"><?php echo __('Label'); ?></label>
            <input type="text" id="new_nav_link_label" aria-labelledby="add-new-heading new_nav_link_label_label" name="new_nav_link_label" />
            <div class="flash alert" style="display: none;" id="label-required" aria-live="polite"><?php echo __('required field'); ?> <span class="sr-only"><?php echo __('Label'); ?></span></div>
            <label id="new_nav_link_uri_label"><?php echo __('URL'); ?></label>
            <input type="text" id="new_nav_link_uri" name="new_nav_link_uri" aria-labelledby="add-new-heading new_nav_link_uri_label" />
            <div class="flash alert" style="display: none;" id="uri-required" aria-live="polite"><?php echo __('required field'); ?> <span class="sr-only"><?php echo __('URI'); ?></span></div>
            <button type="button" id="new_nav_link_button_link" class="blue button"><?php echo __('Add Link'); ?></button>
        </div>
    <?php fire_plugin_hook('admin_appearance_navigation_form', ['form' => $form, 'view' => $this]); ?>
</section>
<section class="three columns omega">
    <div id="save" class="panel">
        <?php echo $this->formSubmit('submit', __('Save Changes'), ['class'=>'submit full-width green button']); ?>
        <?php echo $this->form->getDisplayGroup(Omeka_Form_Navigation::HOMEPAGE_SELECT_DISPLAY_ELEMENT_ID); ?>
    </div>
    <div id="reset" class="panel">
        <a class='delete-confirm full-width red button' href='<?php echo url('appearance/reset-navigation-confirm'); ?>'><?php echo __('Reset Navigation'); ?></a>
    </div>
</section>
<?php echo $this->form->getElement('navigation_csrf'); ?>
</form>
<script type="text/javascript">
    Omeka.Navigation.labelText = <?php echo js_escape(__('Label')); ?>;
    Omeka.Navigation.urlText = <?php echo js_escape(__('URL')); ?>;
    Omeka.Navigation.visitText = <?php echo js_escape(__('View Public Page')); ?>;
    Omeka.Navigation.deleteText = <?php echo js_escape(__('Delete')); ?>;
    Omeka.addReadyCallback(Omeka.Navigation.updateNavLinkEditForms);
    Omeka.addReadyCallback(Omeka.Navigation.addNewNavLinkForm);
    Omeka.addReadyCallback(Omeka.Navigation.updateForNewLinks);
    Omeka.addReadyCallback(Omeka.Navigation.setUpFormSubmission);
    Omeka.manageDrawers('#navigation_main_list', '.main_link');
</script>
<?php echo foot(); ?>
