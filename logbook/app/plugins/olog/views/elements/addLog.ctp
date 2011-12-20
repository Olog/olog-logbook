<div id='logsFormAdd' align='right'>
    <img id="closeNewLog" src="<?php echo $base; ?>/img/blue-document--minus.png" title="minimize add new log" alt="minimize add new log" class="NewLog_icons" style="display:none"  />
    <img id="addNewLog" src="<?php echo $base; ?>/img/blue-document--plus.png" title="add new log" alt="add new log" class="NewLog_icons" />
</div>
<div id="logForm" class="logs form" style="display: none" >
    <?php echo $this->Form->create('log', array('id' => 'mainFormId', 'action' => 'add')); ?>
    <fieldset id='logFormFieldset'>
        <legend><?php __('Add a New Log'); ?></legend>
        <?php if (!$session->check('Auth.User.name')) { ?>
            <div id='logFormCredentials'>
                <div id="credentials">
                    <div id='logFormUsername'><?php echo $this->Form->input('username', array('label' => 'Username')); ?></div>
                    <div id='logFormPassword'><?php echo $this->Form->input('password', array('label' => 'Password')); ?></div>
                </div>
            </div>
        <?php } ?>
        <div id='logFormContainer'>
            <div id='logFormInfo'>
                <?php //echo $this->Form->input('subject', array('type' => 'hidden')); ?>
                <div id='logFormDescription'>
                    <?php echo $this->Form->input('description', array('type' => 'textarea', 'rows' => '12')); ?>
                </div>
            </div>
            <div id='logFormSelects'>
                <div id='logFormLevels'><?php echo $this->Form->input('level'); ?></div>
                <div id='logFormLogbooks'><?php echo $this->Form->input('logbooks', array('type' => 'select', 'multiple' => true, 'size' => 4, 'id' => 'logbook_select')); ?></div>
                <div id='logFormTags'><?php echo $this->Form->input('tags', array('type' => 'select', 'multiple' => true, 'size' => 4)); ?></div>
                <?php echo $form->submit('submit', array('disabled' => true)); ?>
            </div>
        </div>
        <?php echo $form->end(); ?>
    </fieldset>
</div>

<script type="text/javascript" >
    // You cannot add a new log entry unless at least one logbook has been specified
    $('#logbook_select').change(function(){
        if ($(this).val() != null) {
            $('input[type=submit]').attr('disabled', false);
        }
        else {
            $('input[type=submit]').attr('disabled', true);
        }
    });
    
    // Prevent double submitting
    $('#mainFormId').submit(function(){
        $('input[type=submit]', this).attr('enabled', 'disabled');
    });
    
    // Switch icon back and forth upon clicking
    $('#addNewLog').click(function() {
        $('#logForm').show('fast');
        $('#addNewLog').hide();
        $('#closeNewLog').show();
    });
    $('#closeNewLog').click(function() {
        $('#logForm').hide();
        $('#closeNewLog').hide();
        $('#addNewLog').show();
    });
</script>