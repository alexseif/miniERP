<?php

namespace AppBundle\Entity;

use Gedmo\Loggable\Entity\Repository\LogEntryRepository;

/**
 * The LogEntryRepository has some useful functions
 * to interact with log entries.
 *
 * @author Gediminas Morkevicius <gediminas.morkevicius@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class LogEntryCustomRepository extends LogEntryRepository
{

    /**
     * Loads all log entries for the given entity
     *
     * @param object $entity
     *
     * @return LogEntry[]
     */
    public function findByUsername($username)
    {
        return $this->createQueryBuilder('ele')
                        ->where("ele.username = ?1")
                        ->setParameter('1', $username)
                        ->getQuery()
                        ->getResult();
    }

    /**
     * Loads all log entries for the given entity
     *
     * @param object $entity
     *
     * @return LogEntry[]
     */
    public function findGroupByUsername()
    {
        return $this->createQueryBuilder('ele')
                        ->select('ele, COUNT(ele.username) as cUpdates, ele.username')
                        ->groupBy("ele.username")
                        ->where('ele.username IS NOT NULL')
                        ->orderBy('cUpdates', 'DESC')
                        ->getQuery()
                        ->getResult();
    }

}
