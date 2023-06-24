<?php

/*
 * This file is part of the 3D Follow project.
 * (c) Loïck Piera <pyrech@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace qa;

use Castor\Attribute\AsOption;
use Castor\Attribute\AsTask;
use Symfony\Component\Process\Process;

use function Castor\get_context;
use function Castor\io;
use function Castor\notify;
use function Castor\parallel;

#[AsTask(namespace: 'qa', description: 'Run PHPUnit tests and PHP CS Fixer and PHPStan analysis')]
function all(): ?int
{
    [$phpCsFixer, $phpStan] = parallel(
        function () {
            return phpcsfixer(true, false);
        },
        function () {
            return phpstan(true, false);
        }
    );

    if (0 === $phpCsFixer->getExitCode() && 0 === $phpStan->getExitCode()) {
        notify('🎉 QA passes!');
        io()->success('🎉 QA passes!');
        echo $phpCsFixer->getOutput();
        echo $phpStan->getOutput();

        return 0;
    }
    if (0 !== $phpCsFixer->getExitCode()) {
        notify('🚨 QA fails! PHP CS Fixer failed.');
        io()->error('🚨 QA fails! PHP CS Fixer failed.');
        echo $phpCsFixer->getOutput();

        return $phpCsFixer->getExitCode();
    }
    if (0 !== $phpStan->getExitCode()) {
        notify('🚨 QA fails! PHPStan failed.');
        io()->error('🚨 QA fails! PHPStan failed.');
        echo $phpStan->getOutput();

        return $phpStan->getExitCode();
    }

    return 0;
}

#[AsTask(namespace: 'qa', description: 'Run PHP CS Fixer', aliases: ['cs'])]
function phpcsfixer(
    #[AsOption(shortcut: 'q', description: 'Run quietly the command')]
    bool $quiet = false,
    #[AsOption(shortcut: 'e', description: 'Return exit code instead of process')]
    bool $returnExitCode = true,
): null|int|Process {
    $process = docker_compose_run(
        'php vendor/bin/php-cs-fixer fix --config=../.php-cs-fixer.php --cache-file=../.php-cs-fixer.cache',
        c: get_context()->withQuiet($quiet),
    );

    if ($returnExitCode) {
        return $process->getExitCode();
    }

    return $process;
}

#[AsTask(namespace: 'qa', description: 'Run PHPStan analysis', aliases: ['phpstan'])]
function phpstan(
    #[AsOption(shortcut: 'q', description: 'Run quietly the command')]
    bool $quiet = false,
    #[AsOption(shortcut: 'e', description: 'Return exit code instead of process')]
    bool $returnExitCode = true,
): null|int|Process {
    $process = docker_compose_run(
        'php vendor/bin/phpstan analyse src/ -c phpstan.neon',
        c: get_context()->withQuiet($quiet),
    );

    if ($returnExitCode) {
        return $process->getExitCode();
    }

    return $process;
}

#[AsTask(namespace: 'qa', description: 'Run PHPUnit tests', aliases: ['test'])]
function phpunit(
    #[AsOption(shortcut: 'q', description: 'Run quietly the command')]
    bool $quiet = false,
    #[AsOption(shortcut: 'e', description: 'Return exit code instead of process')]
    bool $returnExitCode = true,
    #[AsOption(shortcut: 'filter', description: 'Filter tests to run')]
    string $filter = null,
): null|int|Process {
    docker_compose_run('php bin/console doctrine:database:create --if-not-exists --env=test');
    docker_compose_run('php bin/console doctrine:migration:migrate -n --allow-no-migration --env=test');

    if ($filter) {
        $command = sprintf('bin/phpunit --filter %s', $filter);
    } else {
        $command = 'bin/phpunit';
    }

    $process = docker_compose_run(
        $command,
        c: get_context()->withQuiet($quiet),
    );

    if ($returnExitCode) {
        return $process->getExitCode();
    }

    return $process;
}
