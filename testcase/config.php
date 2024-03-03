<?php
/**
 * op-unit-bitcoin:/testcase/config.php
 *
 * @created   2024-03-03
 * @version   1.0
 * @package   op-unit-bitcoin
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */

/** Declare strict
 *
 */
declare(strict_types=1);

/** namespace
 *
 */
namespace OP\UNIT\Bitcoin;

/* @var $bitcoin \OP\UNIT\Bitcoin */
$bitcoin = OP()->Unit('Bitcoin');

//	...
D( $bitcoin->Config() );
