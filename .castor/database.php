<?php

/*
 * This file is part of the 3D Follow project.
 * (c) Loïck Piera <pyrech@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Castor\Attribute\AsTask;

use function Castor\context;
use function Castor\io;
use function docker\docker_compose;

#[AsTask(description: 'Connect to the PostgreSQL database', name: 'db:client', aliases: ['postgres', 'pg'])]
function postgres_client(): void
{
    io()->title('Connecting to the PostgreSQL database');

    docker_compose(['exec', 'postgres', 'psql', '-U', 'app', 'app'], context()->toInteractive());
}
