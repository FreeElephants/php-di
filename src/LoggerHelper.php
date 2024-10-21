<?php

namespace FreeElephants\DI;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

/**
 * @author samizdam <samizdam@inbox.ru>
 */
class LoggerHelper implements LoggerAwareInterface
{

    use LoggerAwareTrait;

    public function __construct(LoggerInterface $logger)
    {
        $this->setLogger($logger);
    }

    public function logRequestNotDeterminedService(string $interface): void
    {
        $msg = 'Requested service with type ' . $interface . ' is not set. Exception will be thrown. ';
        $context = [
            'typeName' => $interface
        ];
        $this->logger->critical($msg, $context);
    }

    public function logLazyLoading(string $interface, $service): void
    {
        $debugMsg = 'Set service type  ' . $interface . ' instance by lazy load. ';
        $context = [
            'typeName' => $interface,
            'instance' => $this->stringifyService($service),
        ];
        $this->logger->debug($debugMsg, $context);
    }

    public function logServiceRegistration($implementation, string $interface): void
    {
        $implementation = $this->stringifyService($implementation);
        $msg = 'Service with type ' . $interface . ' and implementation ' . $implementation . ' register. ';
        $context = [
            'interface'      => $interface,
            'implementation' => $implementation,
        ];
        $this->logger->debug($msg, $context);
    }

    public function logNotMatchedTypeInstance(string $typeName, $instance): void
    {
        $context = [
            'typeName' => $typeName,
            'instance' => $this->stringifyService($instance)
        ];
        $this->logger->critical('Given instance not belong to this type. Exception will be thrown. ', $context);
    }

    public function logServiceInstanceReplacing(string $typeName, $service, $previousServiceInstance): array
    {
        $debugMsg = 'Replace service type  ' . $typeName . ' instance with another. ';
        $context = [
            'typeName'    => $typeName,
            'instance'    => $this->stringifyService($service),
            'oldInstance' => $this->stringifyService($previousServiceInstance),
        ];
        $this->logger->debug($debugMsg, $context);
        return [$debugMsg, $context];
    }

    public function logRegisterServiceReplacing($implementation, string $interface, $oldImplementation): void
    {
        $msg = 'Replace registered service type ' . $interface . ' with another. ';
        $context = [
            'interface'         => $interface,
            'newImplementation' => $this->stringifyService($implementation),
            'oldImplementation' => $this->stringifyService($oldImplementation),
        ];
        $this->logger->debug($msg, $context);
    }

    public function logServiceSetting(string $typeName, $service): void
    {
        $debugMsg = 'Instance for service type ' . $typeName . ' was set. ';
        $context = [
            'typeName' => $typeName,
            'instance' => $this->stringifyService($service),
        ];
        $this->logger->debug($debugMsg, $context);
    }

    private function stringifyService($implementation): string
    {
        if($implementation instanceof CallableBeanContainer) {
            $implementation = 'user defined callable';
        } elseif(is_object($implementation)) {
            $implementation = get_class($implementation);
        }

        return (string) $implementation;
    }
}
