<?php 
/**
 * Astro Browser
 * 
 * Astro Empires Base Browse UI
 * 
 * @author Adam Davidson <dark@gatevo.com>
 * @version 1.0
 * @package Astro
 */

/**
 * FrameD Core
 */
require_once('core/core.php');

$sqlitedb = new SQLite3Db('db/astros.db');

$player = $sqlitedb->smartSelectOne('player',null, null);

if(!$player){
	$sqlitedb->smartInsert('player',
						   array('added_datetime'    => $sqlitedb->now(),
								 'modified_datetime' => $sqlitedb->now(),
								 'level'			 => '1',
								 'name'				 => 'name',
								 'astro_player_id'	 => 12345
						  ));

	$player = $sqlitedb->smartSelectOne('player',null, null);
}

$mysqldb = new MySQLDb('wordpress','wordpress','127.0.0.1','wordpress');

$user = $mysqldb->smartSelectOne('wp_users',null,null);

$page     = $payload->getParam('page')->getInt(1);
$page_sz  = $payload->getParam('page_sz')->getInt(20);
$searchon = $payload->getParam('searchon')->getString();
$search   = $payload->getParam('search')->getString();
$dir      = $payload->getParam('dir')->getString();
$sorton   = $payload->getParam('sorton')->getString();


$controller->setViewData(array(
								'data'        => $data, 
								'page'        => $page, 
								'page_sz'     => $page_sz, 
								'count'       => $count, 
								'total_pages' => $total_pages,
								'search'      => $search,
								'searchon'    => $searchon,
								'sorton'      => $sorton,
								'dir'         => $dir,
								'user'        => $user,
								'player'      => $player
						));
$controller->render('example');
?>
