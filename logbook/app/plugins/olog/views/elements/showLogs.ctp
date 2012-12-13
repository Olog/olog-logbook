<div class="logs index">
    <div id="searching">
        <?php echo $this->element('searching'); ?>
    </div>
    <div id="paginateTable">
         <?php echo $this->element('paginateTable'); ?>
    </div>
    <span style="float: right;" id="version"><?php $dbinfo = get_class_vars('DATABASE_CONFIG');
         $version = $dbinfo['olog']['version']; 
         echo $version ?>
    </span>
</div>

<?php echo $this->element('component_action'); ?>

