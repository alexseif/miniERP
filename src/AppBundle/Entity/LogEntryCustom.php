<?php

namespace AppBundle\Entity;

use Gedmo\Loggable\Entity\LogEntry;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="ext_log_entries")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\LogEntryCustomRepository")
 */
class LogEntryCustom extends LogEntry
{

}
