<?php

declare(strict_types=1);

namespace JMS\Serializer\Metadata\Driver\DocBlockDriver;

use PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\ReturnTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\TypeAliasImportTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\TypeAliasTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\VarTagValueNode;
use PHPStan\PhpDocParser\Ast\Type\ArrayTypeNode;
use PHPStan\PhpDocParser\Ast\Type\GenericTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\ConstExprParser;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use PHPStan\PhpDocParser\Parser\TypeParser;

/**
 * @internal
 */
final class DocBlockTypeResolver
{
    /** resolve single use statements */
    private const SINGLE_USE_STATEMENTS_REGEX = '/^[^\S\r\n]*use[\s]*([^;\n]*)[\s]*;$/m';

    /** resolve group use statements */
    private const GROUP_USE_STATEMENTS_REGEX = '/^[^\S\r\n]*use[[\s]*([^;\n]*)[\s]*{([a-zA-Z0-9\s\n\r,]*)};$/m';
    private const GLOBAL_NAMESPACE_PREFIX = '\\';
    private const PHPSTAN_ARRAY_SHAPE = '/^([^\s]*) array{.*/m';
    private const PHPSTAN_ARRAY_TYPE = '/^([^\s]*) array<(.*)>/m';

    /**
     * @var PhpDocParser
     */
    protected $phpDocParser;

    /**
     * @var Lexer
     */
    protected $lexer;

    public function __construct()
    {
        $constExprParser = new ConstExprParser();
        $typeParser = new TypeParser($constExprParser);

        $this->phpDocParser = new PhpDocParser($typeParser, $constExprParser);
        $this->lexer = new Lexer();
    }

    /**
     * Attempts to retrieve additional type information from a PhpDoc block. Throws in case of ambiguous type
     * information and will return null if no helpful type information could be retrieved.
     *
     * @param \ReflectionMethod $reflectionMethod
     *
     * @return string|null
     */
    public function getMethodDocblockTypeHint(\ReflectionMethod $reflectionMethod): ?string
    {
        return $this->getDocBlocTypeHint($reflectionMethod);
    }

    /**
     * Attempts to retrieve additional type information from a PhpDoc block. Throws in case of ambiguous type
     * information and will return null if no helpful type information could be retrieved.
     *
     * @param \ReflectionProperty $reflectionProperty
     *
     * @return string|null
     */
    public function getPropertyDocblockTypeHint(\ReflectionProperty $reflectionProperty): ?string
    {
        return $this->getDocBlocTypeHint($reflectionProperty);
    }

    /**
     * @param \ReflectionMethod|\ReflectionProperty $reflector
     *
     * @return string|null
     */
    private function getDocBlocTypeHint($reflector): ?string
    {
        $types = $this->resolveTypeFromDocblock($reflector);

        // The PhpDoc does not contain additional type information.
        if (0 === count($types)) {
            return null;
        }

        // The PhpDoc contains multiple non-null types which produces ambiguity when deserializing.
        if (count($types) > 1) {
            return null;
        }

        // Only one type is left, so we only need to differentiate between arrays, generics and other types.
        $type = $types[0];

        // Simple array without concrete type: array
        if ($this->isSimpleType($type, 'array') || $this->isSimpleType($type, 'list')) {
            return null;
        }

        // Normal array syntax: Product[] | \Foo\Bar\Product[]
        if ($type instanceof ArrayTypeNode) {
            $resolvedType = $this->resolveTypeFromTypeNode($type->type, $reflector);

            return 'array<' . $resolvedType . '>';
        }

        // Generic array syntax: array<Product> | array<\Foo\Bar\Product> | array<int,Product>
        if ($type instanceof GenericTypeNode) {
            if ($this->isSimpleType($type->type, 'array')) {
                $resolvedTypes = array_map(fn (TypeNode $node) => $this->resolveTypeFromTypeNode($node, $reflector), $type->genericTypes);

                return 'array<' . implode(',', $resolvedTypes) . '>';
            }

            if ($this->isSimpleType($type->type, 'list')) {
                $resolvedTypes = array_map(fn (TypeNode $node) => $this->resolveTypeFromTypeNode($node, $reflector), $type->genericTypes);

                return 'array<int, ' . implode(',', $resolvedTypes) . '>';
            }

            throw new \InvalidArgumentException(sprintf("Can't use non-array generic type %s for collection in %s:%s", (string) $type->type, $reflector->getDeclaringClass()->getName(), $reflector->getName()));
        }

        // Primitives and class names: Collection | \Foo\Bar\Product | string
        return $this->resolveTypeFromTypeNode($type, $reflector);
    }

