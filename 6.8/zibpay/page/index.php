<?php
/*
商城数据统计
 */

if (!is_super_admin()) {
    wp_die('您不能访问此页面', '权限不足');
    exit;
}

function zib_this_card()
{
    global $wpdb;
    $thismonth_time = current_time('Y-m');
    $today          = zibpay_get_order_statistics_totime('today');
    $yester         = zibpay_get_order_statistics_totime('yester');
    $thismonth      = zibpay_get_order_statistics_totime('thismonth');
    $lastmonth      = zibpay_get_order_statistics_totime('lastmonth');
    $all            = zibpay_get_order_statistics_totime('all');
    $thisyear       = zibpay_get_order_statistics_totime('thisyear');
    $rebate         = '';

    $_all       = (array) $wpdb->get_row("SELECT SUM(rebate_price) as rebate,SUM(income_price) as income  FROM $wpdb->zibpay_order WHERE  `status` = 1");
    $_thismonth = (array) $wpdb->get_row("SELECT SUM(rebate_price) as rebate,SUM(income_price) as income FROM $wpdb->zibpay_order WHERE  `status` = 1 and  pay_time LIKE '%$thismonth_time%'");

    $rebate = array(
        'all'       => isset($_all['rebate']) ? floatval($_all['rebate']) : 0,
        'thismonth' => isset($_thismonth['rebate']) ? floatval($_thismonth['rebate']) : 0,
    );
    $income = array(
        'all'       => isset($_all['income']) ? floatval($_all['income']) : 0,
        'thismonth' => isset($_thismonth['income']) ? floatval($_thismonth['income']) : 0,
    );

    $_rebate_1 = $wpdb->get_var("SELECT SUM(rebate_price) FROM $wpdb->zibpay_order WHERE  `status` = 1 and `rebate_status` = 1");
    $_income_1 = $wpdb->get_var("SELECT SUM(income_price) FROM $wpdb->zibpay_order WHERE  `status` = 1 and `income_status` = 1");

    //有效
    $rebate['effective'] = ($rebate['all'] - $_rebate_1);
    $income['effective'] = ($income['all'] - $_income_1);

    $data = array(
        array(
            'top'    => '今日订单',
            'val'    => $today['count'],
            'bottom' => '昨日订单：' . $yester['count'],
        ),
        array(
            'top'    => '今日收款',
            'val'    => ($today['sum'] > 1000) ? (int)$today['sum'] : $today['sum'],
            'bottom' => '昨日收款：' . $yester['sum'],
        ),
        array(
            'top'    => '本月订单',
            'val'    => $thismonth['count'],
            'bottom' => '上月订单：' . $lastmonth['count'],
        ),
        array(
            'top'    => '本月收款',
            'val'    => ($thismonth['sum'] > 10000) ? (int) $thismonth['sum']: $thismonth['sum'],
            'bottom' => '上月收款：' . $lastmonth['sum'],
        ),
        array(
            'top'    => '有效单量',
            'val'    => $all['count'],
            'bottom' => '今年订单：' . $thisyear['count'],
        ),
        array(
            'top'    => '有效收款',
            'val'    => ($all['sum'] > 10000) ? (int) $all['sum'] : $all['sum'],
            'bottom' => '今年收款：' . $thisyear['sum'],
        ),
        array(
            'top'    => '总分成',
            'val'    => ($income['all'] > 10000) ? (int) $income['all'] : $income['all'],
            'bottom' => '未提现:' . $income['effective'] . ' · 本月:' . $income['thismonth'],
        ),
        array(
            'top'    => '总佣金',
            'val'    => ($rebate['all'] > 10000) ? (int) $rebate['all'] : $rebate['all'],
            'bottom' => '未提现:' . $rebate['effective'] . ' · 本月:' . $rebate['thismonth'],
        ),
    );

    $html = '';
    foreach ($data as $v) {
        $html .= '<div class="row-3">
                <div class="box-panel">
                    <span class="count_top">' . $v['top'] . '</span>
                    <div class="count">' . $v['val'] . '</div>
                    <span class="count_bottom">' . $v['bottom'] . '</span>
                </div>
            </div>';
    }
    return $html;
}

function zib_this_charts_data($order_type = 0)
{
    $cycle        = 'day';
    $time_day     = '30';
    $time_end     = current_time('Y-m-d 23:59:59');
    $time_start   = date('Y-m-d 00:00:00', strtotime("-$time_day day", strtotime($time_end)));
    $filling      = zib_this_get_time_filling($cycle, array($time_start, $time_end));
    $cycle_format = '%Y-%m-%d';

    global $wpdb;
    $order_type_where = $order_type ? " and order_type=$order_type" : ' and order_type != 8';
    $db_data          = $wpdb->get_results("SELECT COUNT(*) as count,SUM(pay_price) as price,date_format(create_time, '$cycle_format') as time FROM {$wpdb->zibpay_order} WHERE `status` = 1 AND pay_price > 0 AND pay_time BETWEEN '$time_start' AND '$time_end' $order_type_where group by date_format(create_time,'$cycle_format')");

    $nums   = $filling['data'];
    $total  = $filling['data'];
    $result = $filling['time'];
    array_walk($db_data, function ($value, $key) use ($result, &$nums, &$total) {
        $value         = (array) $value;
        $index         = array_search($value['time'], $result);
        $nums[$index]  = $value['count'];
        $total[$index] = floatval($value['price']);
    });
    $chart_data = [
        'time'  => $result,
        'count' => $nums,
        'price' => $total,
    ];
    return $chart_data;
}

