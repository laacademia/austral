<?php

namespace App\Repository;

use App\Entity\Turno;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Service\Cola\ColaInterface;
// use Doctrine\ORM\EntityManagerInterface;

/**
 * @method Turno|null find($id, $lockMode = null, $lockVersion = null)
 * @method Turno|null findOneBy(array $criteria, array $orderBy = null)
 * @method Turno[]    findAll()
 * @method Turno[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TurnoRepository extends ServiceEntityRepository
{
    private $cola;

    public function __construct(ManagerRegistry $registry, ColaInterface $cola)
    {
        parent::__construct($registry, Turno::class);        
        $this->cola = $cola;
    }

    public function sacarTurno($cola){
        $turno = new Turno();

        $turno
            ->setCola($cola)            
            ->setAtendido(false)
            ->setFechaAtendido(null);

        $em = $this->getEntityManager();
        $em->persist($turno);
        $em->flush();
        return $turno;
    }
    /**
    * @return Turno Return an Turno object
    */    
    function atenderProximo(){
        $turno = $this->cola->siguiente($this);
        if(!$turno){
            return null;
        }        
        $turno->atender();

        $em = $this->getEntityManager();
        $em->persist($turno);
        $em->flush();
        return $turno;        
    }

    function getEstadistica(){
        return array(
            'total_personas' => $this->getCantidadTurnos(),
            'total_atendidos' => $this->getCantidadAtendidos(),
            'total_no_atendidos' => $this->getCantidadNoAtendidos()
        );
    }
    function getCantidadTurnos(){
        return $this->createQueryBuilder('t')
            ->select('count(t.id)')
            ->getQuery()
            ->getSingleScalarResult();        
    }
    function getCantidadAtendidos(){
        return $this->createQueryBuilder('t')
            ->select('count(t.id)')
            ->andWhere('t.atendido = :val')
            ->setParameter('val', true)
            ->getQuery()
            ->getSingleScalarResult();
    }
    function getCantidadNoAtendidos(){
        return $this->createQueryBuilder('t')
            ->select('count(t.id)')
            ->andWhere('t.atendido = :val')
            ->setParameter('val', false)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
