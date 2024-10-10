<?php

    namespace NeoxDashBoard\NeoxDashBoardBundle\Message;

    
    class NeoxDashDomainMessage
    {
        
        public function __construct(int $domainId)
        {
            $this->domainId = $domainId;
        }

        public function getDomainId(): int
        {
            return $this->domainId;
        }
    }