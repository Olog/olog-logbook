<?php
echo $this->Html->script('jquery-ui.min.js');
echo $this->Html->script('jquery.phpdate.js');
echo $this->Html->script('chosen.jquery.min.js');
echo $this->Html->css('chosen.css');
?>

<div id="search_parameters">
<div id="quickfilters">
    <?php
    $timespans = array('All', 'Last day',
        'Last 3 Days',
        'Last week',
        'Last month',
        'Last 3 Months',
        'Last 6 Months',
        'Last year',
        'Choose timespan'
    );

    echo '<div id="search_input"><input size="20" type="text" name="search" id="search" /></div>';
    echo '<div id="search_filters">';
    if (isset($this->params['named']['start']) && isset($this->params['named']['end'])) {
        $seconds = $this->params['named']['start'];
        if (abs($seconds - mktime(0, 0, 0, date('m'), date('d') - 1, date('y'))) <= 60) {  // Last day
            $timeOption = 1;
        } elseif (abs($seconds - mktime(0, 0, 0, date('m'), date('d') - 3, date('y'))) <= 60) { // Last 3 days
            $timeOption = 2;
        } elseif (abs($seconds - mktime(0, 0, 0, date('m'), date('d') - 7, date('y'))) <= 100) { // Last week
            $timeOption = 3;
        } elseif (abs($seconds - mktime(0, 0, 0, date('m') - 1, date('d'), date('y'))) <= 100) { // Last month
            $timeOption = 4;
        } elseif (abs($seconds - mktime(0, 0, 0, date('m') - 3, date('d'), date('y'))) <= 100) { // Last 3 months
            $timeOption = 5;
        } elseif (abs($seconds - mktime(0, 0, 0, date('m') - 6, date('d'), date('y'))) <= 500) { // Last 6 months
            $timeOption = 6;
        } elseif (abs($seconds - mktime(0, 0, 0, date('m'), date('d'), date('y') - 1)) <= 500) { // Last year
            $timeOption = 7;
        } else {
            $timeOption = 8;
        }
        echo $this->Form->select('timespan', $timespans, $timeOption, array('id' => 'timespan'));
    } else {
        echo $this->Form->select('timespan', $timespans, 0, array('id' => 'timespan'));
    }
    echo '</div>';
    ?>
</div>
<div id="select_logbook">
<?php
if (isset($this->params['named']['logbook'])) {
    echo $this->Form->input('logbook', array('label' => false, 'type' => 'select', 'multiple' => true, 'options' => $logbooks, 'data-placeholder' => 'Choose Logbook(s)...', 'selected' => explode(',', $this->params['named']['logbook'])));
} else {
    echo $this->Form->input('logbook', array('label' => false, 'type' => 'select', 'multiple' => true, 'options' => $logbooks, 'data-placeholder' => 'Choose Logbook(s)...'));
}
?>
</div>
</div>

<script type="text/javascript" >
    $('#logbook').chosen();
</script>

<script type="text/javascript" >
    $('#logbook').bind('change', function() {
        var logbookType = $('#logbook').val();
        if(logbookType != null){
            logbookType='logbook:'+logbookType;
        }
        else {
            logbookType = '';
        }
<?php
$args = '';
foreach ($this->params['named'] as $key => $param) {
    if ($key != 'logbook' && $key != 'page') {
        $args .= '/' . $key . ':' . $param;
    }
}
?>
        window.location.replace('<?php echo $base . '/' . $this->params['plugin'] . '/' . $this->params['controller'] . '/' . $this->params['action'] . '/'; ?>' + logbookType + '<?php echo $args; ?>');
    });
    
    $('#timespan')
    .ready(function() {
        var newTimeSpan = $('#timespan').val();
        if (newTimeSpan == 8) {
            $('#quickfilters').append('<div id="date_choice"><input id="datepicker_start" type="text"><input id="datepicker_end" type="text"><button id="datepicker_go" type="button">Go</button></div>');
            
            var start_date = new Date();
            start_date.setTime(<?php echo isset($this->params['named']['start']) ? $this->params['named']['start'] : 0; ?> * 1000);
            var start_date_string = (start_date.getMonth() + 1) + '/' + start_date.getDate() + '/' + start_date.getFullYear();
            $('#datepicker_start').datepicker().val(start_date_string);
            
            var end_date = new Date();
            end_date.setTime(<?php echo isset($this->params['named']['end']) ? $this->params['named']['end'] : 0; ?> * 1000);
            var end_date_string = (end_date.getMonth() + 1) + '/' + end_date.getDate() + '/' + end_date.getFullYear();
            $('#datepicker_end').datepicker().val(end_date_string);
        }
    })
    .bind('change', function() {
        var newTimeSpan = $('#timespan').val();
        if (newTimeSpan == 8) {
            $('#quickfilters').append('<div id="date_choice"><input id="datepicker_start" type="text"><input id="datepicker_end" type="text"><button id="datepicker_go" type="button">Go</button></div>');
            $('#datepicker_start').datepicker().watermark('Start date');
            $('#datepicker_end').datepicker().watermark('End date');
        }
        else {
            window.location.replace('<?php echo $base . '/' . $this->params['plugin'] . '/' . $this->params['controller']; ?>+ /timespanChange/' + newTimeSpan + '<?php echo $argumentString; ?>');
        }
    });
    
    $('#datepicker_go').live('click', function() {
        var start = $('#datepicker_start').datepicker('getDate');
        var end = $('#datepicker_end').datepicker('getDate');
        
        if (start == null) {
            alert('You must choose a starting date!');
            exit();
        }
        if (end == null) {
            alert('You must choose an ending date!');
            exit();
        }
        
        if (start > end) {
            alert('The starting date must not come after the ending date!');
            exit();
        }
        
        var start_unix = $.PHPDate("U", start);
        var end_unix = $.PHPDate("U", end);
        
<?php
$args = '';
foreach ($this->params['named'] as $key => $param) {
    if ($key != 'start' && $key != 'end') {
        $args .= '/' . $key . ':' . $param;
    }
}
?>
        
        window.location.replace('<?php echo $base . '/' . $this->params['plugin'] . '/' . $this->params['controller'] . '/' . $this->params['action']; ?>/start:' + start_unix + '/end:' + end_unix + '<?php echo $args; ?>');
    });
    
    $('#search').bind('keypress', function(e) {
        var code = (e.keyCode ? e.keyCode : e.which);
        if(code == 13) {
            var search = $('#search').val();
            if(search!=''){
                search='search:'+'*'+search+'*';
            }
<?php
$args = '';
foreach ($this->params['named'] as $key => $param) {
    if ($key != 'search') {
        $args .= '/' . $key . ':' . $param;
    }
}
?>
            window.location.replace('<?php echo $base . '/' . $this->params['plugin'] . '/' . $this->params['controller'] . '/' . $this->params['action'] . '/'; ?>' + search + '<?php echo $args; ?>');
        }
    }).watermark('Search...');
</script>