<?php
namespace App\Traits;

use Auth;
use App\User;
use App\Models\Log;
use Session;

trait CommonTrait
{
    public function echeck() {return response()->json(array('success' => false, 'errors' => ['errors' => ['Got here.']]), 400);}

	public function get_time()
	{
		$date = new \DateTime();
		return $date->format('Y-m-d H:i:s');
	}

	public function generate_code($table, $col, $l)
	{
		switch($l)
		{
			case 4:
				$mi = 1000;
				$mx = 9999;
				break;

			case 8:
				$mi = 10000000;
				$mx = 99999999;
				break;

			case 9:
				$mi = 100000000;
				$mx = 999999999;
				break;

			case 10:
				$mi = 1000;
				$mx = 9999;
				break;
		}

		switch($table)
		{
			case 'users':
				if($l == 4)
				{
					$val = rand($mi, $mx);
				} elseif($l == 8) {
					do{
						$val = rand($mi, $mx);
						$data = User::where($col, $val)->get();
					}while(!$data->isEmpty());
				} else {
					do{
						$val = str_random(10);
						$data = User::where($col, $val)->get();
					}while(!$data->isEmpty());
				}
				break;

			case 'log':
				do{
					$val = strtoupper('DH'.rand($mi, $mx).str_random(4));
					$data = Tlog::where($col, $val)->get();
				}while(!$data->isEmpty());
				break;

			case 'batch':
				do{
					$val = '#'.rand($mi, $mx);
					$data = Batch::where($col, $val)->get();
				}while(!$data->isEmpty());
				break;
		}
		return $val;
	}

	public function log($user_id, $descrip, $path, $type='')
	{
		$log = new Log();
        $log->user_id = $user_id;
        if($type) $log->type = $type;
		$log->page_url = $path;
		$log->descrip = $descrip;
		$log->save();
	}

	public function ad()
	{
		Session::put('access_denied', 'YOU ARE NOT AUTHORIZED TO ACCESS THE REQUESTED PAGE.');
	}

	public function clean($string)
	{
		$string = str_replace(' ', '_', $string);
		return preg_replace('/[^A-Za-z0-9\-]/', '', $string);
	}
}
