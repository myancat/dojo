<?php

namespace dojo\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class HelloWorld extends Command
{

	private
		$fruitsPrices,
		$fruitsTranslations;

	public function __construct()
	{
		parent::__construct();

		$this->fruitsPrices = [
        	'apples' => 100,
        	'mele' => 100,
        	'pommes' => 100,
        	'cherries' => 75,
        	'bananas' => 150,
        ];
	}

    protected function configure()
    {
        $this->setName('hello')
            ->setDescription('Say hello world')
            ->addArgument('fruit', InputArgument::REQUIRED, 'fruit name');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
		$orderedFruits = preg_split("/(\s|\,)/", $input->getArgument('fruit'));

		$orderedFruitTypes = [
			'apples' => 0,
			'mele' => 0,
        	'pommes' => 0,
        	'cherries' => 0,
        	'bananas' => 0,
		];

		$sum = 0;

		foreach ($orderedFruits as $fruitName)
		{
			$sum += $this->fruitsPrices[$fruitName];
			$orderedFruitTypes[$fruitName] ++;
			$sum = $this->applySumRules($fruitName, $orderedFruitTypes, $sum);
			$output->writeln($fruitName .": ". $sum);
		}


		$output->writeln('Total : ' . $sum);

    }

    private function applySumRules($fruitName, array $orderedFruitTypes, $sum)
    {
    	$closure = [
    		'cherries' => function($orderedFruitTypes, $sum) {
    			return $this->applyCherryRules($orderedFruitTypes, $sum);
    		},
    		'pommes' => function($orderedFruitTypes, $sum) {
    			return $this->applyPommeRules($orderedFruitTypes, $sum);
    		},
    		'mele' => function($orderedFruitTypes, $sum) {
    			return $this->applyPommeRules($orderedFruitTypes, $sum);
    		},
    		'apples' => function($orderedFruitTypes, $sum) {
    			return $this->applyPommeRules($orderedFruitTypes, $sum);
    		},
    		'bananas' => function($orderedFruitTypes, $sum){
    			return $this->applyBananaRules($orderedFruitTypes, $sum);
    		},
    	];
    	$sum = $closure[$fruitName]($orderedFruitTypes, $sum);

    	$sum = $this->applyFruitsRules($orderedFruitTypes, $sum);

    	return $sum;
    }

    private function applyCherryRules(array $orderedFruitTypes, $sum)
    {
    	$cherryNumber = $orderedFruitTypes['cherries'] % 2;

    	if ($cherryNumber == 0) {
    		$sum -= 30;
    	}

		return $sum;
    }

    private function applyPommeRules(array $orderedFruitTypes, $sum)
    {
    	$pommeNumber = ($orderedFruitTypes['pommes'] + $orderedFruitTypes['apples'] + $orderedFruitTypes['mele']) % 4;

    	if ($pommeNumber == 0) {
    		$sum -= $this->fruitsPrices['pommes'];
    	}

		return $sum;
    }

    private function applyFruitsRules(array $orderedFruitTypes, $sum)
    {
    	$fruitsNumber = array_sum($orderedFruitTypes) % 5;

    	if ($fruitsNumber == 0) {
    		$sum -= 200;
    	}

		return $sum;
    }

    private function applyBananaRules(array $orderedFruitTypes, $sum)
    {
    	$bananaKey = 'bananas';

    	$bananaNumber = $orderedFruitTypes[$bananaKey] % 2;

    	if ($bananaNumber == 0) {
    		$sum -= $this->fruitsPrices[$bananaKey];
    	}

		return $sum;
    }
}
