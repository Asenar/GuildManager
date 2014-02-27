<?php 
/*  Guild Manager v1.0.4
	Guild Manager has been designed to help Guild Wars 2 (and other MMOs) guilds to organize themselves for PvP battles.
    Copyright (C) 2013  Xavier Olland

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>. */

//MySQL connection / Connexion � MySQL
include('../../../config.php');
//GuildManager main configuration file / Fichier de configuration principal GuildManager
include('../config.php');
//Language management / Gestion des traductions
include('../language.php');

//Creating needed date variables / Cr�ation des variables de dates n�cessaires
$date = $_GET['date'] ;
$date = strtotime($date);
$day = date('d', $date) ;
$month = date('m', $date) ;
$year = date('Y', $date) ;
$computed_date = $year.'-'.$month.'-01';
$computed_date = date_create( $computed_date);
$prev = date_format(date_sub( $computed_date , date_interval_create_from_date_string("1 month")), 'Y-m-d');
$next = date_format(date_add( $computed_date , date_interval_create_from_date_string("2 month")), 'Y-m-d');
$current_day = date('d', time() );
$current_week = date('W', time() );
$current_month = date('m', time() );
$event_limit_date = date('Y-m-d', time());
$event_limit_date = date_create( $event_limit_date );
$event_limit_date = date_format(date_sub( $event_limit_date , date_interval_create_from_date_string( $event_limit )), 'Y-m-d');

//First day of the month / Premier jour du mois
$first_day = mktime(0,0,0,$month, 1, $year);
$title =strftime( '%B', $first_day);
$title = utf8_encode( $title );

//switching day numbers for WvW week / Modification des num�ros de jours pour la semain McM
// Saturday = 1 /Samedi = 1

$computed_day = date( 'N', $first_day) ;

$sql = "SELECT value FROM ".$gm_prefix."param WHERE TYPE = 'day' AND complement = '$computed_day'";
$list=mysql_query($sql);
while($result=mysql_fetch_row($list)) { $blank = $result[0]; };

$days_in_month = cal_days_in_month(0, $month, $year) ; 
//Creating calendar / Cr�ation du calendrier
 echo "
<div id='calendar'>
	<table border=1 width=294 >
		<theader>
			<tr><th colspan=7> $title $year</th></tr>
			<tr>";
			//Day ordering / Ordre des jours
			$sql = "SELECT LEFT( d.$local, 1 ) FROM ".$gm_prefix."param AS p LEFT JOIN ".$gm_prefix."dictionary AS d ON d.table_ID=p.param_ID AND d.entity_name='param' WHERE TYPE = 'day' ORDER BY value";
			$list=mysql_query($sql);
			while($result=mysql_fetch_row($list))
			{ echo "<td class='center' width=42>".$result[0]."</td>" ; };
			echo "
			</tr>
		</theader>
		<tbody>";
			//Day in week count / Compteur des jours de la semaine
			$day_count = 1; echo "<tr>";
			//Blank creation / Cr�ation des cases vides
			while ( $blank > 0 ) { echo "<td></td>"; $blank = $blank-1;  $day_count++; }  
			$day_num = 1;
			//Count of days in month / Compteur des jours du mois
			while ( $day_num <= $days_in_month ) { 
			//Retrieving day / R�cup�ration du jour
			$computed_date = strtotime($year."-".$month."-".$day_num);

			//Retrieving Events / R�cup�ration des �v�nements
			if ( $year.'-'.$month.'-'.$day_num >= $event_limit_date ){ 
			$sqlr="SELECT r.raid_event_ID, r.map, r.color
			FROM ".$gm_prefix."raid_event AS r 
			WHERE r.dateRaid='$year-$month-$day_num'";

			$listr=mysql_query($sqlr); 
			if( mysql_num_rows($listr)>0)
			{ while( $resultr=mysql_fetch_row($listr)) 
			{ echo "<td class='center' style='background-color:".$resultr[2].";color:#FFFFFF;";
			if( $current_month == $month && $current_day == $day_num){echo "text-decoration:underline;border:solid 3px #606060";};
			echo"'>
			<a class='calendar' onclick=\"$('#event').load('resources/php/BO_Div_Event.php?id=".$resultr[0]."&date=$computed_date');$('#event').show();\" href=\"javascript:void(0)\">".$day_num."</a></td>"; };}
			else {echo "<td class='center' ";
			if( $current_month == $month && $current_day == $day_num){echo "style='border:solid 2px #8c1922;'";};
			echo "><a style='text-decoration:none;color:#000000;' onclick=\"$('#event').load('resources/php/BO_Div_Event.php?id=0&date=$computed_date');$('#event').show();\" href=\"javascript:void(0)\">".$day_num."</a></td>"; };
			}
			//Day without event / Jour sans �v�nement
			else {echo "<td class='center' >".$day_num."</td>"; };
			$day_num++; $day_count++;
			//End of week / Fin de semaine
			if ($day_count > 7) { echo "</tr><tr>"; $day_count = 1; }
			}
			//Blank creation / Cr�ation des cases vides
			while ( $day_count >1 && $day_count <=7 ) {  echo "<td> </td>";  $day_count++;  } 

			//End of calendat, navigation / Fin du calendrier, navigation
			echo "</tr>	
		</tbody>
		<tfooter>
			<tr class='footer'>
				<td class='center' ><a class='menu' onclick=\"$('#result').load('resources/php/BO_Div_Calendar.php?date=".$prev."');$('#result').show();\" href=\"javascript:void(0)\">".$lng[p_BO_Div_Calendar_a_1]."</a></td>
				<td class='center' colspan=5><a class='menu' onclick=\"$('#result').load('resources/php/BO_Div_Calendar.php?date=".$year."-".$current_month."-01');$('#result').show();\" href=\"javascript:void(0)\">".$lng[p_BO_Div_Calendar_a_2]."</a></td>
				<td class='center'><a class='menu' onclick=\"$('#result').load('resources/php/BO_Div_Calendar.php?date=".$next."');$('#result').show();\" href=\"javascript:void(0)\">".$lng[p_BO_Div_Calendar_a_3]."</a></td>
			</tr>
		</tfooter>
	</table>
	</div>";
?>