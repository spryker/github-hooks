<?php declare(strict_types = 1);

/**
 * Copyright © 2017-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace GitHook\Command\FileCommand\PreCommit;

use GitHook\Command\CommandConfigurationInterface;
use GitHook\Command\CommandResult;
use GitHook\Command\FileCommand\FileCommandInterface;
use GitHook\Helper\ProcessBuilderHelper;

class CodeStyleFixCommand implements FileCommandInterface
{

    use ProcessBuilderHelper;

    /**
     * @param \GitHook\Command\CommandConfigurationInterface $commandConfiguration
     *
     * @return \GitHook\Command\CommandConfigurationInterface
     */
    public function configure(CommandConfigurationInterface $commandConfiguration): CommandConfigurationInterface
    {
        $commandConfiguration
            ->setName('CodeStyle fixer')
            ->setDescription('Fixes all fixable CodeStyle bugs.')
            ->setAcceptedFileExtensions('php');

        return $commandConfiguration;
    }

    /**
     * @param string $file
     *
     * @return \GitHook\Command\CommandResult
     */
    public function run(string $file): CommandResult
    {
        $commandResult = new CommandResult();

        $processDefinition = ['vendor/bin/phpcbf', $file, '--standard=vendor/spryker/code-sniffer/Spryker/ruleset.xml'];
        $process = $this->buildProcess($processDefinition);
        $process->run();

        return $commandResult;
    }

}