$charts_data     = zib_this_charts_data();
$vip_charts_data = zib_this_charts_data(4);

//获取填充时间
function zib_this_get_time_filling($cycle, $time)
{
    $cycle_format_array = array(
        'day'   => 'Y-m-d',
        'month' => 'Y-m',
        'year'  => 'Y',
    );
    $count_x = array(
        'day'   => 86400,
        'month' => 259200,
        'year'  => 'Y',
    );

    $new_time   = current_time('mysql');
    $time_start = $time[0];
    $time_end   = !empty($time[1]) ? $time[1] : '';

    if (!$time_end) {
        $time_start = $new_time;
        $time_end   = $time[0];
    }

    if (strtotime($time_end) > strtotime($new_time)) {
        $time_end = $new_time;
    }
    //结束时间不高于当前时间

    if (strtotime($time_end) < strtotime($time_start)) {
        throw new Exception('结束时间不能小于开始时间');
    }

    if ('day' == $cycle) {
        $count = ceil((strtotime($time_end) - strtotime($time_start)) / 86400);
    } elseif ('month' == $cycle) {
        $date1_stamp                     = strtotime($time_end);
        $date2_stamp                     = strtotime($time_start);
        list($date_1['y'], $date_1['m']) = explode("-", date('Y-m', $date1_stamp));
        list($date_2['y'], $date_2['m']) = explode("-", date('Y-m', $date2_stamp));
        $count                           = abs($date_1['y'] - $date_2['y']) * 12 + ($date_1['m'] - $date_2['m']) + 1;
    }

    for ($i = $count - 1; 0 <= $i; $i--) {
        $time_end_sum = date($cycle_format_array[$cycle], strtotime($time_end));
        $result[]     = date($cycle_format_array[$cycle], strtotime('-' . $i . ' ' . $cycle, strtotime($time_end_sum)));
        $data[]       = 0;
    }

    $asd = array(
        'time'       => $result,
        'data'       => $data,
        'count'      => $count,
        'cycle'      => $cycle,
        'time_start' => $time_start,
        'time_end'   => $time_end,
    );

    return array(
        'time' => $result,
        'data' => $data,
    );
}

?>

<div class="wrap pay-container">
<?php echo zib_this_card(); ?>
    <div class="row-6">
        <div class="box-panel highcharts">
            <div class="highcharts-title">有效收款单量</div>
            <div style="margin:0 -30px -20px 0px;"><div id="highcharts_count" style="height:400px"></div></div>
        </div>
    </div>
    <div class="row-6">
        <div class="box-panel highcharts">
            <div class="highcharts-title">有效收款金额</div>
            <div style="margin:0 -30px -20px 0px;"><div id="highcharts_price" style="height:400px"></div></div>
        </div>
    </div>
    <script type="text/javascript">
        option = {
            legend: {
                data: ['全部订单',  '购买会员']
            },
            tooltip: {
                trigger: 'axis'
            },
            xAxis: {
                type: 'category',
                data: <?php echo json_encode($charts_data['time']); ?>
            },
            yAxis: {
                type: 'value'
            },
            series: [{
                name: '全部订单',
                data: <?php echo json_encode($charts_data['count']); ?>,
                type: 'line',
                smooth: true
            }, {
                name: '购买会员',
                data: <?php echo json_encode($vip_charts_data['count']); ?>,
                type: 'line',
                smooth: true
            }]
        };

        var myChart = echarts.init(document.getElementById('highcharts_count'), 'westeros');
        myChart.setOption(option);

        option = {
            legend: {
                data: ['全部订单', '购买会员']
            },
            tooltip: {
                trigger: 'axis'
            },
            xAxis: {
                type: 'category',
                data: <?php echo json_encode($charts_data['time']); ?>
            },
            yAxis: {
                type: 'value'
            },
            series: [{
                name: '全部订单',
                data: <?php echo json_encode($charts_data['price']); ?>,
                type: 'line',
                smooth: true
            }, {
                name: '购买会员',
                data: <?php echo json_encode($vip_charts_data['price']); ?>,
                type: 'line',
                smooth: true
            }]
        };

        var myChart = echarts.init(document.getElementById('highcharts_price'), 'westeros');
        myChart.setOption(option);
    </script>
</div>

<?php
