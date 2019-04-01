<?php

use Slim\Http\Request;
use Slim\Http\Response;
use Squid\Patreon\Patreon;
use Squid\Patreon\OAuth;

$app->get('/patreon[/{format:json}]', function ($request, $response, $args) {

	$data = getPatreonObject($this);

	if(isset($args['format']) && $args['format']==='json') {
		

		$patrons = array_filter($data['patrons'], function($patron){
			return $patron['is_active'];
		});
		//todo: oh im so bad with php
		foreach ($patrons as $index => $patron) {
			unset($patrons[$index]['picture']);
			unset($patrons[$index]['is_active']);
			unset($patrons[$index]['per_payment']);
			unset($patrons[$index]['total_amount']);
		};

		return $response->withJson($patrons)->withHeader('Access-Control-Allow-Origin', '*');
	} else {
		return $this->view->render($response, 'patreon.html', $data);
	};
});

function getPatreonObject($app) {
	$settings = $app->get('settings')['tauceti'];
	$patreonFile = $settings['storage'] . 'patreon_tokens';
	$cacheFile = $settings['cache'] . 'patreon';
	$tokens = $settings['patreon'];
	$data = null;

	//file cache
	if(file_exists($cacheFile)) {
		$data = unserialize(file_get_contents($cacheFile));
	}
	
	if(!file_exists($cacheFile) || time() - filemtime($cacheFile) > 1800) {//30 min
		
		try {
			if(file_exists($patreonFile)) {
				$baseTokens = unserialize(file_get_contents($patreonFile));
				$tokens['access_token'] = $baseTokens['access_token'];
				$tokens['refresh_token'] = $baseTokens['refresh_token'];
				$tokens['expires_in'] = $baseTokens['expires_in'];
			};

			if(!isset($tokens['expires_in']) || $tokens['expires_in'] < (time() + 604800)) {//refresh a week before expiration
				error_log(serialize($tokens));
				$OA = new OAuth($tokens['patreon_client_id'], $tokens['patreon_client_secret'], 'redirect');
				$tokens = $OA->getNewToken($tokens['refresh_token']);
				$tokens['expires_in'] = time() + $tokens['expires_in'];
				file_put_contents($patreonFile, serialize($tokens));
			};

			$patreon = new Patreon($tokens['access_token']);

			$campaign = $patreon->campaigns()->getMyCampaignWithPledges();

			function associatedCkey($name, $associated_ckeys){
				if(isset($associated_ckeys[$name])) {
					return $associated_ckeys[$name];
				};

				return $name;
			};

			//preparing data
			$patrons = $campaign->pledges->mapWithKeys(function ($pledge) {
				return [$pledge->patron->id => [
					'name' => $pledge->patron->full_name,
					'picture' => $pledge->patron->image_url,
					'per_payment' => number_format($pledge->amount_cents / 100, 2),
					'total_amount' => number_format($pledge->total_historical_amount_cents / 100, 2),
					'is_active' => $pledge->isActive(),/*проверку на деклинед */
					'reward_price' => $pledge->hasReward() ? $pledge->reward->getPrice() : null,
					'reward' => $pledge->hasReward() ? $pledge->reward->title : null,
				]];
			})->toArray();

			foreach ($patrons as $id => $patron){
				if(isset($settings['patreon']['associated_ckeys'][$patron['name']])) {
					$patrons[$id]['name'] = $settings['patreon']['associated_ckeys'][$patron['name']];
				};
			};

			array_multisort(array_column($patrons, 'total_amount'), SORT_DESC, $patrons);

			$currentGoal; //current or last complited goal

			$campaign->goals->sortBy('pledge_sum');
			foreach ($campaign->goals as $goal){
				if(!$goal->isComplete()) {
					$currentGoal = $goal;
					break;
				}
			};

			if(!isset($currentGoal)) {
				$currentGoal = $campaign->goals->last();
			};

			$data = [
				'pledge_url' => $campaign->pledge_url,
				'title' => "{$campaign->creator->full_name} is creating {$campaign->creation_name}",
				'patrons' => $patrons,
				'pledge_sum' => $campaign->pledge_sum,
				'goal' => [
					'title' => $currentGoal->title,
					'description' => $currentGoal->description,
					'completed_percentage' => $currentGoal->completed_percentage,
					'amount_cents' => $currentGoal->amount_cents,
				],
			];			

			file_put_contents($cacheFile, serialize($data));

		} catch (Exeption $e) {
		};

	};

	return $data;
}
