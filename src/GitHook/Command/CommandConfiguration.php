<?php

/**
 * Copyright © 2017-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace GitHook\Command;

class CommandConfiguration implements CommandConfigurationInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $description = '';

    /**
     * @var array
     */
    protected $acceptedFileExtensions = [];

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return \GitHook\Command\CommandConfigurationInterface
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return \GitHook\Command\CommandConfigurationInterface
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return array
     */
    public function getAcceptedFileExtensions()
    {
        return $this->acceptedFileExtensions;
    }

    /**
     * @param string|array $fileExtensions
     *
     * @return \GitHook\Command\CommandConfigurationInterface
     */
    public function setAcceptedFileExtensions($fileExtensions)
    {
        $this->acceptedFileExtensions = (array)$fileExtensions;

        return $this;
    }
}
