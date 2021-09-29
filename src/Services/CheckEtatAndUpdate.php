<?php

namespace App\Services;

use App\Entity\Sortie;
use App\Entity\SortieHistorisee;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;

class CheckEtatAndUpdate
{
    public function checkEtatAndUpdate(Paginator $sorties, EntityManagerInterface $entityManager)
    {
        $now = new \DateTime();
        foreach ($sorties as $sortie) {
            /* @var $sortie Sortie */

            if ($sortie->getEtat()->getId() === 2 &&
                ($sortie->getParticipantInscrit()->count() >= $sortie->getNbInscriptionsMax() or
                    $sortie->getDateLimiteInscription() < $now)) {
                $etatCloture = $entityManager->getReference('App:Etat', '3');
                $sortie->setEtat($etatCloture);
                $entityManager->persist($sortie);
                $entityManager->flush();
            }

            if (($sortie->getEtat()->getId() === 2 or $sortie->getEtat()->getId() === 4) &&
                date_timestamp_get($now) > date_timestamp_get($sortie->getDateHeureDebut())
                + date_timestamp_get($sortie->getDuree()) +
                timezone_offset_get(date_timezone_get($sortie->getDuree()), $sortie->getDuree())) {
                $etatTermine = $entityManager->getReference('App:Etat', '5');
                $sortie->setEtat($etatTermine);
                $entityManager->persist($sortie);
                $entityManager->flush();

            }

            if (($sortie->getEtat()->getId() !== 1) &&
                $now > date_create_immutable(date_format($sortie->getDateHeureDebut(), 'Y-m-d H:i:s'))
                    ->add(date_interval_create_from_date_string("30 days"))) {

                $sortieHistorisee = new SortieHistorisee();

                $sortieHistorisee->setNom($sortie->getNom());
                $sortieHistorisee->setDateHeureDebut($sortie->getDateHeureDebut());
                $sortieHistorisee->setDateLimiteInscription($sortie->getDateLimiteInscription());
                $sortieHistorisee->setDuree($sortie->getDuree());
                $sortieHistorisee->setNbInscriptionsMax($sortie->getNbInscriptionsMax());
                $sortieHistorisee->setMotifAnnulation($sortie->getMotifAnnulation());
                $sortieHistorisee->setInfosSortie($sortie->getInfosSortie());
                $sortieHistorisee->setEtat($sortie->getEtat()->getLibelle());
                $sortieHistorisee->setCampusOrganisateur($sortie->getCampusOrganisateur()->getNom());
                $sortieHistorisee->setSortieLieu($sortie->getSortieLieu()->getNom());
                $sortieHistorisee->setSortieVille($sortie->getSortieLieu()->getLieuVille()->getNom());
                $sortieHistorisee->setParticipantOrganisateur($sortie->getParticipantOrganisateur()->getNom());
                $sortieHistorisee->setParticipantInscris($sortie->getParticipantInscrit()->getValues());

                $entityManager->persist($sortieHistorisee);
                $entityManager->flush();

                $entityManager->remove($sortie);
                $entityManager->flush();
            }
        }

    }

}