    /**
     * Returns a flat list of types of the given var tags. Union types are flattened as well.
     *
     * @param ReturnTagValueNode[]|VarTagValueNode[] $tagValues
     *
     * @return TypeNode[]
     */
    private function flattenTagValueTypes(array $tagValues): array
    {
        if ([] === $tagValues) {
            return [];
        }

        return array_merge(...array_map(static function ($node) {
            if ($node->type instanceof UnionTypeNode) {
                return $node->type->types;
            }

            return [$node->type];
        }, $tagValues));
    }

    /**
     * Returns a flat list of types of the given param tags. Union types are flattened as well.
     *
     * @param ParamTagValueNode[] $varTagValues
     *
     * @return TypeNode[]
     */
    private function flattenParamTagValueTypes(string $parameterName, array $varTagValues): array
    {
        if ([] === $varTagValues) {
            return [];
        }

        $parameterName = sprintf('$%s', $parameterName);
        $types = [];
        foreach ($varTagValues as $node) {
            if ($parameterName !== $node->parameterName) {
                continue;
            }

            $types[] = $node->type;
        }

        return $types;
    }

    /**
     * Filters the null type from the given types array. If no null type is found, the array is returned unchanged.
     *
     * @param TypeNode[] $types
     *
     * @return TypeNode[]
     */
    private function filterNullFromTypes(array $types): array
    {
        return array_values(array_filter(array_map(fn (TypeNode $node) => $this->isNullType($node) ? null : $node, $types)));
    }

    /**
     * Determines if the given type is a null type.
     *
     * @param TypeNode $typeNode
     *
     * @return bool
     */
    private function isNullType(TypeNode $typeNode): bool
    {
        return $this->isSimpleType($typeNode, 'null');
    }

    /**
     * Determines if the given node represents a simple type.
     *
     * @param TypeNode $typeNode
     * @param string $simpleType
     *
     * @return bool
     */
    private function isSimpleType(TypeNode $typeNode, string $simpleType): bool
    {
        return $typeNode instanceof IdentifierTypeNode && $typeNode->name === $simpleType;
    }

    /**
     * Attempts to resolve the fully qualified type from the given node. If the node is not suitable for type
     * retrieval, an exception is thrown.
     *
     * @param TypeNode $typeNode
     * @param \ReflectionMethod|\ReflectionProperty $reflector
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    private function resolveTypeFromTypeNode(TypeNode $typeNode, $reflector): string
    {
        if (!($typeNode instanceof IdentifierTypeNode)) {
            throw new \InvalidArgumentException(sprintf("Can't use unsupported type %s for collection in %s:%s", (string) $typeNode, $reflector->getDeclaringClass()->getName(), $reflector->getName()));
        }

        return $this->resolveType($typeNode->name, $reflector);
    }

    /**
     * @param \ReflectionMethod|\ReflectionProperty $reflector
     */
    private function expandClassNameUsingUseStatements(string $typeHint, \ReflectionClass $declaringClass, $reflector): string
    {
        $expandedClassName = $declaringClass->getNamespaceName() . '\\' . $typeHint;
        if ($this->isClassOrInterface($expandedClassName)) {
            return $expandedClassName;
        }

        $classContents = file_get_contents($declaringClass->getFileName());
        $foundUseStatements = $this->gatherGroupUseStatements($classContents);
        $foundUseStatements = array_merge($this->gatherSingleUseStatements($classContents), $foundUseStatements);

        foreach ($foundUseStatements as $statementClassName) {
            if ($alias = explode('as', $statementClassName)) {
                if (array_key_exists(1, $alias) && trim($alias[1]) === $typeHint) {
                    return trim($alias[0]);
                }
            }

            if ($this->endsWith($statementClassName, $typeHint)) {
                return $statementClassName;
            }
        }

        if ($declaringClass->getDocComment()) {
            $phpstanArrayType = $this->getPhpstanArrayType($declaringClass, $typeHint, $reflector);

            if ($phpstanArrayType) {
                return $phpstanArrayType;
            }
        }

        if ($this->isClassOrInterface($typeHint)) {
            return $typeHint;
        }

        throw new \InvalidArgumentException(sprintf("Can't use incorrect type %s for collection in %s:%s", $typeHint, $declaringClass->getName(), $reflector->getName()));
    }

    private function endsWith(string $statementClassToCheck, string $typeHintToSearchFor): bool
    {
        $typeHintToSearchFor = '\\' . $typeHintToSearchFor;

        return substr($statementClassToCheck, -strlen($typeHintToSearchFor)) === $typeHintToSearchFor;
    }

    private function isPrimitiveType(string $type): bool
    {
        return in_array($type, ['int', 'integer', 'float', 'bool', 'boolean', 'double', 'string']);
    }

    private function hasGlobalNamespacePrefix(string $typeHint): bool
    {
        return self::GLOBAL_NAMESPACE_PREFIX === $typeHint[0];
    }

