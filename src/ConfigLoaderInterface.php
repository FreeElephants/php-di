<?php

namespace FreeElephants\DI;

interface ConfigLoaderInterface
{

    public function readConfig(string $scope): array;
}
