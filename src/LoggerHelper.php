<?php

namespace FreeElephants\DI;

use Psr\Log\LoggerInterface;

/**
 * @author samizdam <samizdam@inbox.ru>
 */
class LoggerHelper
{

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param string $interface
     * @return string
     */
    public function logRequestNotDeterminedService(string $interface)
    {
        $msg = 'Requested service with type ' . $interface . ' is not set. Exception will be thrown. ';
        $context = [
            'typeName' => $interface
        ];
        $this->logger->critical($msg, $context);
    }

    /**
     * @param string $interface
     * @param $service
     */
    public function logLazyLoading(string $interface, $service)
    {
        $debugMsg = 'Set service type  ' . $interface . ' instance by lazy load. ';
        $context = [
            'typeName' => $interface,
            'instance' => $service
        ];
        $this->logger->debug($debugMsg, $context);
    }

    /**
     * @param string $implementation
     * @param string $interface
     */
    public function logServiceRegistration(string $implementation, string $interface)
    {
        $msg = 'Service with type ' . $interface . ' and implementation ' . $implementation . ' register. ';
        $context = [
            'interface' => $interface,
            'implementation' => $implementation,
        ];
        $this->logger->debug($msg, $context);
    }

    public function logNotMatchedTypeInstance(string $typeName, $instance)
    {
        $context = [
            'typeName' => $typeName,
            'instance' => $instance
        ];
        $this->logger->critical('Given instance not belong to this type. Exception will be thrown. ', $context);
    }

    /**
     * @param string $typeName
     * @param $service
     * @param $previousServiceInstance
     * @return array
     */
    public function logServiceInstanceReplacing(string $typeName, $service, $previousServiceInstance)
    {
        $debugMsg = 'Replace service type  ' . $typeName . ' instance with another. ';
        $context = [
            'typeName' => $typeName,
            'instance' => $service,
            'oldInstance' => $previousServiceInstance,
        ];
        $this->logger->debug($debugMsg, $context);
        return array($debugMsg, $context);
    }

    /**
     * @param string $implementation
     * @param string $interface
     * @param $oldImplementation
     */
    public function logRegisterServiceReplacing(string $implementation, string $interface, $oldImplementation)
    {
        $msg = 'Replace registered service type ' . $interface . ' with another. ';
        $context = [
            'interface' => $interface,
            'newImplementation' => $implementation,
            'oldImplementation' => $oldImplementation,
        ];
        $this->logger->debug($msg, $context);
    }

    /**
     * @param string $typeName
     * @param $service
     */
    public function logServiceSetting(string $typeName, $service)
    {
        $debugMsg = 'Instance for service type ' . $typeName . ' was set. ';
        $context = [
            'typeName' => $typeName,
            'instance' => $service
        ];
        $this->logger->debug($debugMsg, $context);
    }
}