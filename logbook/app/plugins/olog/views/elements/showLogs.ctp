<div class="logs index">
    <div id="searching">
        <?php echo $this->element('searching'); ?>
    </div>
    <div class="paging">
        <?php echo $this->Paginator->prev('<< ' . __('previous', true), array(), null, array('class' => 'disabled')); ?>
        | 	<?php echo $this->Paginator->numbers(); ?>
        |
        <?php echo $this->Paginator->next(__('next', true) . ' >>', array(), null, array('class' => 'disabled')); ?>
    </div>
    <table class="logs" cellpadding="0" cellspacing="0">
        <?php
        $i = 0;
        $j = 0;

        // If there is only one log then what is being passed is NOT an array
        if (empty($logs['logs']['log'][0])) {
            if (empty($logs['logs']['log'])) {
                echo "<div id='no_logs'>There are currently no log entries.</div>";
            } else {
                $temp = $logs['logs']['log'];
                unset($logs);
                $logs['logs']['log'][0] = $temp;
            }
        }

        foreach ($logs['logs']['log'] as $log):
            $class = null;
            if ($i++ % 2 == 0) {
                $class = ' class="altrow"';
            }
            ?>
            <tr<?php echo $class; ?> id="log_<?php echo $log['id']; ?>" >
                <td class="subject">
                    <div>
                        <div class="date_and_owner">
                            <?php echo date('d M Y H:i', strtotime($log['createdDate'])) . ', ' . $log['owner']; ?>
                            <?php if ($log['version'] > 0) { ?>
                                |&nbsp;
                                <div class="edited">
                                    <?php echo ' [edited] ' . date('d M Y H:i', strtotime($log['modifiedDate'])); ?>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="logbooks">
                            <img src="<?php echo $base; ?>/img/logbook.png" title="logbooks" alt="logbooks"/>&nbsp;<?php
                        foreach ($log['logbooks'] as $logbooks) {
                            if (isset($logbooks['name'])) {
                                echo $logbooks['name'];
                            } else {
                                $logbook_array = array();
                                foreach ($logbooks as $logbook) {
                                    if (isset($logbook['name'])) {
                                        $logbook_array[] = $logbook['name'];
                                    }
                                }
                                echo implode(", ", $logbook_array);
                            }
                        }
                            ?>
                        </div>
                        <div class="clear"></div>
                    </div>
                    <div>
                        <div class="level"><?php echo $log['level'] ?></div>
                        <div class="tag">
                            <?php
                            if (!empty($log['tags']))
                                echo '<img src="' . $base . '/img/tag-medium.png" title="tags" alt="tags">&nbsp;';
                            foreach ($log['tags'] as $tags) {
                                if (isset($tags['name'])) {
                                    echo $tags['name'];
                                } else {
                                    $log_array = array();
                                    foreach ($tags as $tag) {
                                        if (isset($tag['name'])) {
                                            $log_array[] = $tag['name'];
                                        }
                                    }
                                    echo implode(", ", $log_array);
                                }
                            }
                            ?>
                        </div>
                        <div class="clear"></div>
                    </div>
                    <div>
                        <div class="properties">
                            <?php
                            if (isset($log['properties'])) {
                                foreach ($log['properties'] as $properties) {
                                    if (isset($properties['name']) && ($properties['name'] == "Component")) {
                                        foreach ($properties['attributes']['entry'] as $entry) {
                                            if (isset($entry['key']) && ($entry['key'] == "Hierarchy")) {
                                                echo '<img id="' . $log['id'] . '_component' . '" src="' . $base . '/img/task.png" title="properties" alt="properties" />&nbsp;' . $entry['value'];
                                            }
                                        }
                                    } else {
                                        foreach ($properties as $property) {
                                            if (isset($property['name']) && ($property['name'] == "Component")) {
                                                foreach ($property['attributes']['entry'] as $entry) {
                                                    if (isset($entry['key']) && ($entry['key'] == "Hierarchy")) {
                                                        echo '<div><img id="' . $log['id'] . '_component' . '" src="' . $base . '/img/task.png" title="properties" alt="properties" />&nbsp;' . $entry['value'] . '</div>';
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
//                            echo '<div style="display:none" class="maxComponent" >' . max(array_keys($components)) . '</div>';
//                            unset($components);
                            ?>
                        </div>
                        <div class="clear"></div>
                    </div>
                    <div class='description'>
                        <?php echo (!empty($log['description']) ? nl2br(htmlentities($log['description'])) : ''); ?>
                    </div>
                    <div id="copypaste_action">
                        <?php echo $this->element('copypaste_action', array('logid' => $log['id'])); ?>
                    </div>
                    <div id="fileupload_<?php echo $log['id']; ?>" >
                        <div class="files" title="<?php echo $base; ?>/olog/uploads/index/id:<?php echo $log['id']; ?>"/></div>
                    <div id="file_action">
                        <?php echo $this->element('file_action'); ?>
                    </div>
                    <div class="placeholder">
                        <div class="action_buttons" style="display: none"><label>actions</label>
                            <form style='padding: 0px' action="<?php echo $base; ?>/olog/uploads/index/id:<?php echo $log['id']; ?>" method="POST" enctype="multipart/form-data">
                                <input type="file" name="file" id="fileItem">
                                <input type="hidden" name="id" value="<?php echo $log['id']; ?>" />
                                <a style="padding: 0px 0px 0px 20px;" href="<?php echo $base . '/' . $this->params['plugin'] . '/' . $this->params['controller'] . '/edit/' . $log['id']; ?>">
                                    <img border="0" src="<?php echo $base; ?>/img/logentry-edit.png" title="edit log" alt="edit log" />
                                </a>
                                <span style="padding: 0px 0px 0px 0px;" id="componentAdd_<?php echo $log['id']; ?>" title="<?php echo $log['id']; ?>">
                                    <img border="0" src="<?php echo $base; ?>/img/task--plus.png" title="add component" alt="add component" />
                                </span>
                                <span style="padding: 0px 0px 0px 0px;" logid="<?php echo $log['id']; ?>" id="imageAdd_<?php echo $log['id']; ?>">
                                    <img border="0" src="<?php echo $base; ?>/img/clipboard--plus.png" title="paste from clipboard" alt="paste from clipboard" />
                                </span>
                            </form>
                        </div>
                    </div>
                    </div>
            </tr>
        <?php endforeach; ?>
    </table>
    <p>
        <?php
        echo $this->Paginator->counter(array(
            'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
        ));
        ?>	</p>

    <div class="paging">
        <?php echo $this->Paginator->prev('<< ' . __('previous', true), array(), null, array('class' => 'disabled')); ?>
        | 	<?php echo $this->Paginator->numbers(); ?>
        |
        <?php echo $this->Paginator->next(__('next', true) . ' >>', array(), null, array('class' => 'disabled')); ?>
    </div>
</div>

<?php echo $this->element('component_action'); ?>


<script type="text/javascript" >   
    $(document).ready(function() {
        $("input[type=file]").filestyle({ 
            image: "<?php echo $base; ?>/img/image--plus.png",
            imageheight : 16,
            imagewidth : 16,
            width : 16,
            showFilename : false
        });
    });
        
    $('tr[id^="log_"]').hover(
    function() { // Hover in
        $(this).find('.action_buttons').show(); 
    }, 
    function() { // Hover out
        $(this).find('.action_buttons').hide();
    });

    $('.edit_log').click(function() {
        $('#logForm').show('fast');
        $('#addNewLog').hide();
        $('#closeNewLog').show();
        $.getJSON(this.title);
    });
</script>