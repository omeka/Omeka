    <div class="table-actions">
        <?php if(!empty($plugins['new'])) :?>
        <ul class="quick-filter-wrapper">
            <li><a href="#" tabindex="0"><?php echo __('Newly Added'); ?></a>
            <ul class="dropdown">
                <li><span class="quick-filter-heading"><?php echo __('Newly Added') ?></span></li>
                <?php foreach($plugins['new'] as $id=>$plugin): ?>
                <li><a href="#<?php echo $id; ?>"><?php echo $plugin->getDisplayName(); ?></a></li>
                <?php endforeach; ?>
            </ul>
            </li>
        </ul>    
        <?php endif; ?>    
        <?php if(!empty($plugins['needs-attention'])) :?>
        <ul class="quick-filter-wrapper">
            <li><a href="#" tabindex="0"><?php echo __('Needs Action'); ?></a>
            <ul class="dropdown">
                <li><span class="quick-filter-heading"><?php echo __('Needs Action') ?></span></li>
                <?php foreach($plugins['needs-attention'] as $id=>$plugin): ?>
                <li><a href="#<?php echo $id; ?>"><?php echo $plugin->getDisplayName(); ?></a></li>
                <?php endforeach; ?>
            </ul>
            </li>
        </ul>    
        <?php endif; ?>
        <?php if(!empty($plugins['active'])): ?>
        <a href="#active" class="small green button"><?php echo __('Active'); ?></a>
        <?php endif; ?>
        <?php if(!empty($plugins['inactive'])): ?>
        <a href="#inactive" class="small blue button"><?php echo __('Inactive'); ?></a>
        <?php endif; ?>
        <?php if(isset($plugins['uninstalled'])): ?>
        <a href="#uninstalled" class="small red button"><?php echo __('Uninstalled'); ?></a>
        <?php endif; ?>
        <?php if($topLink):?>
        <a href="#">Top</a>
        <?php endif; ?>
    </div>