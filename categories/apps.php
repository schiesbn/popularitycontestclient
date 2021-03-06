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

namespace OCA\PopularityContestClient\Categories;


use Doctrine\DBAL\Connection;
use OCP\IDBConnection;
use OCP\IL10N;

/**
 * Class Apps
 *
 * @package OCA\PopularityContestClient\Categories
 */
class Apps implements ICategory {
	/** @var IDBConnection */
	protected $connection;

	/** @var \OCP\IL10N */
	protected $l;

	/**
	 * @param IDBConnection $connection
	 * @param IL10N $l
	 */
	public function __construct(IDBConnection $connection, IL10N $l) {
		$this->connection = $connection;
		$this->l = $l;
	}

	/**
	 * @return string
	 */
	public function getCategory() {
		return 'apps';
	}

	/**
	 * @return string
	 */
	public function getDisplayName() {
		return (string) $this->l->t('App list <em>(For each app: name, version, is enabled?)</em>');
	}

	/**
	 * @return array (string => string|int)
	 */
	public function getData() {
		$query = $this->connection->getQueryBuilder();

		$query->select('*')
			->from('appconfig')
			->where($query->expr()->in('configkey', $query->createNamedParameter(
				['enabled', 'installed_version'],
				Connection::PARAM_STR_ARRAY
			)));
		$result = $query->execute();

		$data = [];
		while ($row = $result->fetch()) {
			if ($row['configkey'] === 'enabled' && $row['configvalue'] === 'no') {
				$data[$row['appid']] = 'disabled';
			}
			if ($row['configkey'] === 'installed_version' && !isset($data[$row['appid']])) {
				$data[$row['appid']] = $row['configvalue'];
			}
		}
		$result->closeCursor();

		return $data;
	}
}
