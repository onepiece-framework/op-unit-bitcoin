<?php
/** op-unit-bitcoin:/Bitcoin.class.php
 *
 * @created   2019-08-27
 * @version   1.0
 * @package   op-unit-bitcoin
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */

/** namespace
 *
 */
namespace OP\UNIT;

/** Used class.
 *
 */
use OP\Env;
use OP\OP_CORE;
use OP\OP_UNIT;
use OP\IF_UNIT;
use function OP\ConvertPath;
use function OP\Encode;

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
	use OP_CORE, OP_UNIT;

	/** Database
	 *
	 */
	static function Database()
	{
		/* @var $_database BITCOIN\Database */
		static $_database;

		//	...
		if( $_database === null ){
			//	...
			require(__DIR__.'/Database.class.php');

			//	...
			$_database = new \OP\UNIT\BITCOIN\Database();
		}

		//	...
		return $_database;
	}

	/** Get RPC port number.
	 *
	 * @created  2019-08-28
	 * @param    string      $chain
	 * @return   integer     $port
	 */
	static function Port($label)
	{
		//	...
		switch( $label ){
			case 'mainnet':
				$port =  8332;
				break;
			case 'testnet':
				$port = 18332;
				break;
			case 'regtest':
				$port = 18443;
				break;
		};

		//	...
		return $port;
	}

	/** Submit to RPC.
	 *
	 * @created  2019-08-28
	 * @param    string      $method
	 * @return   string      $json
	 */
	static function RPC($method, $params=[])
	{
		//	Bitcoin RPC URL
		static $_url;

		//	Bitcoin RPC URL - Build
		if( $_url === null ){
			//	...
			$config = Env::Get('bitcoin');
			//	...
			$port = $config['port']     ?? self::Port($config['chain']);
			$host = $config['host']     ?? null;
			$user = $config['user']     ?? null;
			$pass = $config['password'] ?? null;
			//	...
			$_url = "http://{$user}:{$pass}@{$host}:{$port}";
		};

		//	Bitcoin RPC JSON - Build
		$json = [];
		$json['jsonrpc'] = '1.0';
		$json['id']      = 'forasync';
		$json['method']  = $method;
		$json['params']  = $params;
		$json = json_encode($json);

		/*
		//	Console command.
		$command = "curl --data-binary '$json' -H 'content-type:text/plain;' $_url";
		$json = `$curl`;
		*/

		//	Curl - Header
		$headers = array(
			"Content-Type: application/json",
		);

		//	Check
		if(!function_exists('curl_init') ){
			$module = 'curl';
			include( ConvertPath('asset:/bootstrap/php/content.phtml') );
			throw new \Exception("php-{$module} is not installed.");
		}

		//	Curl - Setting
		$curl = curl_init($_url);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST  , "POST"   );
		curl_setopt($curl, CURLOPT_POSTFIELDS     , $json    );
		curl_setopt($curl, CURLOPT_RETURNTRANSFER ,  TRUE    );
		curl_setopt($curl, CURLOPT_HTTPHEADER     , $headers );
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER ,  FALSE   );
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST ,  FALSE   );
		curl_setopt($curl, CURLOPT_COOKIEJAR      , 'cookie' );
		curl_setopt($curl, CURLOPT_COOKIEFILE     , '/tmp'    );
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION ,  TRUE    );

		//	Curl - Execute
		$json = curl_exec($curl);
		curl_close($curl);

		//	JSON - Parse
		$json = json_decode($json, true);

		//	...
		if( $json['error'] ?? null ){
			throw new \Exception( json_encode($json['error']) );
		};

		return $json['result'] ?? null;
	}

	/** Get bitcoin address by label.
	 *
	 * <pre>
	 * //	Get bitcoin address.
	 * $this->Address();
	 *
	 * //	Get bitcoin address by label.
	 * $this->Address('TEST');
	 *
	 * //	Get new address generate always.
	 * $this->Address('TEST', null);
	 * </pre>
	 *
	 * @created  2019-08-28
	 * @param    string      $lable
	 * @param    string      $purpose
	 * @return   string      $address
	 */
	static function Address($label=null, $purpose='receive')
	{
		//	...
		try{
			/*
			//	Convert to md5.
			$label = md5($label);
			*/
			//	...
			$label = Encode($label);
			$label = nl2br($label);

			//	Get already generated address.
			if( $result = self::RPC('getaddressesbylabel',[$label]) ){

				//	Get first address.
				foreach( $result as $key => $val ){

					//	...
					/*
					if(!$purpose ){
						return $key;
					};
					*/

					//	...
					if( $purpose and $purpose === $val['purpose']){
						return $key;
					};
				};

				//	...
				throw new \Exception("Does not match this purpose. ({$purpose})");
			};

		}catch( \Exception $e ){

			//	...
			$error = json_decode($e->getMessage(), true);

			//	...
			if(($error['code'] ?? null) !== -11 ){
				throw $e;
			};
		};

		//	...
		return self::RPC('getnewaddress',[$label]);
	}

	/** Get wallet balance. That total each address.
	 *
	 * @created  2019-08-28
	 * @param    string      $address
	 * @return   integer     $btc
	 */
	static function Balance(string $address='')
	{
		//	Per address.
		if( $address ){
			return self::Received($address);
		}

		//	Wallet total amount
		return self::RPC('getbalance');
	}

	/** Amount(string|null $address) --> Balance(null), Recieve(string $address)
	 *
	 * @created  2019-08-28
	 * @param    string|null $address
	 * @return   integer     $result
	 */
	static function Amount($address=null)
	{
		if( $address ){
			return self::Received($address);
		}else{
			return self::Balance();
		};
	}

	/** Send bitcoin.
	 *
	 * @created  2019-08-28
	 * @param    string      $address
	 * @param    float       $amount
	 * @return   string      $transaction_id
	 */
	static function Send($address, $amount)
	{
		//	...
		$transaction_id = self::RPC('sendtoaddress',[$address, $amount]);

		//	...
		if( Env::Get('bitcoin')['database'] ?? null ){
			self::Database()->Send($address, $amount, $transaction_id);
		}

		//	...
		return $transaction_id;
	}

	/** Received bitcoin.
	 *
	 * @created  2019-08-28
	 * @param    string      $address
	 * @return   integer     $result
	 */
	static function Received(string $address)
	{
		return self::RPC('getreceivedbyaddress',[$address]);
	}

	/** Received bitcoin.
	 *
	 * @created  2019-08-28
	 * @param    string      $transaction_id
	 * @return   integer     $result
	 */
	static function Transaction($transaction_id)
	{
		return self::RPC('gettransaction',[$transaction_id]);
	}

	/** Get block information.
	 *
	 * @created  2019-08-28
	 * @param    string      $block_id
	 * @return   array       $result
	 */
	static function Block($block_id)
	{
		return self::RPC('getblock',[$block_id]);
	}

	/** Generate block.
	 *
	 * @created  2019-08-28
	 * @param    string      $address of receive reward
	 * @param    integer     $number of blocks
	 * @return   string      $address of mining reward
	 */
	static function Mining(string $address, int $number=1)
	{
		return self::RPC('generatetoaddress',[(int)$number, $address]);
	}

	/** Get Blockchain information.
	 *
	 * @created  2019-08-28
	 * @return   array
	 */
	static function InfoBlockchain()
	{
		return self::RPC('getblockchaininfo');
	}

	/** Get mining information.
	 *
	 * @created  2019-08-28
	 * @return   array
	 */
	static function InfoMining()
	{
		return self::RPC('getmininginfo');
	}

	/** Get wallet information.
	 *
	 * @created  2019-08-28
	 * @return   array
	 */
	static function InfoWallet()
	{
		return self::RPC('getwalletinfo');
	}

	/** Get address information.
	 *
	 * @created  2019-08-28
	 * @return   array
	 */
	static function InfoAddress($address)
	{
		return self::RPC('getaddressinfo', [$address]);
	}
}
