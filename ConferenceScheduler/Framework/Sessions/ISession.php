<?php

namespace Framework\Sessions;

interface ISession
{
    public function getSessionId();

    public function saveSession();

    public function destroySession();

    public function __get($name);

    public function __set($name, $value);
}