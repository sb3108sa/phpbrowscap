<?php
/**
 * Browscap.ini parsing class with caching and update capabilities
 *
 * PHP version 5
 *
 * LICENSE: This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * This file sepcifies the caching strategy for Browscap.
 * Adjust the data in this file according to the browser percentage.
 *
 * Instead of having a single large file containing all
 * browser behaviors, we can split the informations into
 * multi files, with the most popular files at the beginning.
 *
 * $cache_level specifies such multi-level caching scheme.
 * It is in the following formats:
 *   array('<cache_file_name>' => array('<Browser><MajorVer>'))
 *   The order of entries in the array implies the level of caching.
 *   <cache_file_name> is the file that store the cache;
 *   <Browser><MajorVer> is the browser info that is included in the cache;
 *     e.g. IE7, Firefox2 and etc.
 *
 * The last entry must be an empty array, which leads to inclusion
 * of all browsers information, or an array with a single element
 * "Default Browser0", which is the default browser without
 * further browscap recognition
 *
 * @package    Browscap
 * @author     Xiaoliang "David" Wei <dwei@facebook.com>
 * @copyright  Copyright (c) 2009 David Wei
 * @version    0.7
 * @license    http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @link       http://garetjax.info/projects/browscap/
 */

$cache_level = array(
	 // Layer 1: <15KB
	 'browscap_cache_l1.php' => array(
			'IE6', 'IE7', 'IE8',
			'Firefox3',
			'Safari3',
			),

	 // Layer 2: 52KB
	 'browscap_cache_l2.php' => array(
		 'Firefox2', 'Safari2', 'Opera9',
		 ),

	 // Layer 3: 57KB
	 'browscap_cache_l3.php' => array(
		 'Safari1', 'Mozilla1', 'iPhone3', 'Opera0', 'Firefox1',
		 'iTouch0', 'Netscape7', 'Firefox0', 'Nokia0',
		 'Wii Web Browser0', 'Opera10', 'Chrome0',
		 ),

	 /* The last entry should be an empty array, which implies
		* to include all browser, or an array with a single element
		* "Default Browser0", which is the default browser without
		* further browscap recognition
		*/
	 // Layer 4: 300KB
	 'browscap_cache_l4.php' => array(),

	 // Layer 4 (alternative): 0.7KB
	 //   'browscap_cache_l4.php' => array('Default Browser0'),
	);
