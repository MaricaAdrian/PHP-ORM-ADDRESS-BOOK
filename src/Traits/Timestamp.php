<?php
namespace Traits;

trait Timestamp
{
    private \DateTimeInterface $createdAt;
    private \DateTimeInterface $updatedAt;

    // Method to initialize the timestamps, to be called by the consuming class
    public function initializeTimestamps(): void
    {
        $this->createdAt = new \DateTime(); // Sets createdAt to the current time
        $this->updatedAt = new \DateTime(); // Sets updatedAt to the current time
    }

    public function updateTimestamp(): void
    {
        $this->updatedAt = new \DateTime(); // Sets updatedAt to the current time
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }
}