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
 * This file is an example of using BrowscapFull to update the browser data.
 *
 * @package    Browscap
 * @author     Xiaoliang "David" Wei <dwei@facebook.com>
 * @copyright  Copyright (c) 2009 David Wei
 * @version    0.7
 * @license    http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @link       http://garetjax.info/projects/browscap/
 */


include_once 'BrowscapFull.php';

$main_dir = '../';

$browscap = new BrowscapFull($main_dir);
$browscap->doAutoUpdate   = true;
$browscap->localFile = $main_dir . 'browscap.ini';
$browscap->updateCache();


/* Test if there is any issue */
$ua = null;
$ua = $browscap->getBrowser();
echo $ua->Browser.$ua->Major."\n";


