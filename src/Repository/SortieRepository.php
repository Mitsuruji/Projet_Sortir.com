<?php

namespace App\Repository;

use App\Data\SearchOptions;
use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }


    /**
     * Recupère les sorties en fonction des options de recherches
     */
    public function findSearch(SearchOptions $searchOptions)
    {
        $queryBuilder = $this
            ->createQueryBuilder('s')
            ->join('s.etat','etat')
            ->addSelect('etat')
            ->leftjoin('s.participantInscrit','insc')
            ->addSelect('insc')
            ->join('s.participantOrganisateur','orga')
            ->addSelect('orga')
            ->join('s.campusOrganisateur', 'camp')
            ->addSelect('camp')
            ;



//        if (!empty($searchOptions->getFilterIsPasInscris()) && !empty($searchOptions->getFilterIsInscris())) {
//            $queryBuilder = $queryBuilder
//                ->addSelect('insc');
//        }
        if (!empty($searchOptions->getFilterIsOrganisateur())) {
            $queryBuilder = $queryBuilder
                ->andWhere('orga.id LIKE :currentUser')
                ->setParameter('currentUser', $searchOptions->getCurrentUser());
        }

        if(!empty($searchOptions->getFilterIsInscris())) {
            $queryBuilder = $queryBuilder
                ->orWhere('insc.id LIKE :currentUser')
                ->setParameter('currentUser', $searchOptions->getCurrentUser());
        }

        if(!empty($searchOptions->getFilterIsPasInscris())) {
            $queryBuilder = $queryBuilder
                ->orWhere('insc.id != :currentUser')
                ->setParameter('currentUser', $searchOptions->getCurrentUser());
        }

        if (!empty($searchOptions->getFilterSortiesPassees())) {
            $queryBuilder = $queryBuilder
                ->orWhere('etat.id = 5');
        }

        if (!empty($searchOptions->getChoixCampus())) {
            $queryBuilder = $queryBuilder
                ->andWhere('camp.id LIKE :choixCampus')
                ->setParameter('choixCampus', $searchOptions->getChoixCampus());
        }

        if (!empty($searchOptions->getFilterNomSortie())) {
            $queryBuilder = $queryBuilder
                ->andWhere('s.nom LIKE :filterNomSortie')
                ->setParameter('filterNomSortie', "%{$searchOptions->getFilterNomSortie()}%");
        }

        if (!empty($searchOptions->getFilterDateMin())) {
            $queryBuilder = $queryBuilder
                ->andWhere('s.dateHeureDebut >= :filterDateMin')
                ->setParameter('filterDateMin', $searchOptions->getFilterDateMin());
        }

        if (!empty($searchOptions->getFilterDateMax())) {
            $queryBuilder = $queryBuilder
                ->andWhere('s.dateHeureDebut <= :filterDateMax')
                ->setParameter('filterDateMax', $searchOptions->getFilterDateMax());
        }


        $query = $queryBuilder->getQuery();
        $paginator = new Paginator($query);

        return $paginator;
    }
}
