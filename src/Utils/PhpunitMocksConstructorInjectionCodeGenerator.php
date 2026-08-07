<?php

declare(strict_types=1);

namespace FreeElephants\DI\Utils;

class PhpunitMocksConstructorInjectionCodeGenerator
{
    public function generateCreateInstanceMethodWithMockedConstructorNullableArgs(string $className): string
    {
        $reflectedClass = new \ReflectionClass($className);
        $constructor = $reflectedClass->getConstructor();

        $methodSignature = sprintf("private function create%s(\n", $reflectedClass->getShortName());

        $methodBody = sprintf("\treturn new \\%s(\n", $className);
        foreach ($constructor->getParameters() as $param) {
            $argName = $param->getName();
            $argType = $param->getType()->getName();

            $methodSignature .= sprintf("\t?\\%s $%s = null,\n", $argType, $argName);

            $methodBody .= sprintf("\t\t$%s ?: \$this->createMock(\\%s::class),\n", $argName, $argType);
        }
        $methodSignature .= sprintf("\t): \\%s {\n", $className);
        $methodBody .= "\t);\n";

        return $methodSignature . $methodBody . "}\n";
    }
}
