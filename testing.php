<?php

/**
 * Description
 * @author Tim Rücker <tim.ruecker@mcbring.de>
 * @copyright (c) 2014, mcbring.de
 * 
 */
require_once('index.php');



$db = new \mysqli('localhost', 'root', '', 'test');

$dbOb = new \RASTER\QueryBuilder($db);
$dbOb->select()->from('tes');
$dbOb->where(array('field' => 'val', 'ff' => 'f'), array('qanother' => 'dd'));
echo $dbOb->query() . "\n\n\n";



$dbOb2 = new \RASTER\QueryBuilder($db);
$dbOb2->insert('ttt')->set(array('name' => 'value'));
echo $dbOb2->query() . "\n\n\n";




$dbOb3 = new \RASTER\QueryBuilder($db);
$dbOb3->update('test')->set(array('a' => 'b'))->where(array('id' => 5));
echo $dbOb3->query() . "\n\n\n";



$dbOb4 = new \RASTER\QueryBuilder($db);
$dbOb4->delete('test')->where(array('a' => 'b'));
echo $dbOb4->query() . "\n\n\n";
