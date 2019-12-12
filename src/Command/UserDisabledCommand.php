<?php


namespace App\Command;


use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UserDisabledCommand extends Command
{
    private $userRepository;

    private $em;

    public function __construct(UserRepository $userRepository, ObjectManager $em, $name = null) {
        parent::__construct($name);
        $this->userRepository = $userRepository;
        $this->em = $em;
    }

    public function configure()
    {
        $this->setName("user:disabled");
        $this->setDescription("Disabled user last_update +5 years");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $userDisabled = $this->userRepository->userDisabled();
        $countUserDisabled = count($userDisabled);

        if ($countUserDisabled) {
            $countThree = $countUserDisabled / 3;

            $progressBar = new ProgressBar($output, $countUserDisabled);
            $progressBar->setBarCharacter('<fg=green>⚬</>');
            $progressBar->setEmptyBarCharacter("<fg=red>⚬</>");
            $progressBar->setProgressCharacter("<fg=green>➤</>");
            $progressBar->setFormat(
                "<fg=white;bg=cyan> %status:-45s%</>\n%current%/%max% [%bar%] %percent:3s%%\n  %estimated:-20s%  %memory:20s%"
            );
            $progressBar->start();

            for ($i = 0; $i < $countUserDisabled; $i++) {
                $user = $this->userRepository->find($userDisabled[$i]->getId());
                $user->setIsActive(false);
                $this->em->flush();

                if ($i < $countThree) {
                    $progressBar->setMessage("Starting...", 'status');
                } elseif ($i < $countThree * 2) {
                    $progressBar->setMessage("Halfway :)", 'status');
                } else {
                    $progressBar->setMessage("Almost :D", 'status');
                }
                $progressBar->advance();
                usleep(200000);
            }

            $progressBar->setMessage("Done !", 'status');
            $progressBar->finish();
        } else {
            $output->writeln('Aucun utilisateur !');
        }
    }
}