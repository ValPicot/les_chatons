<?php

namespace App\Command;

use App\Entity\User;
use App\Service\RandomService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
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
        $this->setDescription('Disabled user last_update +5 years');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $userDisabled = $this->em->getRepository(User::class)->userDisabled();
        $countUserDisabled = count($userDisabled);

        if ($countUserDisabled) {
            $countThree = $countUserDisabled / 3;

            $progressBar = new ProgressBar($output, $countUserDisabled);
            $progressBar->setBarCharacter('<fg=green>⚬</>');
            $progressBar->setEmptyBarCharacter('<fg=red>⚬</>');
            $progressBar->setProgressCharacter('<fg=green>|</>');
            $progressBar->setFormat(
                "<fg=white;bg=cyan> %status:-45s%</>\n%current%/%max% [%bar%] %percent:3s%%\n  %estimated:-20s%  %memory:20s%"
            );
            $progressBar->start();

            $i = 0;
            foreach ($userDisabled as $user) {
                ++$i;
                $this->randomService->randomUser($user);

                if ($i < $countThree) {
                    $progressBar->setMessage('Starting...', 'status');
                } elseif ($i < $countThree * 2) {
                    $progressBar->setMessage('Halfway :)', 'status');
                } else {
                    $progressBar->setMessage('Almost :D', 'status');
                }
                $progressBar->advance();
                usleep(200000);
            }

            $progressBar->setMessage('Done !', 'status');
            $progressBar->finish();
            $output->writeln('');
        } else {
            $output->writeln('Aucun utilisateur !');
        }
    }
}
