<?php

/*
 * (c) Nathalie Verdavoir <nathalie.verdavoir@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Nat\DeployBundle\Command\Test;

use Nat\DeployBundle\Service\Message;
use Nat\DeployBundle\Service\RunProcess;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

// the "name" and "description" arguments of AsCommand replace the
// static $defaultName and $defaultDescription properties
#[AsCommand(
    name: 'nat:test',
    description: 'Test',
    hidden: false,
    aliases: ['nat:t']
)]
class Test extends Command
{
    private $filesystem;
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        
        $this->filesystem = new Filesystem();
        $this->filesystem->mirror('/public', '/testMirror');
        return Command::SUCCESS;
    }
}
