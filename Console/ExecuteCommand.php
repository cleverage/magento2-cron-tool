<?php
/*
 *    CleverAge/Magento2-Cron-Tool
 *    Copyright (C) 2017 Clever-Age
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace CleverAge\CronTool\Console;


use Magento\Framework\App\Area;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\State;
use Symfony\Component\Console\Command\Command;
use Magento\Cron\Model\Config;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ExecuteCommand
 * @package CleverAge\Cron\Console
 *
 * Allows to execute a specified job, without waiting for its schedule ; for development/debugging purpose mainly
 */
class ExecuteCommand extends Command
{

    /** @var Config */
    protected $cronConfig;

    /** @var ObjectManager */
    protected $objectManager;

    /** @var State */
    protected $_state;

    /**
     * ExecuteCommand constructor.
     *
     * @param Config      $cronConfig
     * @param State       $state
     * @param string|null $name
     */
    public function __construct(Config $cronConfig, State $state, $name = null)
    {
        parent::__construct($name);
        $this->cronConfig = $cronConfig;
        $this->objectManager = ObjectManager::getInstance();
        $this->_state = $state;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('cleverage:cron:execute');
        $this->setDescription('Run a given cron job, without waiting for its schedule');

        $this->addArgument('job_code');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @see \Magento\Framework\App\Cron::launch */
        $this->_state->setAreaCode(Area::AREA_CRONTAB);

        $jobCode = $input->getArgument('job_code');
        $jobConfiguration = $this->getJobConfiguration($jobCode);

        $instance = $jobConfiguration['instance'];
        $method = $jobConfiguration['method'];

        $output->writeln("Executing job {$jobCode} using {$instance}::{$method}");

        $this->objectManager->get($instance)->$method();
    }

    /**
     * @param string $jobCode
     *
     * @return array
     */
    protected function getJobConfiguration($jobCode)
    {
        $jobs = $this->cronConfig->getJobs();

        foreach ($jobs as $group => $jobList) {
            if (array_key_exists($jobCode, $jobList)) {
                return $jobList[$jobCode];
            }
        }

        throw new \UnexpectedValueException("Unknown job {$jobCode}");

    }


}
