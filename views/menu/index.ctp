<div class="menu-sections index">

<?php echo $cpanel->newSectionLink(__('New Section', true), array('class' => 'new')) ?>

<?php echo $tree->generate($sections, array('element' => 'menu/menu_sections_nested_list', 'class' => 'data-tree', 'type' => 'ol')) ?>

</div>