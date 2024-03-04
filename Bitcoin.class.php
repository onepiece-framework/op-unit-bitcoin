<?php
/** op-unit-bitcoin:/Bitcoin.class.php
 *
 * @created   2019-08-27
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
namespace OP\UNIT;

/** Used class.
 *
 */
use OP\OP_CI;
use OP\OP_CORE;
use OP\OP_UNIT;
use OP\IF_UNIT;

/** Bitcoin
 *
 * @created   2019-08-27
 * @version   1.0
 * @package   op-unit-bitcoin
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */
class Bitcoin implements IF_UNIT
{
	/** trait.
	 *
	 */
	use OP_CORE, OP_UNIT, OP_CI;

	/** CLI - Access to local process.
	 *
	 * @created   2024-03-02
	 * @return    Bitcoin\CLI
	 */
	static function CLI() : Bitcoin\CLI
	{
		//	...
		static $_CLI;

		//	...
		if(!$_CLI ){
			require_once(__DIR__.'/Bitcoin-CLI.class.php');
			$_CLI = new Bitcoin\CLI;
		}

		//	...
		return $_CLI;
	}

	/** RPC - Access by TCP/IP
	 *
	 * @created   2024-03-02
	 * @return    Bitcoin\RPC
	 */
	static function RPC() : Bitcoin\RPC
	{
		//	...
		static $_RPC;

		//	...
		if(!$_RPC ){
			require_once(__DIR__.'/Bitcoin-RPC.class.php');
			$_RPC = new Bitcoin\RPC;
		}

		//	...
		return $_RPC;
	}

	static function Config()
	{
		//	...
		static $config;

		//	...
		if(!$config ){
			$config = require_once(__DIR__.'/include/config.php');
		}

		//	...
		return $config;
	}
}
