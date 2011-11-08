<div id="quickfilters">
    <?php
    $timespans = array('All', 'Last day',
        'Last 3 Days',
        'Last week',
        'Last month',
        'Last 3 Months',
        'Last 6 Months',
        'Last year'
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
        }
        echo $this->Form->select('timespan', $timespans, $timeOption, array('id' => 'timespan'));
    } else {
        echo $this->Form->select('timespan', $timespans, 0, array('id' => 'timespan'));
    }
    if (isset($this->params['named']['logbook'])) {
        echo $this->Form->select('logbook', $logbooks, array($this->params['named']['logbook']), array('id' => 'logbook'));
    } else {
        echo $this->Form->select('logbook', $logbooks, null, array('id' => 'logbook'));
    }
    echo '</div>';
    ?>
</div>

<script type="text/javascript" >
    $('#logbook').bind('change', function() {
        var logbookType = $('#logbook').val();
        if(logbookType!=''){
            logbookType='logbook:'+logbookType;
        }
<?php
$args = '';
foreach ($this->params['named'] as $key => $param) {
    if ($key != 'logbook') {
        $args .= '/' . $key . ':' . $param;
    }
}
?>
        window.location.replace('<?php echo $base . '/' . $this->params['plugin'] . '/' . $this->params['controller'] . '/' . $this->params['action'] . '/'; ?>' + logbookType + '<?php echo $args; ?>');
    });
    
    $('#timespan').bind('change', function() {
        var newTimeSpan = $('#timespan').val();
        window.location.replace('<?php echo $base . '/' . $this->params['plugin'] . '/' . $this->params['controller']; ?>+ /timespanChange/' + newTimeSpan + '<?php echo $argumentString; ?>');
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