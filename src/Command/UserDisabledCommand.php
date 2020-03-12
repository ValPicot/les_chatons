<?php

namespace App\Command;

use App\Entity\User;
use App\Service\RandomService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UserDisabledCommand extends Command
{
    private $em;

    private $randomService;

    public function __construct(EntityManagerInterface $em, RandomService $randomService, $name = null)
    {
        parent::__construct($name);
        $this->em = $em;
        $this->randomService = $randomService;
    }

    public function configure()
    {
        $this->setName('user:disabled');
        $this->setDescription('Disabled user last_update -5 years by default');

        $this->addOption('years', null, InputOption::VALUE_REQUIRED, 'Number of Years', 5);
        $this->addOption('limit', null, InputOption::VALUE_REQUIRED, 'Limit select users');
        $this->addOption('batch_size', null, InputOption::VALUE_REQUIRED, 'Batch size of flush', 100);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $limit = $input->getOption('limit');
        $years = $input->getOption('years');
        $batch_size = $input->getOption('batch_size');

        $userDisabled = $this->em->getRepository(User::class)->userDisabled($years, $limit);
        $countUserDisabled = $this->em->getRepository(User::class)->countUserDisabled($years);
        //var_dump(count($userDisabled));var_dump((int)$countUserDisabled);

        if ($countUserDisabled) {
            $progressBar = new ProgressBar($output, $countUserDisabled);
            $progressBar->setBarCharacter('<fg=green>⚬</>');
            $progressBar->setEmptyBarCharacter('<fg=red>⚬</>');
            $progressBar->setProgressCharacter('<fg=green>|</>');
            $progressBar->setFormat(
                "<fg=white;bg=cyan> %status:-45s%</>\n%current%/%max% [%bar%] %percent:3s%%\n  %estimated:-20s%  %memory:20s%"
            );
            $progressBar->start();
            $progressBar->setMessage('Run ...', 'status');
            do {
                foreach ($userDisabled as $key => $user) {
                    $this->randomService->randomUser($user);
                    $progressBar->advance();

                    if (0 === $key % $batch_size) {
                        $this->em->flush();
                        sleep(1.5);
                    }
                }
                $this->em->flush();
                $userDisabled = $this->em->getRepository(User::class)->userDisabled($years, $limit);
            } while (count($userDisabled) > 0);

            $progressBar->setMessage('Done !', 'status');
            $progressBar->finish();
            $output->writeln('');
        } else {
            $output->writeln('Aucun utilisateur !');
        }
    }
}
