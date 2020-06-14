<?php

declare(strict_types=1);

namespace JMS\Serializer\Metadata\Driver;

class DocBlockTypeResolver
{
    /** resolve type hints from property */
    private const CLASS_PROPERTY_TYPE_HINT_REGEX = "#@var[\s]*([^\n\$\s]*)#";
    /** resolve single use statements */
    private const SINGLE_USE_STATEMENTS_REGEX = '/^[^\S\r\n]*use[\s]*([^;\n]*)[\s]*;$/m';
    /** resolve group use statements */
    private const GROUP_USE_STATEMENTS_REGEX = '/^[^\S\r\n]*use[[\s]*([^;\n]*)[\s]*{([a-zA-Z0-9\s\n\r,]*)};$/m';
    private const GLOBAL_NAMESPACE_PREFIX = "\\";

    public function getPropertyDocblockTypeHint(\ReflectionProperty $reflectionProperty): ?string
    {
        preg_match_all(self::CLASS_PROPERTY_TYPE_HINT_REGEX, $reflectionProperty->getDocComment(), $matchedDocBlockParameterTypes);
        $typeHint = trim($matchedDocBlockParameterTypes[1][0]);

        $unionTypeHint = [];
        foreach (explode("|", $typeHint) as $singleTypeHint) {
            if ($singleTypeHint !== "null") {
                $unionTypeHint[] = $singleTypeHint;
            }
        }
        $typeHint = implode("|", $unionTypeHint);
        if (count($unionTypeHint) > 1) {
            throw new \InvalidArgumentException("Can't use union type {$typeHint} for collection in {$reflectionProperty->getDeclaringClass()->getName()}:{$reflectionProperty->getName()}");
        }

        if (strpos($typeHint, "[]") === false) {
            throw new \InvalidArgumentException("Can't use incorrect type {$typeHint} for collection in {$reflectionProperty->getDeclaringClass()->getName()}:{$reflectionProperty->getName()}");
        }

        $typeHint = rtrim($typeHint, "[]");
        if (!$this->hasGlobalNamespacePrefix($typeHint) && !$this->isPrimitiveType($typeHint)) {
            $typeHint = $this->expandClassNameUsingUseStatements($typeHint, $this->getDeclaringClassOrTrait($reflectionProperty), $reflectionProperty);
        }

        return "array<" . ltrim($typeHint, "\\") . ">";
    }

    private function expandClassNameUsingUseStatements(string $typeHint, \ReflectionClass $declaringClass): string
    {
        $expandedClassName = $declaringClass->getNamespaceName() . "\\" . $typeHint;
        if (class_exists($expandedClassName)) {
            return $expandedClassName;
        }

        $classContents = file_get_contents($declaringClass->getFileName());
        $foundUseStatements = $this->gatherGroupUseStatements($classContents);
        $foundUseStatements = array_merge($this->gatherSingleUseStatements($classContents), $foundUseStatements);

        foreach ($foundUseStatements as $statementClassName) {
            if ($alias = explode("as", $statementClassName)) {
                if (array_key_exists(1, $alias) && trim($alias[1]) === $typeHint) {
                    return trim($alias[0]);
                }
            }

            if ($this->endsWith($statementClassName, $typeHint)) {
                return $statementClassName;
            }
        }

        return $typeHint;
    }

    private function endsWith(string $statementClassToCheck, string $typeHintToSearchFor) : bool
    {
        $typeHintToSearchFor = "\\" . $typeHintToSearchFor;
        $length = strlen($typeHintToSearchFor);
        if ($length == 0) {
            return true;
        }

        return (substr($statementClassToCheck, -$length) === $typeHintToSearchFor);
    }

    private function isPrimitiveType(string $type) : bool
    {
        return in_array($type, ["int", "float", "bool", "string"]);
    }

    private function hasGlobalNamespacePrefix(string $typeHint): bool
    {
        return $typeHint[0] === self::GLOBAL_NAMESPACE_PREFIX;
    }

    private function gatherGroupUseStatements(string $classContents): array
    {
        $foundUseStatements = [];
        preg_match_all(self::GROUP_USE_STATEMENTS_REGEX, $classContents, $foundGroupUseStatements);
        for ($useStatementIndex = 0; $useStatementIndex < count($foundGroupUseStatements[0]); $useStatementIndex++) {
            foreach (explode(",", $foundGroupUseStatements[2][$useStatementIndex]) as $singleUseStatement) {
                $foundUseStatements[] = trim($foundGroupUseStatements[1][$useStatementIndex]) . trim($singleUseStatement);
            }
        }

        return $foundUseStatements;
    }

    private function gatherSingleUseStatements(string $classContents): array
    {
        $foundUseStatements = [];
        preg_match_all(self::SINGLE_USE_STATEMENTS_REGEX, $classContents, $foundSingleUseStatements);
        for ($useStatementIndex = 0; $useStatementIndex < count($foundSingleUseStatements[0]); $useStatementIndex++) {
            $foundUseStatements[] = trim($foundSingleUseStatements[1][$useStatementIndex]);
        }

        return $foundUseStatements;
    }

    private function getDeclaringClassOrTrait(\ReflectionProperty $reflectionProperty): \ReflectionClass
    {
        foreach ($reflectionProperty->getDeclaringClass()->getTraits() as $trait) {
            foreach ($trait->getProperties() as $traitProperty) {
                if ($traitProperty->getName() === $reflectionProperty->getName()) {
                    return $this->getDeclaringClassOrTrait($traitProperty);
                }
            }
        }

        return $reflectionProperty->getDeclaringClass();
    }
}