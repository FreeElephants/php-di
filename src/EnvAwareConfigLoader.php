<?php

namespace FreeElephants\DI;

class EnvAwareConfigLoader implements ConfigLoaderInterface
{
    private const DEFAULT_ENV_VAR_NAME = 'ENV';
    private string $envVariableName;
    private string $configBasePath;

    public function __construct(string $configBasePath, string $envVariableName = self::DEFAULT_ENV_VAR_NAME)
    {
        $this->configBasePath = $configBasePath;
        $this->envVariableName = $envVariableName;
    }

    public function readConfig(string $scope): array
    {
        $commonComponentsFile = sprintf($this->configBasePath . '/%s.php', $scope);
        if (file_exists($commonComponentsFile)) {
            $commonComponents = require $commonComponentsFile;
        } else {
            throw new \RuntimeException('Required common config file does not exists: ' . $commonComponentsFile);
        }

        $envComponents = [];
        if ($env = getenv($this->envVariableName)) {
            $envComponentsFile = sprintf($this->configBasePath . '/%s.%s.php', $scope, $env);
            if (file_exists($envComponentsFile)) {
                $envComponents = require $envComponentsFile;
            }
        }

        return $this->merge($commonComponents, $envComponents);
    }

    private function merge(array $commonComponents, array $envComponents): array
    {
        $result = $commonComponents;
        foreach ($envComponents as $key => $config) {
            if (isset($commonComponents[$key]) && is_array($commonComponents[$key])) {
                $result[$key] = $this->merge($commonComponents[$key], $config);
            } else {
                $result[$key] = $config;
            }
        }

        return $result;
    }
}
