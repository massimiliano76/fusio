<?php
/*
 * Fusio
 * A web-application to create dynamically RESTful APIs
 * 
 * Copyright (C) 2015 Christoph Kappestein <k42b3.x@gmail.com>
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Fusio;

use Doctrine\DBAL\Connection;
use PSX\Dependency\ObjectBuilderInterface;

/**
 * Processor
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl-3.0
 * @link    http://fusio-project.org
 */
class Processor
{
	protected $connection;
	protected $factory;

	public function __construct(Connection $connection, Factory\Action $factory)
	{
		$this->connection = $connection;
		$this->factory    = $factory;
	}

	public function execute($actionId, Request $request)
	{
		$action = $this->connection->fetchAssoc('SELECT class, config FROM fusio_action WHERE id = :id', array('id' => $actionId));

		if(empty($action))
		{
			throw new ConfigurationException('Invalid action');
		}

		$config = !empty($action['config']) ? unserialize($action['config']) : array();

		return $this->factory->factory($action['class'])->handle($request, new Parameters($config));
	}
}