    private function gatherGroupUseStatements(string $classContents): array
    {
        $foundUseStatements = [];
        preg_match_all(self::GROUP_USE_STATEMENTS_REGEX, $classContents, $foundGroupUseStatements);
        for ($useStatementIndex = 0; $useStatementIndex < count($foundGroupUseStatements[0]); $useStatementIndex++) {
            foreach (explode(',', $foundGroupUseStatements[2][$useStatementIndex]) as $singleUseStatement) {
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

    /**
     * @param \ReflectionMethod|\ReflectionProperty $reflector
     */
    private function getDeclaringClassOrTrait($reflector): \ReflectionClass
    {
        foreach ($reflector->getDeclaringClass()->getTraits() as $trait) {
            foreach ($trait->getProperties() as $traitProperty) {
                if ($traitProperty->getName() === $reflector->getName()) {
                    return $this->getDeclaringClassOrTrait($traitProperty);
                }
            }
        }

        return $reflector->getDeclaringClass();
    }

    /**
     * @param \ReflectionMethod|\ReflectionProperty $reflector
     */
    private function resolveType(string $typeHint, $reflector): string
    {
        if (!$this->hasGlobalNamespacePrefix($typeHint) && !$this->isPrimitiveType($typeHint)) {
            $typeHint = $this->expandClassNameUsingUseStatements($typeHint, $this->getDeclaringClassOrTrait($reflector), $reflector);
        }

        return ltrim($typeHint, '\\');
    }

    private function isClassOrInterface(string $typeHint): bool
    {
        return class_exists($typeHint) || interface_exists($typeHint);
    }

    /**
     * @param \ReflectionMethod|\ReflectionProperty $reflector
     */
    private function resolveTypeFromDocblock($reflector): array
    {
        $docComment = $reflector->getDocComment();
        if (!$docComment && PHP_VERSION_ID >= 80000 && $reflector instanceof \ReflectionProperty && $reflector->isPromoted()) {
            $constructor = $reflector->getDeclaringClass()->getConstructor();
            if (!$constructor) {
                return [];
            }

            $docComment = $constructor->getDocComment();

            if (!$docComment) {
                return [];
            }

            $tokens = $this->lexer->tokenize($docComment);
            $phpDocNode = $this->phpDocParser->parse(new TokenIterator($tokens));

            return $this->flattenParamTagValueTypes($reflector->getName(), $phpDocNode->getParamTagValues());
        }

        if (!$docComment) {
            return [];
        }

        // First we tokenize the PhpDoc comment and parse the tokens into a PhpDocNode.
        $tokens = $this->lexer->tokenize($docComment);
        $phpDocNode = $this->phpDocParser->parse(new TokenIterator($tokens));

        if ($reflector instanceof \ReflectionProperty) {
            // Then we retrieve a flattened list of annotated types excluding null.
            $tagValues = $phpDocNode->getVarTagValues();
        } else {
            // Then we retrieve a flattened list of annotated types including null.
            $tagValues = $phpDocNode->getReturnTagValues();
        }

        $types = $this->flattenTagValueTypes($tagValues);

        return $this->filterNullFromTypes($types);
    }

    /**
     * @param \ReflectionMethod|\ReflectionProperty $reflector
     */
    private function getPhpstanArrayType(\ReflectionClass $declaringClass, string $typeHint, $reflector): ?string
    {
        $tokens = $this->lexer->tokenize($declaringClass->getDocComment());
        $phpDocNode = $this->phpDocParser->parse(new TokenIterator($tokens));
        $self = $this;

        foreach ($phpDocNode->children as $node) {
            if (
                $node instanceof PhpDocTagNode
                && $node->value instanceof TypeAliasTagValueNode
                && $node->value->alias === $typeHint
            ) {
                $phpstanType = $node->value->__toString();
                preg_match(self::PHPSTAN_ARRAY_SHAPE, $phpstanType, $foundPhpstanArray);
                if (isset($foundPhpstanArray[0])) {
                    return 'array';
                }

                preg_match(self::PHPSTAN_ARRAY_TYPE, $phpstanType, $foundPhpstanArray);
                if (isset($foundPhpstanArray[0])) {
                    $types = explode(',', $foundPhpstanArray[2]);

                    return sprintf('array<%s>', implode(
                        ',',
                        array_map(static fn (string $type) => $self->resolveType(trim($type), $reflector), $types),
                    ));
                }
            } elseif ($node instanceof PhpDocTagNode && $node->value instanceof TypeAliasImportTagValueNode) {
                $importedFromFqn = $this->resolveType($node->value->importedFrom->name, $reflector);

                return $this->getPhpstanArrayType(
                    new \ReflectionClass($importedFromFqn),
                    $node->value->importedAlias,
                    $reflector,
                );
            }
        }

        return null;
    }
}
