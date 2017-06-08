<?php

namespace AppBundle\Command;

use AppBundle\Entity\User;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AppCreateAdminCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:create-admin')
            ->setDescription('Créer un nouvel administrateur')
            ->addArgument('email', InputArgument::REQUIRED, "Une adresse e-mail appartenant au domaine de l'Université Lyon 1.");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Services
        $io = new SymfonyStyle($input, $output);
        $userManager = $this->getContainer()->get('fos_user.user_manager');
        $validator = $this->getContainer()->get('validator');

        // Credentials
        $email = $input->getArgument('email');
        $password = $io->askHidden("Mot de passe ?", function ($password) {
            if (empty($password)) {
                throw new \RuntimeException("Le mot de passe ne peut être vide.");
            }

            return $password;
        });


        $admin = new User();
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setEmail($email);
        $admin->setEnabled(true);
        $admin->setPlainPassword($password);

        foreach ($validator->validate($admin) as $error) {
            $io->error($error->getMessage());
            return;
        }

        try {
            $userManager->updateUser($admin);
        } catch (UniqueConstraintViolationException $e) {
            $io->error("Un utilisateur possédant la même adresse e-mail existe déjà.");
            return;
        }

        $io->success("L'administrateur a bien été créé ! Vous pouvez maintenant vous connecter via l'interface web.");
    }
}
