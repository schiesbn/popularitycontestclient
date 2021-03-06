<?php
/**
 * @author Joas Schilling <nickvergessen@owncloud.com>
 *
 * @copyright Copyright (c) 2015, ownCloud, Inc.
 * @license AGPL-3.0
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 */

namespace OCA\PopularityContestClient;

use OC\BackgroundJob\TimedJob;
use OCA\PopularityContestClient\AppInfo\Application;

class MonthlyReport extends TimedJob {

	/**
	 * MonthlyReport constructor.
	 */
	public function __construct() {
		// Run all 28 days
		$this->setInterval(28 * 24 * 60 * 60);
	}

	protected function run($argument) {
		$application = new Application();
		/** @var \OCA\PopularityContestClient\Collector $collector */
		$collector = $application->getContainer()->query('OCA\PopularityContestClient\Collector');
		$result = $collector->sendReport();

		if (!$result->succeeded()) {
			\OC::$server->getLogger()->info('Error while sending usage statistic');
		}
	}
}
