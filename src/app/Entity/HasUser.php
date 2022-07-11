<?php

namespace App\Entity;

interface HasUser
{
    public function setUser(?User $user);
    public function getUser(): ?User;
}