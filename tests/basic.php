<?php

require_once dirname(__file__) . '/../src/CampaignMonitor.php';

$cm = new CampaignMonitor('Your API Key');
$cm->set_cache_options(dirname(__file__) . '/cache/', 60 * 60);
$cm->client('Your client id');

$cm->get_campaigns();

?>