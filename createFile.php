<?php

$json = file_get_contents('php://input');
$obj = json_decode($json);
$allQueues = $obj->queues;
$doc = new DOMDocument('1.0');

//the root element
$root = $doc->createElement('configuration');
$root = $doc->appendChild($root);
foreach ($allQueues as $q):

    if ($q->queues != '') {
        $property = $doc->createElement('property');
        $name = $doc->createElement('name');
        
        if($q->parentStr == '') {
            $nameVal = $doc->createTextNode('yarn.scheduler.capacity.' . $q->qname . '.queues');
        }else{
            $nameVal = $doc->createTextNode('yarn.scheduler.capacity.' .$q->parentStr.'.'.$q->qname . '.queues');
        }
        
        $property->appendChild($name);
        $name->appendChild($nameVal);
        $value = $doc->createElement('value');
        $valueVal = $doc->createTextNode($q->queues);
        $value->appendChild($valueVal);
        $property->appendChild($value);
        $root->appendChild($property);
    }
    
    if(is_numeric($q->capacity)) {
        $property = $doc->createElement('property');
        $name = $doc->createElement('name');
        
        if($q->parentStr == '') {
            $nameVal = $doc->createTextNode('yarn.scheduler.capacity.' . $q->qname . '.capacity');
        }else{
            $nameVal = $doc->createTextNode('yarn.scheduler.capacity.' .$q->parentStr.'.'.$q->qname . '.capacity');
        }
       
        $property->appendChild($name);
        $name->appendChild($nameVal);
        $value = $doc->createElement('value');
        $valueVal = $doc->createTextNode($q->capacity);
        $value->appendChild($valueVal);
        $property->appendChild($value);
        $root->appendChild($property);
    }
    
    if($q->state != '') {
        $property = $doc->createElement('property');
        $name = $doc->createElement('name');
        
        if($q->parentStr == '') {
            $nameVal = $doc->createTextNode('yarn.scheduler.capacity.' . $q->qname . '.state');
        }else{
            $nameVal = $doc->createTextNode('yarn.scheduler.capacity.' .$q->parentStr.'.'.$q->qname . '.state');
        }
       
        $property->appendChild($name);
        $name->appendChild($nameVal);
        $value = $doc->createElement('value');
        $valueVal = $doc->createTextNode($q->state);
        $value->appendChild($valueVal);
        $property->appendChild($value);
        $root->appendChild($property);
    }
    
    
    if($q->acl_submit_applications != '') {
        $property = $doc->createElement('property');
        $name = $doc->createElement('name');
        
        if($q->parentStr == '') {
            $nameVal = $doc->createTextNode('yarn.scheduler.capacity.' . $q->qname . '.acl_submit_applications');
        }else{
            $nameVal = $doc->createTextNode('yarn.scheduler.capacity.' .$q->parentStr.'.'.$q->qname . '.acl_submit_applications');
        }
       
        $property->appendChild($name);
        $name->appendChild($nameVal);
        $value = $doc->createElement('value');
        $valueVal = $doc->createTextNode($q->acl_submit_applications);
        $value->appendChild($valueVal);
        $property->appendChild($value);
        $root->appendChild($property);
    }

endforeach;
//header('Content-type: text/xml');
//header('Content-Disposition: attachment; filename="capacity-scheduler.xml"');
$doc->saveXML();
$doc->save("res/capacity-scheduler.xml");
