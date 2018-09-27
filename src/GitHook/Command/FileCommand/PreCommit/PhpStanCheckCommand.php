<?php

/**
 * Copyright © 2017-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace GitHook\Command\FileCommand\PreCommit;

use GitHook\Command\CommandConfigurationInterface;
use GitHook\Command\CommandInterface;
use GitHook\Command\CommandResult;
use GitHook\Command\Context\CommandContextInterface;
use GitHook\Command\FileCommand\PreCommit\PhpStan\PhpStanConfiguration;
use Symfony\Component\Process\Process;

/**
 * If you see an error about something can't be autoloaded, check if there is an alias for the given class.
 */
class PhpStanCheckCommand implements CommandInterface
{
    /**
     * @param \GitHook\Command\CommandConfigurationInterface $commandConfiguration
     *
     * @return \GitHook\Command\CommandConfigurationInterface
     */
    public function configure(CommandConfigurationInterface $commandConfiguration)
    {
        $commandConfiguration
            ->setName('PhpStan check')
            ->setAcceptedFileExtensions('php');

        return $commandConfiguration;
    }

    /**
     * @param \GitHook\Command\Context\CommandContextInterface $context
     *
     * @return \GitHook\Command\CommandResultInterface
     */
    public function run(CommandContextInterface $context)
    {
        $phpStanConfiguration = new PhpStanConfiguration($context->getCommandConfig('phpstan'));
        $commandResult = new CommandResult();
        $filePath = $context->getFile();

        if (!$this->isFileAllowed($filePath, $phpStanConfiguration->getDirectories())) {
            return $commandResult;
        }

        $level = $this->getLevel($phpStanConfiguration, $context);

        $command = sprintf('vendor/bin/phpstan analyse %s -l %s -c %s', $filePath, $level, $phpStanConfiguration->getConfigPath());

        $process = new Process($command, PROJECT_ROOT);
        $process->run();

        if (!$process->isSuccessful()) {
            $commandResult
                ->setError(trim($process->getErrorOutput()))
                ->setMessage(trim($process->getOutput()));
        }

        return $commandResult;
    }

    /**
     * @param string $filePath
     * @param array $allowedDirectories
     *
     * @return bool
     */
    protected function isFileAllowed(string $filePath, array $allowedDirectories): bool
    {
        $fileDirectory = dirname($filePath);

        foreach ($allowedDirectories as $allowedDirectory) {
            if (strpos($fileDirectory, $allowedDirectory) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param \GitHook\Command\FileCommand\PreCommit\PhpStan\PhpStanConfiguration $phpStanConfiguration
     * @param \GitHook\Command\Context\CommandContextInterface $context
     *
     * @return int
     */
    protected function getLevel(PhpStanConfiguration $phpStanConfiguration, CommandContextInterface $context): int
    {
        $level = $phpStanConfiguration->getLevel();

        if (!$context->isModuleFile()) {
            return $level;
        }

        $modulePath = $context->getModulePath();
        $modulePhpstanConfigurationPath = $modulePath . 'phpstan.json';

        if (!file_exists($modulePhpstanConfigurationPath)) {
            return $level;
        }

        $moduleConfig = json_decode(file_get_contents($modulePhpstanConfigurationPath), true);
        if (!isset($moduleConfig['defaultLevel'])) {
            return $level;
        }

        return $moduleConfig['defaultLevel'];
    }
}
