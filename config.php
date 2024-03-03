<?php
/** op-unit-bitcoin:/config.php
 *
 * @created   2024-03-02
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
namespace OP\UNIT\BITCOIN;

/** Used class.
 *
 */

//	...
$CLI = [
];

//	...
$RPC = [
	'chain'    => 'regtest',
	'host'     => 'localhost',
	'user'     => 'username',
	'password' => 'password',
];

//	...
$config = [
	'bitcoin.conf' => '~/.bitcoin/bitcoin.conf',
	'CLI' => $CLI,
	'RPC' => $RPC,
];

//	...
return $config;
