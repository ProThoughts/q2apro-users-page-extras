<?php
/*
	Plugin Name: q2apro Users Page Extras
	Plugin URI: 
	Plugin Description: Add Thumbs-up count and Answer count to table of page users
	Plugin Version: 0.2
	Plugin Date: 2017-12-12
	Plugin Author: q2apro.com
	Plugin Author URI: http://www.q2apro.com/
	Plugin License: GPLv3
	Plugin Minimum Question2Answer Version: 1.5
	Plugin Update Check URI: 

	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	More about this license: http://www.gnu.org/licenses/gpl.html
	
*/

if ( !defined('QA_VERSION') )
{
	header('Location: ../../');
	exit;
}

// layer
qa_register_plugin_layer('q2apro-users-page-extras-layer.php', 'q2apro Users Page Extras');	


function qa_get_acceptance_rate_A($userid) 
{
	
	list( $userid, $count, $sel_count ) = answer_stats( $userid );
	
	// no such user exists
	if ( $userid === null || $userid < 1 ) 
	{
		return;
	}

	$acceptRate = 0;
	if($count>0) 
	{
		$acceptRate = number_format(100*$sel_count/$count, 1, ',', '.');
		//$output .= round(100*$sel_count/$count).' %';
	}
	$output = '<span class="qa-userlist-acceptrate" title="'.$acceptRate.' % der Antworten dieses Mitglieds wurden als Beste Antwort ausgezeichnet">';
	$output .= $acceptRate.' %</span>';
	
	return($output);
}


// userid, answer count and selected count - takes a lot of time to process! 
function answer_stats($userid) 
{
	$userdata = qa_db_read_one_assoc( 
				qa_db_query_sub('
					SELECT u.userid, COUNT(a.postid) AS qs, SUM(q.selchildid=a.postid) AS selected
					FROM ^users u
					LEFT JOIN ^posts a ON u.userid = a.userid AND a.type="A"
					LEFT JOIN ^posts q ON a.parentid = q.postid AND q.type="Q"
					WHERE u.userid = $
					', 
					$userid) 
			)
	;

	if(empty($userdata['selected']))
	{
		$userdata['selected'] = 0;
	}

	return array( $userdata['userid'], $userdata['qs'], $userdata['selected'] );
}


// userid, answer count and selected count
function qa_get_the_user_upvotes($userid) {
	$userUpvotes = qa_db_read_one_value(qa_db_query_sub('SELECT upvoteds FROM ^userpoints
															WHERE userid=#', $userid), true);
	$output = '<span class="qa-userlist-upvotes" title="Dieses Mitglied erhielt '.$userUpvotes.' Pluspunkte von anderen Mitgliedern">';
	$output .= $userUpvotes.' <img src="'.qa_path('qa-theme/mathelounge').'/daumen_hoch_mini.png" alt="Daumen" /></span>';
	return $output;
}


// userid, answer count and selected count
function qa_get_the_user_acount($userid) {
	$userAnswCount = qa_db_read_one_value(qa_db_query_sub('SELECT aposts FROM ^userpoints
														WHERE userid=#', $userid), true);
	$userAnswCount = number_format($userAnswCount, 0, ',', '.');					
	$output = '<span class="qa-userlist-upvotes" title="Dieses Mitglied hat '.$userAnswCount.' Antworten gegeben">';
	$output .= $userAnswCount.' Antworten</span>';
	return $output;
}



/*
	Omit PHP closing tag to help avoid accidental output
*/