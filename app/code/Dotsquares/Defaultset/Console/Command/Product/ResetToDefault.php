<?php

namespace Dotsquares\Defaultset\Console\Command\Product;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\App\Config\ConfigResource\ConfigInterface;
use Magento\Config\Model\Config\Factory;

class ResetToDefault extends Command
{
    protected $config;
    protected $configFactory;

    public function __construct(
        ConfigInterface $config,
        Factory $configFactory
    ) {
        $this->config = $config;
        $this->configFactory = $configFactory;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('vendor:module:reset-to-default')
            ->setDescription('Reset configuration to default values');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Resetting configuration to default values...');

        $configData = $this->config->getConfigData();
        $defaultConfig = [];

        foreach ($configData as $data) {
            if ($data['value'] != $data['default_value']) {
                $path = $data['path'];
                $defaultConfig[$path] = $data['default_value'];
            }
        }

        if (count($defaultConfig)) {
            $this->configFactory->create()->saveConfig(
                array_keys($defaultConfig),
                array_values($defaultConfig),
                0,
                0
            );

            $output->writeln('Reset completed.');
        } else {
            $output->writeln('No changes needed.');
        }
    }
}


// php bin/magento vendor:module:reset-to-default
