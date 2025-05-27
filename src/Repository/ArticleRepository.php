<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Article>
 *
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    /**
     * Récupère les articles pour DataTables avec pagination, tri et recherche.
     *
     * @param int $start Début de la pagination
     * @param int $length Nombre d'éléments par page
     * @param string|null $search Terme de recherche
     * @param string $orderColumn Colonne pour tri
     * @param string $orderDir Direction du tri ('ASC' ou 'DESC')
     * 
     * @return array ['data' => Article[], 'totalCount' => int, 'filteredCount' => int]
     */
    public function findForDataTable(int $start, int $length, ?string $search, string $orderColumn, string $orderDir): array
    {
        $qb = $this->createQueryBuilder('a')
        ->select('a', 'c', 'COUNT(DISTINCT com.id) AS HIDDEN commentsCount', 'COUNT(DISTINCT l.id) AS HIDDEN likesCount')
        ->leftJoin('a.categories', 'c')
        ->leftJoin('a.commentaires', 'com')
        ->leftJoin('a.articleLikes', 'l')
        ->groupBy('a.id');
    

        if ($search) {
            $qb->andWhere('a.titre LIKE :search OR c.nom LIKE :search')
               ->setParameter('search', '%' . $search . '%');
        }

        $totalCount = (int) $this->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $filteredCountQb = $this->createQueryBuilder('a')
            ->select('COUNT(DISTINCT a.id)')
            ->leftJoin('a.categories', 'c');

        if ($search) {
            $filteredCountQb->andWhere('a.titre LIKE :search OR c.nom LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        $filteredCount = (int) $filteredCountQb->getQuery()->getSingleScalarResult();

        // Tri
        switch ($orderColumn) {
            case 'commentsCount':
                $qb->orderBy('commentsCount', $orderDir);
                break;
            case 'likesCount':
                $qb->orderBy('likesCount', $orderDir);
                break;
            case 'categories':
                $qb->orderBy('c.nom', $orderDir);
                break;
            default:
            $allowedFields = ['id', 'titre', 'dateCreation'];
                if (in_array($orderColumn, $allowedFields)) {
                    $qb->orderBy('a.' . $orderColumn, $orderDir);
                } else {
                    $qb->orderBy('a.id', 'DESC');
                }
                break;
        }

        $qb->setFirstResult($start)
           ->setMaxResults($length);

        return [
            'data' => $qb->getQuery()->getResult(),
            'totalCount' => $totalCount,
            'filteredCount' => $filteredCount,
        ];
    }

    /**
     * Recherche des articles par titre (autocomplete).
     *
     * @param string $query Terme de recherche
     * @param int $limit Nombre maximum de résultats
     * @return Article[]
     */
    public function searchByTitle(string $query, int $limit = 10): array
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.categories', 'c')
            ->addSelect('c')
            ->where('a.titre LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->orderBy('a.dateCreation', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
