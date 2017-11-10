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


use Magento\Cron\Model\Config;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\TableHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ListCommand
 * @package CleverAge\Cron\Console
 *
 * List the defined jobs in Magento XML configuration
 */
class ListCommand extends Command
{

    /** @var Config */
    protected $cronConfig;

    /**
     * ListCommand constructor.
     *
     * @param Config      $cronConfig
     * @param string|null $name
     */
    public function __construct(Config $cronConfig, $name = null)
    {
        parent::__construct($name);
        $this->cronConfig = $cronConfig;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('cleverage:cron:list');
        $this->setDescription('List all cron jobs defined in XML confiugration');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $jobs = $this->cronConfig->getJobs();

        /** @var TableHelper $table */
        $table = $this->getHelperSet()->get('table');
        $table->setHeaders(['Group', 'Job', 'Call', 'Schedule']);

        foreach ($jobs as $group => $jobList) {
            foreach ($jobList as $job) {
                $schedule = '';
                if (array_key_exists('schedule', $job)) {
                    $schedule = $job['schedule'];
                }

                $table->addRow([$group, $job['name'], $job['instance'] . '::' . $job['method'], $schedule]);
            }
        }

        $table->render($output);
    }
}
