<?php

namespace TreeHouse\Queue\Exception;

class RescheduleException extends \Exception
{
    /**
     * @var \DateTime
     */
    private $rescheduleDate;

    /**
     * @var string
     */
    private $rescheduleMessage;

    /**
     * @param \DateTime $date
     */
    public function setRescheduleDate(\DateTime $date)
    {
        $this->rescheduleDate = $date;
    }

    /**
     * @return \DateTime
     */
    public function getRescheduleDate()
    {
        return $this->rescheduleDate;
    }

    /**
     * @param string|null $msg
     */
    public function setRescheduleMessage($msg = null)
    {
        $this->rescheduleMessage = $msg;
    }

    /**
     * @return string
     */
    public function getRescheduleMessage()
    {
        return $this->rescheduleMessage;
    }

    /**
     * @param string      $time
     * @param string|null $msg
     *
     * @return RescheduleException
     */
    public static function create($time, $msg = null)
    {
        $re = new RescheduleException($msg);
        $re->setRescheduleDate(new \DateTime($time));
        $re->setRescheduleMessage($msg);

        return $re;
    }
}
