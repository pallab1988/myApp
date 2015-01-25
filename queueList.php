<?php

$finalJsonArr = array();
$xml = simplexml_load_file('res/proper-capacity-scheduler.xml');
$masterArr = [];
foreach ($xml->property as $property):
    $masterArr[] = array($property->name, $property->value);
endforeach;

$allQueues = [];
$allQueues = getAllQueues();

//q with properties arr
$qArr = getQProperties();

$finalQueuesArr = processAllQArr();

$finalJsonArr['queues'] = $finalQueuesArr;

print json_encode($finalJsonArr);

//function for getting the first level queues
function getFirstLevelQueues() {
    global $masterArr;
    foreach ($masterArr as $obj):
        $haystack = $obj;
        if (in_array('yarn.scheduler.capacity.root.queues', $haystack)) :
            return explode(',', $obj[1]);
        endif;
    endforeach;
}

//function for getting all the queues
function getAllQueues() {
    global $masterArr;
    $queuesArr = [];
    $str = 'root';
    foreach ($masterArr as $obj):
        $arr = explode('.', $obj[0]);
        $lastElement = array_pop($arr);
        if ($lastElement == 'queues'):
            $queuesArr[] = explode(',', $obj[1]);
        endif;
    endforeach;

    foreach ($queuesArr as $qArr):
        foreach ($qArr as $q) {
            $str = $str . '|' . $q;
        }
    endforeach;
    return explode('|', $str);
}

//function for getting properties of individual queue

function getQProperties() {
    global $allQueues;
    global $masterArr;
    foreach ($allQueues as $q) :
        $propertiesArr = [];
        foreach ($masterArr as $obj):
            $arr = explode('.', $obj[0]);
            if ($q == $arr[count($arr) - 2]):
                $propertiesArr[$arr[count($arr) - 1]] = $obj[1];
            endif;
        endforeach;
        $individualQPropertiesArr[$q] = $propertiesArr;
    endforeach;
    return $individualQPropertiesArr;
}

function processAllQArr() {
    global $qArr;
    $finalArr = [];
    foreach ($qArr as $key => $q):
        $propertyArr = [];
        foreach ($q as $k => $v):
            $propertyArr[$k] = (string) $v;
        endforeach;
        if ($key != 'root') {
            $parentName = get_parent_queue($key);
        } else {
            $parentName = '';
        }
        $propertyArr['parentStr'] = getParentQueueStr($key);
        $propertyArr['qname'] = $key;
        $propertyArr['pQueue'] = $parentName;

        if (!array_key_exists('queues', $propertyArr)):
            $propertyArr['queues'] = '';
        endif;
        if (!array_key_exists('capacity', $propertyArr)):
            $propertyArr['capacity'] = '';
        endif;
        if (!array_key_exists('state', $propertyArr)):
            $propertyArr['state'] = '';
        endif;
        if (!array_key_exists('acl_submit_applications', $propertyArr)):
            $propertyArr['acl_submit_applications'] = '';
        endif;
        if (!array_key_exists('qname', $propertyArr)):
            $propertyArr['qname'] = '';
        endif;
        if (!array_key_exists('pQueue', $propertyArr)):
            $propertyArr['pQueue'] = '';
        endif;
        $finalArr[] = $propertyArr;
    endforeach;
    return $finalArr;
}

//function to get parent queue
function get_parent_queue($str) {
    global $qArr;
    $val = '';
    foreach ($qArr as $key => $master) {
        if (array_key_exists('queues', $master)) {
            $arr = explode(',', (string) $master['queues']);
            if (in_array($str, $arr)) {
                $val = $key;
            }
        }
    }
    return $val;
}

//function for getting the parent STR
function getParentQueueStr($q) {
    global $masterArr;
    $str = '';

    foreach ($masterArr as $element):
        $str = (string) $element[0];
        $arr = explode('.', $str);
        $firstThreeElement = array_slice($arr, 0, 3);
        $diffArr = array_values(array_diff($arr, $firstThreeElement));
        array_pop($diffArr);
        $lastElement = array_pop($diffArr);
        if ($lastElement == $q) {
            $str = implode('.', $diffArr);
            break;
        }
    endforeach;
    return $str;
}
