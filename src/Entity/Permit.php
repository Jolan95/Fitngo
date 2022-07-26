<?php

namespace App\Entity;

use App\Repository\PermitRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PermitRepository::class)]
class Permit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\Column]
    private ?bool $newsletter = null;

    #[ORM\Column]
    private ?bool $payment_online = null;

    #[ORM\Column]
    private ?bool $team_schedule = null;

    #[ORM\Column]
    private ?bool $live_chat = null;

    #[ORM\Column]
    private ?bool $virtual_training = null;

    #[ORM\Column]
    private ?bool $detailed_data = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isNewsletter(): ?bool
    {
        return $this->newsletter;
    }

    public function setNewsletter(bool $newsletter): self
    {
        $this->newsletter = $newsletter;

        return $this;
    }

    public function isPaymentOnline(): ?bool
    {
        return $this->payment_online;
    }

    public function setPaymentOnline(bool $payment_online): self
    {
        $this->payment_online = $payment_online;

        return $this;
    }

    public function isTeamSchedule(): ?bool
    {
        return $this->team_schedule;
    }

    public function setTeamSchedule(bool $team_schedule): self
    {
        $this->team_schedule = $team_schedule;

        return $this;
    }

    public function isLiveChat(): ?bool
    {
        return $this->live_chat;
    }

    public function setLiveChat(bool $live_chat): self
    {
        $this->live_chat = $live_chat;

        return $this;
    }

    public function isVirtualTraining(): ?bool
    {
        return $this->virtual_training;
    }

    public function setVirtualTraining(bool $virtual_training): self
    {
        $this->virtual_training = $virtual_training;

        return $this;
    }

    public function isDetailedData(): ?bool
    {
        return $this->detailed_data;
    }

    public function setDetailedData(bool $detailed_data): self
    {
        $this->detailed_data = $detailed_data;

        return $this;
    }
    public function setOptions(bool $common_data){
        $this->setDetailedData($common_data);
        $this->setLiveChat($common_data);
        $this->setTeamSchedule($common_data);
        $this->setPaymentOnline($common_data);
        $this->setNewsletter($common_data);
        $this->setVirtualTraining($common_data);

        return $this;
    }
}
