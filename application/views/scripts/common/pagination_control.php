<?php if ($this->pageCount): ?>
<ul class="pagination_list">
    <!-- First page link --> 
    <li class="pagination_first">
    <?php if ($this->first != $this->current): ?>
    <a href="<?php echo $this->url(array('page' => $this->first), null, $_GET); ?>">First</a>
    <?php else: ?>
    First
    <?php endif; ?>
    </li>
    
    <!-- Previous page link --> 
    <li class="pagination_previous">
    <?php if (isset($this->previous)): ?>
    <a href="<?php echo $this->url(array('page' => $this->previous), null, $_GET); ?>">Previous</a>
    <?php else: ?>
    Previous
    <?php endif; ?>
    </li>
    
    <!-- Numbered page links -->
    <?php foreach ($this->pagesInRange as $page): ?> 
    <?php if ($page != $this->current): ?>
    <li class="pagination_range"><a href="<?php echo $this->url(array('page' => $page), null, $_GET); ?>"><?php echo $page; ?></a></li>
    <?php else: ?>
    <li class="pagination_current"><?php echo $page; ?></li>
    <?php endif; ?>
    <?php endforeach; ?>
    
    <!-- Next page link -->
    <li class="pagination_next">
    <?php if (isset($this->next)): ?> 
    <a href="<?php echo $this->url(array('page' => $this->next), null, $_GET); ?>">Next</a>
    <?php else: ?>
    Next
    <?php endif; ?>
    </li>
    
    <!-- Last page link --> 
    <li class="pagination_last">
    <?php if ($this->last != $this->current): ?>
    <a href="<?php echo $this->url(array('page' => $this->last), null, $_GET); ?>">Last</a>
    <?php else: ?>
    First
    <?php endif; ?>
    </li>
</ul>
<?php endif; ?>
