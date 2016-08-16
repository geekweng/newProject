<?php

namespace AppBundle\Service\System;

interface StatisticsService
{
	public function getOnlineCount($retentionTime);

	public function getloginCount($retentionTime);
}
